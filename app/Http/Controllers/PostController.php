<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Notification;
use App\Post;
use App\Rating;
use App\Subscription;
use App\Thread;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function create(Request $request)
    {
        $post = new Post();
        $post->user_id = Auth::id();
        $post->body = $request->body;
        $post->thread_id = $request->thread_id;
        $post->save();

        $post['user'] = Auth::user();


        $thread = Thread::find($post->thread_id);
        $thread->replies++;
        $thread->save();

        $post['thread'] = $thread;

        $thread['latestPost'] = Post::with('user')->where('id', $post->id)->first();


        /*
         * Subscriptions
         */

        $subscriptions = Subscription::with('user')->where([
            ['thread_id', '=', $request->thread_id],
            ['user_id', '!=', Auth::id()]
        ])->get();

        if (count($subscriptions))
        {
            foreach ($subscriptions as $subscription)
            {
                $notification = new Notification();
                $notification->text = 'Thread "'.$thread->title.'" has a new reply.';
                $notification->user_id = $subscription->user->id;
                $notification->thread_id = $thread->id;
                $notification->save();

                $user = User::find($subscription->user->id);
                $user->has_new_notifications = 1;
                $user->save();

                broadcast(new NotificationSent($notification));
            }
        }



        return response()->json($thread, 200);
    }

}

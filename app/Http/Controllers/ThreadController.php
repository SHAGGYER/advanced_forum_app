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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class ThreadController extends Controller
{
    public $table = 'threads';

    public function subscribe(Request $request)
    {
        $subscription = new Subscription();
        $subscription->user_id = Auth::id();
        $subscription->thread_id = $request->thread_id;
        $subscription->save();

        return response()->json($subscription, 200);
    }

    public function unsubscribe(Request $request)
    {
        $subscription = Subscription::where('thread_id', $request->thread_id)->first();

        $subscription->delete();

        return response()->json(true, 200);
    }

    public function getThreadById($id)
    {
        $thread = Thread::with('user', 'forum')->where('id', $id)->first();

        $posts = Post::with('user')->where('thread_id', $thread->id)->paginate(10);

        $thread['posts'] = $posts;

        $sub = Subscription::where([
            ['user_id', '=', Auth::id()],
            ['thread_id', '=', $thread->id]
        ])->first();

        if (isset($sub->id))
        {
            $thread['subscription'] = $sub;
        }

        return response()->json($thread, 200);
    }


    public function search($searchQuery)
    {
        $threads = Thread::with('user')
            ->where('title', 'like', '%'.strtolower($searchQuery).'%')
            ->select('threads.*' )
            ->latest()
            ->paginate(10);


        foreach ($threads as $thread)
        {
            $post = Post::with('user')->where('thread_id', $thread->id)->latest()->first();
            $thread['latestPost'] = $post;
        }


        return response()->json($threads, 200);
    }

    public function create(Request $request)
    {
        $thread = new Thread();
        $thread->forum_id = $request->forum_id;
        $thread->title = $request->title;
        $thread->user_id = Auth::id();
        $thread->save();



        $post = new Post();
        $post->thread_id = $thread->id;
        $post->user_id = Auth::id();
        $post->body = $request->body;
        $post->save();

        /*
         * Subscription
         */

        $sub = new Subscription();
        $sub->thread_id = $thread->id;
        $sub->user_id = Auth::id();
        $sub->save();

        $thread['latestPost'] = Post::with('user')->where('id', $post->id)->first();

        return response()->json($thread, 200);
    }
}

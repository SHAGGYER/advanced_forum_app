<?php

namespace App\Http\Controllers;

use App\Category;
use App\Forum;
use App\Post;
use App\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function delete(Request $request)
    {
        $cat = Category::find($request->category_id);
        $cat->delete();

        return response()->json(true, 200);
    }

    public function create(Request $request)
    {
        $cat = new Category();
        $cat->title = $request->title;
        $cat->save();

        return response()->json($cat, 200);
    }

    public function getCats()
    {
        $cats = Category::with('fora')->get();

        for ($i = 0; $i < count($cats); $i++)
        {
            $currentCategory = $cats[$i];

            for ($j = 0; $j < count($currentCategory['fora']); $j++)
            {
                $currentForum = $currentCategory['fora'][$j];

                $currentForum['latest'] = Post::leftJoin('threads', 'posts.thread_id', '=', 'threads.id')
                    ->where('threads.forum_id', '=', $currentForum->id)
                    ->latest()
                    ->select('posts.*', 'threads.title as thread_title', 'threads.id as thread_id')
                    ->first();

                if (isset($currentForum['latest']->id))
                {
                    $currentForum['latest']['user'] = User::find($currentForum['latest']->user_id);

                }


                $currentForum['replies'] = Post::leftJoin('threads', 'posts.thread_id', '=', 'threads.id')
                    ->where('threads.forum_id', '=', $currentForum->id)
                    ->count();
            }

        }





        return response()->json($cats, 200);
    }


}

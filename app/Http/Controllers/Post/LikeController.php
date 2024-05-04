<?php

namespace App\Http\Controllers\Post;

use App\Events\PostLiked;
use App\Http\Controllers\Controller;
use App\Models\Post;

class LikeController extends Controller
{
    public function like(Post $Post)
    {
        $Post->likes()->attach(auth()->id());
        $Post->save();
        event(new PostLiked($Post, auth()->user()));
        return back();
    }

    public function dislike(Post $Post)
    {
        $Post->likes()->detach(auth()->id());
        $Post->save();
        return back();

    }

    public function toggle(Post $Post)
    {
        $Post->likes()->toggle(auth()->id());
        return back();
    }
}

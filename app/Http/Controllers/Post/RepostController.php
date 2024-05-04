<?php

namespace App\Http\Controllers\Post;

use App\Events\PostRePosted;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class RePostController extends Controller
{
    public function rePost( Request $request, Post $Post)
    {
        $rePoster = auth()->user();

        if(auth()->user()->Posts()->where('rePosting', $Post->id)->exists()){
            return back()->withErrors([
                'rePosting'=>'You have already rePosted this Post'
            ]);
        }
        $rePost = new Post([
            'message'=>$Post->message,
        ]);

        $rePost->forceFill([
            'user_id' => $rePoster->id,
            'rePosting'=>$Post->id,
        ]);

        $rePost->save();


        event(new PostRePosted($Post,$rePost));
        return back();
    }

    public function undo_rePost(Request $request, Post $Post)
    {
        $Post->rePosts()->where('user_id', auth()->id())->delete();
    }
}

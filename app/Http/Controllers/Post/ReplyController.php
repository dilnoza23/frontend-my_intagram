<?php

namespace App\Http\Controllers\Post;

use App\Events\PostRepliedTo;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Rules\PostExists;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function index()
    {

    }

    public function show()
    {

    }

    public function store(PostRequest $request, Post $Post)
    {
        $reply = new Post($request->only('message'));
        $reply['user_id'] = auth()->id();

       $reply->inReplyTo()->associate($Post);

       $reply->save();

        event(new PostRepliedTo($reply));

        return back();
    }
}

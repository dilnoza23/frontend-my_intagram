<?php

namespace App\Http\Controllers\Post;

use App\Events\PostCreated;
use App\Events\PostRepliedTo;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Rules\PostExists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user= auth()->user();
        $Posts = Post
            ::with(['user:id,name'])
            ->latest()
            ->isReply(false)
            ->paginate()
        ;

        return Inertia::render('Posts/Index',
            [
                'Posts'=>$Posts,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $validatedData = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $Post = new Post($validatedData);
        $request->user()->Posts()->save($Post);
        event(new PostCreated($Post));
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $Post)
    {
       $Post = Post::with([
           'user:id,name',
           'replies',
           'replies.user:id,name',
           'inReplyTo',
           'inReplyTo.user:id,name'])
           ->findOrFail($Post->id);
        return Inertia::render('Posts/Show',
        [
            'Post'=>$Post
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $Post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $Post):RedirectResponse
    {
        $this->authorize('update', $Post);

        $Post->update($request->validated());

        return redirect(route('posts.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $Post):RedirectResponse
    {
        $this->authorize('delete', $Post);
        $Post->rePosts()->delete();
        $Post->delete();
        return redirect(route('posts.index'));
    }
}

<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_reply_scope_filters_out_posts(): void
    {
        $user  = User::factory()->create();
        $Post = Post::factory()->create(['user_id'=> $user->id]);
        $reply = Post::factory()->create(['user_id'=>$user->id]);

        $Posts=Post::isReply(true)->get();

        $this->assertFalse($Posts->contains('replying_to', null));
    }
}

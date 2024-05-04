<?php

namespace Tests\Unit\User;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_posts_method_filters_out_replies(): void
    {
        $user  = User::factory()->create();
        $Post = Post::factory()->create(['user_id'=> $user->id]);
        $reply = Post::factory()->create(['user_id'=>$user->id]);

        $Post->replies()->save($reply);
        $posts = $user->posts()->get();

        $this->assertFalse($posts->contains('id', $reply->id ));

    }
    public function test_replies_method_filters_out_posts(): void
    {
        $user  = User::factory()->create();
        $Post = Post::factory()->create(['user_id'=> $user->id]);
        $reply = Post::factory()->create(['user_id'=>$user->id]);

        $Post->replies()->save($reply);
        $replies = $user->replies()->get();

        $this->assertTrue($replies->contains('id', $reply->id ));

    }


}

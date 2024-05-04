<?php

namespace Tests\Feature\Post;
use App\Events\PostLiked;
use App\Models\Post;
use App\Models\User;
use App\Notifications\LikePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery\Matcher\Not;
use Tests\TestCase;
class PostLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_like_Post():void
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        Notification::fake();

        $response = $this->likePost($liker, $Post);

        Notification::assertSentTo(
            $user,
            LikePost::class,
            function($notification, $channels) use($Post, $liker){
                return $notification->liker->id === $liker->id && $notification->Post->id===$Post->id;
        });

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));
        $this->assertTrue($Post->refresh()->likes()->count()===1);
    }

public function test_can_dislike_Post():void
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        $Post->likes()->attach($liker->id);

        $response = $this
            ->actingAs($liker)
            ->from(route('posts.index'))
            ->patch(route('posts.unlike', [
                'Post'=>$Post->id,
            ]));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));

        $this->assertTrue($Post->refresh()->likes()->count()===0);
    }

    public function test_can_toggle_like()
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        $response = $this
            ->actingAs($liker)
            ->from(route('posts.index'))
            ->patch(route('posts.toggle-like', [
                'Post'=>$Post->id,
            ]));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));

        $this->assertTrue($Post->refresh()->likes()->count()===1);

        $response = $this
            ->actingAs($liker)
            ->from(route('posts.index'))
            ->patch(route('posts.toggle-like', [
                'Post'=>$Post->id,
            ]));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));

        $this->assertTrue($Post->refresh()->likes()->count()===0);

    }



    private function likePost(User $liker, Post $Post): \Illuminate\Testing\TestResponse
    {
        return $this
            ->actingAs($liker)
            ->fromRoute('posts.index')
            ->patch(route('posts.like', [
                'Post'=>$Post->id,
            ]));

    }

    public function test_cannot_like_a_nonexistent_post():void
    {
        $user = User::factory()->create();
        Event::fake();

        $response = $this
            ->actingAs($user)
            ->from(route('posts.index'))
            ->patch(route('posts.like', [
                'Post'=>"non_existent_id",
            ]));


        $response
            ->assertNotFound();

        $this->assertTrue($user->refresh()->likedPosts()->count()===0);
        Event::assertNotDispatched(PostLiked::class);

    }

    public function test_Poster_receives_Post_liked_notification():void
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$user->id,
        ]);

       Notification::fake();
        $response = $this->likePost($liker, $Post);
       Notification::assertSentTo(
           $user,
           LikePost::class,
           function ($notification)use ($liker){
               $this->assertObjectHasProperty('Post', $notification);
               $this->assertEquals($liker->id, $notification->liker->id);
               return true;
           }
       );
    }

    public function test_Poster_doesnt_receive_notification_for_own_like(){
        $user = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        Notification::fake();
        $response = $this->likePost($user, $Post);
        Notification::assertNothingSent();

    }
}

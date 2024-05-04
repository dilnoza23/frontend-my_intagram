<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use App\Notifications\ReplyPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Tests\TestCase;

class PostReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_replied_to():void
    {
        $Poster = User::factory()->create();
        $replier = User::factory()->create();

        $Post = Post::factory()->create([
                'user_id'=>$Poster->id
            ]);
        Notification::fake();
        $response = $this->postReply($replier, route('posts.show', ['Post'=>$Post->id]), $Post->id);

        Notification::assertSentTo($Poster, ReplyPost::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.show', ['Post'=>$Post->id]));

        $this->assertDatabaseHas('Posts', [
            'replying_to'=>$Post->id,
        ]);

        $this->assertDatabaseCount('Posts',2);
        $this->assertTrue($Post->refresh()->replies()->count()===1);

    }

    public function test_cannot_reply_to_a_nonexistent_post():void
    {
        $user = User::factory()->create();

        $response = $this->postReply($user, route('posts.index'));

        $response
            ->assertNotFound();

        $this->assertDatabaseMissing('Posts', [

        ]);
    }

    public function test_replies_are_not_deleted_after_Post_deleted():void
    {
        $user = User::factory()->create();
        $Post = Post::factory()->create([
            'user_id'=>$user->id
        ]);

        Notification::fake();
        $response = $this
            ->actingAs($user)
            ->from(route('posts.show', ['Post'=>$Post->id]))
            ->post(route('Posts.reply',['Post'=>$Post->id]),[
                'message'=>'reply',
            ]);
        Notification::assertNothingSentTo($user);

        $response = $this
            ->actingAs($user)
            ->from(route('posts.show', ['Post'=>$Post->id]))
            ->delete(route('posts.destroy',['Post'=>$Post->id]));

        $this->assertDatabaseCount('Posts', 1);
    }

    public function test_notification_has_correct_data()
    {
        $Poster = User::factory()->create();
        $replier = User::factory()->create();

        $Post = Post::factory()->create([
            'user_id'=>$Poster->id
        ]);
        Notification::fake();
        $this->postReply($replier,route('posts.show', [$Post->id]), $Post->id);

        Notification::assertSentTo(
            $Poster,
            ReplyPost::class,
            function ($notification)use ($replier, $Post){
                $this->assertObjectHasProperty('reply', $notification);
                $this->assertEquals($replier->id, $notification->reply->user->id);
                return true;
            }
        );

    }

    public function test_user_doesnt_receive_notification_of_own_reply()
    {
        $user = User::factory()->create();
        $Post = Post::factory()->create([
            'user_id'=>$user->id
        ]);

        Notification::fake();
        $this->postReply($user, route('posts.show', [$Post->id]), $Post->id);

        Notification::assertNothingSent();


    }

    private function postReply(User $user,string $from,$id='non_existent')
    {
        return $this
            ->actingAs($user)
            ->from($from)
            ->post(route('Posts.reply', ['Post'=>$id]),[
                'message'=>'reply',
            ]);

    }
}

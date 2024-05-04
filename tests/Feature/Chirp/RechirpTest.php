<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use App\Notifications\RePostPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Matcher\Not;
use Notification;
use Tests\TestCase;

class RePostTest extends TestCase
{
    use RefreshDatabase;
    public function test_users_can_rePost_Posts():void
    {
        $Poster = User::factory()->create();
        $Post=Post::factory()->create([
            'user_id'=>$Poster->id,
        ]);

        $rePoster = User::factory()->create();

        $response = $this->rePost($rePoster, $Post);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('posts.index');

        $this->assertDatabaseHas('Posts', [
            'rePosting'=>$Post->id,
        ]);

    }

    public function test_user_can_rePost_Post_only_once():void
    {
        $Poster = User::factory()->create();
        $Post=Post::factory()->create([
            'user_id'=>$Poster->id,
        ]);

        $rePoster = User::factory()->create();

        $this
            ->rePost($rePoster, $Post)
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('posts.index');
        $response = $this->rePost($rePoster, $Post);

        $response
            ->assertSessionHasErrors('rePosting')
            ->assertRedirectToRoute('posts.index');


        $this->assertTrue(Post::where('rePosting', $Post->id)->count()===1);
        $this->assertTrue($Post->refresh()->rePosts()->count()===1);

    }

    public function test_notification_sent_to_Poster_after_rePost(){
        $Poster = User::factory()->create();
        $Post=Post::factory()->create([
            'user_id'=>$Poster->id,
        ]);

        $rePoster = User::factory()->create();
        Notification::fake();

        $response = $this->rePost($rePoster, $Post);


        Notification::assertSentTo(
            $Poster,
            RePostPost::class,
            function ($notification)use ($rePoster){
                $this->assertObjectHasProperty('Post', $notification);
                $this->assertEquals($rePoster->id, $notification->rePost->user->id);
                return true;
            }
        );
        Notification::assertCount(1);
    }


    public function test_user_doesnt_receive_notification_if_rePost_own_Post():void
    {
        $user = User::factory()->create();
        $Post=Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        Notification::fake();
        $response = $this->rePost($user, $Post);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('posts.index');
        Notification::assertNothingSentTo($user);

        $this->assertDatabaseHas('Posts', [
            'rePosting'=>$Post->id,
        ]);

    }

    private  function rePost(User $rePoster, Post $Post)
    {
        return $this
            ->actingAs($rePoster)
            ->fromRoute('posts.index')
            ->post(route('posts.rePost', ['Post'=>$Post->id]));

    }



}

<?php

namespace Tests\Feature\Post;
use App\Models\User;
use App\Notifications\NewPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Tests\TestCase;
class PostCreateTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_create_Post()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->attach($follower);

        Notification::fake();
        $response = $this->postPost($user);

        Notification::assertSentTo($follower, NewPost::class);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));
        $this->assertEquals('test', $user->refresh()->Posts()->first()->message);
    }

    public function test_cannot_create_Post_with_blank_message()
    {
        $user = User::factory()->create();
        $response = $this->postPost($user, '');

        $response
            ->assertSessionHasErrors('message')
            ->assertRedirect(route('posts.index'));

        $this->assertFalse($user->refresh()->Posts()->exists());
    }



    public function test_notification_has_required_values()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->attach($follower);

        Notification::fake();
        $this->postPost($user);
        Notification::assertSentTo(
            $follower,
            NewPost::class,
            function ($notification){
                $this->assertObjectHasProperty('Post', $notification);
                $this->assertEquals('test', $notification->Post->message);
                return true;
            }
        );

    }
    private function postPost($user, $message = 'test')
    {
        return $this
            ->actingAs($user)
            ->from(route('posts.index'))
            ->post(route('posts.store'), [
                'message'=>$message,
            ]);
    }
}

<?php

namespace Tests\Feature\Post;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
class PostActionsTest extends TestCase
{
    use RefreshDatabase;
    public function test_Post_can_be_deleted():void
    {
        $user = User::factory()->create();
        $Post=Post::factory()->create([
            'user_id'=>$user->id,
        ]);

        $response = $this->deletePost($user, $Post);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));

        $this->assertDatabaseMissing(Post::class, [
            'id'=>$Post->id,
        ]);
    }

    private function deletePost(User $user, Post $Post): TestResponse
    {
        return $this
            ->actingAs($user)
            ->fromRoute('posts.index')
            ->delete(route('posts.destroy',[
                'Post'=>$Post->id,
            ]));
    }

    public function test_other_users_cant_delete_Post():void
    {
        $Poster = User::factory()->create();
        $Post = Post::factory()->create([
            'user_id'=>$Poster->id,
        ]);

        $otherUser = User::factory()->create();

        $response = $this->deletePost($otherUser, $Post);

        $response
            ->assertStatus(403);

        $this->assertTrue($Poster->refresh()->Posts()->count()===1);

    }

}

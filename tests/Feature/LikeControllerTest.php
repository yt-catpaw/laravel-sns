<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ログインユーザーは投稿にいいねできる()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->postJson("/posts/{$post->id}/like");

        $response->assertOk()
            ->assertJson([
                'liked' => true,
                'count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    #[Test]
    public function ログインユーザーは投稿のいいねを解除できる()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // 先にいいね
        $user->likedPosts()->attach($post->id);

        $response = $this->actingAs($user)->deleteJson("/posts/{$post->id}/like");

        $response->assertOk()
            ->assertJson([
                'liked' => false,
                'count' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    #[Test]
    public function ゲストは投稿にいいねできない()
    {
        $post = Post::factory()->create();

        $response = $this->postJson("/posts/{$post->id}/like");

        $response->assertUnauthorized(); // = 401
    }
}

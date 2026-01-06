<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentCountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function コメント総数を返す(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        Comment::factory()->count(3)->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/comments/count');

        $response->assertOk()
            ->assertJson([
                'count' => 3,
            ]);
    }

    #[Test]
    public function コメント数取得でエラー時は500を返す(): void
    {
        Schema::drop('comments');

        $response = $this->getJson('/api/comments/count');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Server error',
            ]);
    }
}

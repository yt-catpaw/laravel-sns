<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
   use RefreshDatabase;

    #[Test]
    public function ログインユーザーは投稿に返信できる()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('comments.store', $post), [
                'body' => 'テストコメントです',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body'    => 'テストコメントです',
        ]);
    }

    #[Test]
    public function ゲストユーザーは投稿に返信できない()
    {
        $post = Post::factory()->create();

        $this->post(route('comments.store', $post), [
            'body' => 'ゲストコメント',
        ])->assertRedirect(route('login'));
    }

    #[Test]
    public function 返信本文は必須である()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('comments.store', $post), [
                'body' => '',
            ])
            ->assertSessionHasErrors(['body']);

        $this->assertDatabaseCount('comments', 0);
    }

    #[Test]
    public function ログインユーザーは自分の返信を削除できる()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body'    => '削除対象コメント',
        ]);

        $this->actingAs($user)
            ->delete(route('comments.destroy', $comment))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    #[Test]
    public function ログインユーザーは他人の返信を削除できない()
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $post  = Post::factory()->create();

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $other->id,
            'body'    => '他人のコメント',
        ]);

        $this->actingAs($user)
            ->delete(route('comments.destroy', $comment))
            ->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ゲストはタイムラインにアクセスできない()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    #[Test]
    public function ログインユーザーはタイムラインを表示でき投稿が見える()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create([
            'tweet' => 'hello timeline',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('hello timeline');
    }

    #[Test]
    public function ログインユーザーは画像なしで投稿できる()
    {
        $user = User::factory()->create();

        $response = $this->from('/')->actingAs($user)->post('/posts', [
            'tweet' => 'テスト投稿',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('status', '投稿しました。');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'tweet' => 'テスト投稿',
            'image_path' => null,
        ]);
    }

    #[Test]
    public function ログインユーザーは画像付きで投稿できる()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->from('/')->actingAs($user)->post('/posts', [
            'tweet' => '画像付き投稿',
            'image' => $file,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('status', '投稿しました。');

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertNotNull($post->image_path);
        $this->assertTrue(str_starts_with($post->image_path, 'public/posts/images/'));
        Storage::disk('local')->assertExists($post->image_path);
    }

    #[Test]
    public function 投稿内容が空ならバリデーションエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->from('/')->actingAs($user)->post('/posts', [
            'tweet' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('tweet');
    }

    #[Test]
    public function ゲストは投稿できない()
    {
        $response = $this->post('/posts', [
            'tweet' => 'guest post',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

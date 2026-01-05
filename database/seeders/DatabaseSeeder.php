<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostView;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = $this->createTestUser();
        $posts = $this->createTestPosts($user);
        $this->createTestComments($posts);
        $this->createTestViews($posts, $user);
    }

    private function createTestUser(): User
    {
        return User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'), 
        ]);
    }

    private function createTestPosts(User $user)
    {
        return Post::factory(5)->for($user)->create();
    }

    private function createTestComments(Collection $posts): void
    {
        foreach ($posts as $post) {
            // 親コメントを 3〜6 件
            $parents = Comment::factory(rand(3, 6))->create([
                'post_id' => $post->id,
            ]);

            foreach ($parents as $parent) {
                // 各親コメントに返信を 0〜3 件
                Comment::factory(rand(0, 3))->create([
                    'post_id'   => $post->id,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }

    private function createTestViews(Collection $posts, User $user): void
    {
        foreach ($posts as $post) {
            // 未ログイン閲覧を生成
            PostView::factory(rand(8, 16))->for($post)->create();
            // ログインユーザーによる閲覧を生成
            PostView::factory(rand(5, 12))->for($post)->for($user)->create();
        }
    }
}

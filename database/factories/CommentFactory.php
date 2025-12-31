<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;


    public function definition(): array
    {
        return [
            'post_id'   => Post::factory(),
            'user_id'   => User::factory(),
            'parent_id' => null,
            'body'      => fake('ja_JP')->text(200)
        ];
    }

    public function replyTo(Comment $parent): static
    {
        return $this->state(fn () => [
            'post_id'   => $parent->post_id,
            'parent_id' => $parent->id,
        ]);
    }
}

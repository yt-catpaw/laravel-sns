<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostView>
 */
class PostViewFactory extends Factory
{
    protected $model = PostView::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => $this->faker->boolean(60) ? User::factory() : null,
            'session_token' => $this->faker->uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

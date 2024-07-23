<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'thread_id' => ThreadFactory::new(),
            'author_id' => 0,
            'post_id' => null,
            'content' => fake()->sentence(),
        ];
    }
}

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
            'profiles_id' => 0,
            'threads_id' => ThreadFactory::new(),
            'content' => fake()->sentence(),
        ];
    }
}

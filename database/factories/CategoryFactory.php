<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'accepts_threads' => 1,
            'thread_count' => 0,
            'post_count' => 0,
            'is_private' => 0,
            'color_light_mode' => '#007BFF',
            'color_dark_mode' => '#007BFF',
        ];
    }
}

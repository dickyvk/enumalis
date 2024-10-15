<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Category;

class ThreadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profiles_id' => fake()->numberBetween(1, Profile::count()),
            'categories_id' => fake()->numberBetween(1, Category::count()),
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'pinned' => 0,
            'locked' => fake()->numberBetween($min = 0, $max = 1),
            'reply_count' => 0,
            'deleted_at' => null,
        ];
    }
}
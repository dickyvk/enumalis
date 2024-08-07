<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;
use App\Models\Thread;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profiles_id' => fake()->numberBetween(1, Profile::count()),
            'threads_id' => fake()->numberBetween(1, Thread::count()),
            'content' => fake()->sentence(),
            'deleted_at' => null,
        ];
    }
}
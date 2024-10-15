<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Thread;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profiles_id' => fake()->numberBetween(1, Profile::count()),
            'threads_id' => fake()->numberBetween(1, Thread::count()),
            'body' => fake()->sentence(),
            'deleted_at' => null,
        ];
    }
}
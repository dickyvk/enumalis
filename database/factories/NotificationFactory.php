<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profiles_id' => fake()->numberBetween(1, Profile::count()),
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => fake()->numberBetween($min = 0, $max = 1),
        ];
    }
}

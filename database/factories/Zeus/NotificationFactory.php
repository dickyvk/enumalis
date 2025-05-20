<?php

namespace Database\Factories\Zeus;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zeus\Profile;

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
            'profiles_id' => Profile::count() ? fake()->numberBetween(1, Profile::count()) : null,
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'read_at' => rand(0, 1)
                ? fake()->dateTimeBetween('-1 year', 'now')
                : null,
        ];
    }
}

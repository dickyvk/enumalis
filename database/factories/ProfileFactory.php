<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'users_id' => fake()->numberBetween(1, User::count()),
            'name' => fake()->name(),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury()->format('Y-m-d'),
            'gender' => fake()->numberBetween($min = 1, $max = 2),
            'blood_type' => fake()->numberBetween($min = 1, $max = 4),
            'identity_type' => fake()->numberBetween($min = 1, $max = 2),
            'identity_number' => fake()->numerify('3273############'),
        ];
    }
}

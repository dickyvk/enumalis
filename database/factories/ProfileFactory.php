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
            'users_id' => User::count() ? fake()->numberBetween(1, User::count()) : null,
            'name' => fake()->name(),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury()->format('Y-m-d'),
            'gender' => fake()->numberBetween(1, 2), // Assuming 1 = Male, 2 = Female
            'blood_type' => fake()->numberBetween(1, 4), // Assuming 1 = A, 2 = B, 3 = AB, 4 = O
            'identity_type' => fake()->numberBetween(1, 2), // Assuming 1 = KTP, 2 = Passport
            'identity_number' => fake()->numerify('3273############'),
        ];
    }
}

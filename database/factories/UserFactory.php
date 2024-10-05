<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => (string) Str::uuid(), // Use UUID instead of random string
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'type' => fake()->numberBetween(0, 2), // Assuming 0, 1, 2 are user types
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * States for different user types.
     */
    public function admin()
    {
        return $this->state([
            'type' => 2, // Assuming 2 is admin
        ]);
    }

    public function master()
    {
        return $this->state([
            'type' => 1, // Assuming 1 is master
        ]);
    }

    public function regular()
    {
        return $this->state([
            'type' => 0, // Assuming 0 is regular user
        ]);
    }
}

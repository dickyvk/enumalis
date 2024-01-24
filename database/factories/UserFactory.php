<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => Str::random(35),
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'type' => fake()->numberBetween($min = 0, $max = 2),
            'remember_token' => Str::random(10),
        ];
    }
}

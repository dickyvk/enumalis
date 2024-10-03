<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class RuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'users_id' => User::count() ? fake()->unique()->numberBetween(1, User::count()) : null,
            'terms' => fake()->boolean(),
            'policy' => fake()->boolean(),
            'pagination' => fake()->randomElement([10, 20, 50, 100]),
        ];
    }
}

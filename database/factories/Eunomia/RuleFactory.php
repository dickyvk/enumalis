<?php

namespace Database\Factories\Eunomia;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Eunomia\User;

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

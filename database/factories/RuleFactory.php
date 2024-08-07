<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class RuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'users_id' => fake()->unique()->numberBetween(1, User::count()),
            'terms' => fake()->numberBetween($min = 0, $max = 1),
            'policy' => fake()->numberBetween($min = 0, $max = 1),
            'pagination' => fake()->randomElement([10,20,50,100]),
        ];
    }
}

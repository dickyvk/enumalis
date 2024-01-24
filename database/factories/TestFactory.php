<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'a' => Str::random(10),
            'b' => Str::random(10),
        ];
    }
}

<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pheme\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_private' => $this->faker->boolean,
        ];
    }
}

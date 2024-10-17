<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pheme\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}

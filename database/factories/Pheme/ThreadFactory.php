<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pheme\Thread;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Category;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    public function definition()
    {
        return [
            'profiles_id' => Profile::factory(),
            'categories_id' => Category::factory(),
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'is_pinned' => $this->faker->boolean,
            'locked' => $this->faker->boolean,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Thread;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'categories_id' => CategoryFactory::new(),
            'pinned' => 0,
            'locked' => 0,
            'reply_count' => 0,
            'deleted_at' => null,
        ];
    }
}

<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pheme\Post;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Thread;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'profiles_id' => Profile::factory(),
            'threads_id' => Thread::factory(),
            'body' => $this->faker->paragraph,
        ];
    }
}

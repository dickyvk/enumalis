<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\PostHistory;
use App\Models\Pheme\Post;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostHistoryFactory extends Factory
{
    protected $model = PostHistory::class;

    public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'profiles_id' => Profile::factory(),
            'body' => $this->faker->paragraph,
        ];
    }
}

<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\Reaction;
use App\Models\Pheme\Post;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['upvote', 'like', 'dislike']),
            'posts_id' => Post::factory(),
            'profiles_id' => Profile::factory(),
        ];
    }
}

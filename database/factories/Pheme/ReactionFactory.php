<?php

namespace Database\Factories\Pheme;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pheme\Reaction;
use App\Models\Zeus\Profile;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition()
    {
        return [
            'profiles_id' => Profile::factory(),
            'reactable_id' => $this->faker->numberBetween(1, 100), // Adjust this according to your existing data
            'reactable_type' => $this->faker->randomElement(['Thread', 'Post']),
            'reaction_type' => $this->faker->randomElement(['like', 'dislike']),
        ];
    }
}

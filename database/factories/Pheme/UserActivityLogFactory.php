<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\UserActivityLog;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityLogFactory extends Factory
{
    protected $model = UserActivityLog::class;

    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'activity_type' => $this->faker->randomElement(['post_created', 'thread_created', 'reaction_given']),
            'activityable_type' => $this->faker->randomElement(['Thread', 'Post']),
            'activityable_id' => $this->faker->numberBetween(1, 100), // Adjust based on your existing data
        ];
    }
}

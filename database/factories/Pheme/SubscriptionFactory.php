<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\Subscription;
use App\Models\Pheme\Thread;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'thread_id' => Thread::factory(),
            'profiles_id' => Profile::factory(),
        ];
    }
}

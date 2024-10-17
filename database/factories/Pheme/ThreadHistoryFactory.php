<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\ThreadHistory;
use App\Models\Pheme\Thread;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadHistoryFactory extends Factory
{
    protected $model = ThreadHistory::class;

    public function definition()
    {
        return [
            'threads_id' => Thread::factory(),
            'body' => $this->faker->paragraph,
            'edited_by' => Profile::factory(),
            'edited_at' => now(),
        ];
    }
}

<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\ThreadTag;
use App\Models\Pheme\Thread;
use App\Models\Pheme\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadTagFactory extends Factory
{
    protected $model = ThreadTag::class;

    public function definition()
    {
        return [
            'threads_id' => Thread::factory(),
            'tags_id' => Tag::factory(),
        ];
    }
}

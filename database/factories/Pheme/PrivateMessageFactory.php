<?php

namespace Database\Factories\Pheme;

use App\Models\Pheme\PrivateMessage;
use App\Models\Zeus\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrivateMessageFactory extends Factory
{
    protected $model = PrivateMessage::class;

    public function definition()
    {
        return [
            'from_profiles_id' => Profile::factory(),
            'to_profiles_id' => Profile::factory(),
            'body' => $this->faker->text(200),
        ];
    }
}

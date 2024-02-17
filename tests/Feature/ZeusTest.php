<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class ZeusTest extends TestCase
{
    public function test_add_new_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'name' => fake()->name(),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury()->format('Y-m-d'),
            'gender' => fake()->numberBetween($min = 1, $max = 2),
            'blood_type' => fake()->numberBetween($min = 1, $max = 4),
            'identity_type' => fake()->numberBetween($min = 1, $max = 2),
            'identity_number' => fake()->numerify('3273############'),
        ];
        $array = $this->json('post', 'zeus/profile/add', $payload, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'users_id',
                    'name',
                    'place_of_birth',
                    'date_of_birth',
                    'gender',
                    'blood_type',
                    'identity_type',
                    'identity_number',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $user->delete();
    }
}

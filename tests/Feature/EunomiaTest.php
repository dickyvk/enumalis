<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class EunomiaTest extends TestCase
{
    public function test_register_login_logout_successful()
    {
        //Register
        $payload = [
            'uid' => Str::random(35),
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];
        $this->json('post', 'eunomia/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);;

        //Login
        $payload = [
            'uid' => $payload['uid'],
        ];
        $this->json('POST', 'eunomia/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $user = User::where('uid', $payload['uid'])->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
        $this->json('get', 'eunomia', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'phone',
                'created_at',
                'updated_at',
            ]);
        $this->json('post', 'eunomia/logout', [], $headers)->assertStatus(204);
        $this->json('get', 'eunomia', [], $headers)->assertStatus(500);
    }
}

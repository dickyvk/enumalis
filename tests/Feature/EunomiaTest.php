<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rule;

class EunomiaTest extends TestCase
{
    public function test_user_register()
    {
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
            ]);

        $user = User::where('uid', $payload['uid'])->first();
        $user->delete();
    }
    public function test_user_login()
    {
        $user = User::factory()->create();

        $payload = [
            'uid' => $user->uid,
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

        $user->delete();
    }
    public function test_user_show()
    {
        $user = User::factory()->create();
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

        $user->delete();
    }
    public function test_user_update()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];
        $this->json('post', 'eunomia', $payload, $headers)->assertStatus(200);
        $user = User::where('id', $user->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]);
        }

        $user->delete();
    }
    public function test_user_rule()
    {
        $user = User::factory()->create();
        $rule = Rule::factory()->create(['users_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'terms' => fake()->numberBetween($min = 0, $max = 1),
            'policy' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $this->json('post', 'eunomia/rule', $payload, $headers)->assertStatus(200);
        $rule = Rule::where('users_id', $user->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($rule->$key, $payload[$key]);
        }

        $user->delete();
    }
    public function test_user_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('post', 'eunomia/logout', [], $headers)->assertStatus(204);
        $this->json('get', 'eunomia', [], $headers)->assertStatus(500);

        $user->delete();
    }

    public function test_master_successful()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $user->type = 1;
        $user->save();
        $this->assertEquals($user->type, 'master');

        $user->delete();
    }
}

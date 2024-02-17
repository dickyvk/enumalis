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
    public function test_register_new_user()
    {
        $payload = [
            'uid' => Str::random(35),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];
        $this->json('post', 'eunomia/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $user = User::where('uid', $payload['uid'])->first();
        $user->delete();
    }
    public function test_login_as_user()
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
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $user->delete();
    }
    public function test_show_user_detail()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'eunomia', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'email',
                'phone',
                'created_at',
                'updated_at',
            ]);

        $user->delete();
    }
    public function test_update_user_detail()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
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
    public function test_update_user_rule()
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
    public function test_logout_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('post', 'eunomia/logout', [], $headers)->assertStatus(204);
        $this->json('get', 'eunomia', [], $headers)->assertStatus(500);

        $user->delete();
    }

    public function test_register_new_master()
    {
        $user = User::factory()->create();

        $user->type = 1;
        $user->save();
        $user = User::where('id', $user->id)->first();
        $this->assertEquals($user->type, 'master');

        $user->delete();
    }
    public function test_master_show_all_user()
    {
        $user = User::factory()->create();
        $user->type = 1;
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'eunomia/all', [], $headers)
            ->assertStatus(200)
            ->assertJsonIsArray();

        $user->delete();
    }
    public function test_master_update_other_user()
    {
        $user = User::factory()->create();
        $master = User::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];
        $array = $this->json('post', 'eunomia/'.$user->id, $payload, $headers)->assertStatus(200);
        $user = User::where('id', $user->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]);
        }

        $user->delete();
        $master->delete();
    }
    public function test_master_delete_other_user()
    {
        $user = User::factory()->create();
        $master = User::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $array = $this->json('delete', 'eunomia/'.$user->id, [], $headers)->assertStatus(204);
        $array = $this->json('post', 'eunomia/'.$user->id, [], $headers)->assertStatus(404);

        $master->delete();
    }
}

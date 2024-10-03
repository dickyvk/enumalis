<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rule;

class EunomiaTest extends TestCase
{
    use RefreshDatabase; // Ensures the database is reset after each test

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

        $this->assertDatabaseHas('users', [
            'uid' => $payload['uid'],
            'email' => $payload['email'],
        ]);
    }

    public function test_login_as_user()
    {
        $user = User::factory()->regular()->create();

        $payload = [
            'uid' => $user->uid,
        ];

        $this->json('post', 'eunomia/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
            ]);
    }

    public function test_show_user_detail()
    {
        $user = User::factory()->regular()->create();
        $headers = $this->getAuthHeaders($user);

        $this->json('get', 'eunomia', [], $headers)
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
    }

    public function test_update_user_detail()
    {
        $user = User::factory()->regular()->create();
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];

        $this->json('post', 'eunomia', $payload, $headers)
            ->assertStatus(200);

        $user->refresh(); // Refresh the user model to get the updated values
        foreach ($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]);
        }
    }

    public function test_update_user_rule()
    {
        $user = User::factory()->regular()->create();
        $rule = Rule::factory()->create(['users_id' => $user->id]);
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'terms' => fake()->numberBetween(0, 1),
            'policy' => fake()->numberBetween(0, 1),
        ];

        $this->json('post', 'eunomia/rule', $payload, $headers)
            ->assertStatus(200);

        $rule->refresh(); // Refresh to get the updated rule values
        foreach ($payload as $key => $value) {
            $this->assertEquals($rule->$key, $payload[$key]);
        }
    }

    public function test_logout_user()
    {
        $user = User::factory()->regular()->create();
        $headers = $this->getAuthHeaders($user);

        $this->json('post', 'eunomia/logout', [], $headers)
            ->assertStatus(204);

        // Check if the user can no longer access their details
        $this->json('get', 'eunomia', [], $headers)
            ->assertStatus(401); // Expecting unauthorized since the token should be revoked
    }

    public function test_register_new_master()
    {
        $user = User::factory()->master()->create();
        $this->assertEquals($user->type, 'master');
    }

    public function test_master_show_all_user()
    {
        $master = User::factory()->master()->create();
        $headers = $this->getAuthHeaders($master);

        $this->json('get', 'eunomia/all', [], $headers)
            ->assertStatus(200)
            ->assertJsonIsArray();
    }

    public function test_master_update_other_user()
    {
        $user = User::factory()->regular()->create();
        $master = User::factory()->master()->create();
        $headers = $this->getAuthHeaders($master);

        $payload = [
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];

        $this->json('post', 'eunomia/'.$user->id, $payload, $headers)
            ->assertStatus(200);

        $user->refresh(); // Refresh to get the updated user values
        foreach ($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]);
        }
    }

    public function test_master_delete_other_user()
    {
        $user = User::factory()->regular()->create();
        $master = User::factory()->master()->create();
        $headers = $this->getAuthHeaders($master);

        $this->json('delete', 'eunomia/'.$user->id, [], $headers)
            ->assertStatus(204);

        // Check if the user has been deleted
        $this->json('post', 'eunomia/'.$user->id, [], $headers)
            ->assertStatus(404);
    }
}

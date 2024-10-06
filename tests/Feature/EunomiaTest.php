<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rule;

class EunomiaTest extends TestCase
{
    /**
     * Test the registration of a new user.
     * 
     * This will generate a random 'uid', 'email', and 'phone', then attempt to register the user.
     * After registration, it verifies that the user is present in the 'users' table.
     */
    public function test_register_new_user()
    {
        $payload = [
            'uid' => Str::uuid(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];

        $this->json('post', 'eunomia/register', $payload)
            ->assertStatus(201) // Expecting a successful registration with status 201
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Store created user for cleanup
        $this->createdUsers[] = User::where('uid', $payload['uid'])->first();
        
        $this->assertDatabaseHas('users', [
            'uid' => $payload['uid'],
            'email' => $payload['email'],
        ]);
    }

    /**
     * Test the login functionality of a regular user.
     * 
     * This checks if a user can successfully log in using their 'uid'.
     */
    public function test_login_as_user()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup

        $payload = [
            'uid' => $user->uid,
        ];

        $this->json('post', 'eunomia/login', $payload)
            ->assertStatus(200) // Expecting successful login with status 200
            ->assertJsonStructure([
                'message',
                'access_token',
            ]);
    }

    /**
     * Test showing the authenticated user's details.
     * 
     * This retrieves the user's details and verifies the correct response structure.
     */
    public function test_show_user_detail()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $headers = $this->getAuthHeaders($user);

        $this->json('get', 'eunomia', [], $headers)
            ->assertStatus(200) // Expecting a successful response with status 200
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

    /**
     * Test updating the authenticated user's details.
     * 
     * This will update the user's 'email' and 'phone' fields and verify that the changes are persisted.
     */
    public function test_update_user_detail()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];

        $this->json('post', 'eunomia', $payload, $headers)
            ->assertStatus(200); // Expecting a successful update with status 200

        $user->refresh(); // Refresh the user model to get the updated values
        foreach ($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]); // Verifying updated values
        }
    }

    /**
     * Test updating the authenticated user's rule (e.g., terms and policy).
     * 
     * Verifies that the 'terms' and 'policy' values are updated in the 'rules' table.
     */
    public function test_update_user_rule()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $rule = Rule::factory()->create(['users_id' => $user->id]);
        $this->createdRules[] = $rule; // Store created rule for cleanup
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'terms' => 1,
            'policy' => 1,
        ];

        $this->json('post', 'eunomia/rule', $payload, $headers)
            ->assertStatus(200); // Expecting a successful update with status 200

        $rule = Rule::where('users_id', $user->id)->first(); // Retrieve the updated rule
        foreach ($payload as $key => $value) {
            $this->assertEquals($rule->$key, $payload[$key]); // Verifying the updated rule values
        }
    }

    /**
     * Test logging out the authenticated user.
     * 
     * Ensures that after logging out, the user cannot access protected resources anymore.
     */
    public function test_logout_user()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $headers = $this->getAuthHeaders($user);

        $this->json('post', 'eunomia/logout', [], $headers)
            ->assertStatus(200); // Expecting successful logout with status 200

        // Retrieve the specific token used for the current session
        $token = $user->tokens()->where('name', 'auth_token')->first();

        // Assert that the specific personal access token is missing
        if ($token) {
            $this->assertDatabaseMissing('eunomia.personal_access_tokens', [
                'id' => $token->id, // Check the ID of the token
            ]);
        }
    }

    /**
     * Test registering a new master user.
     * 
     * Verifies that the user is registered with the 'master' type.
     */
    public function test_register_new_master()
    {
        $user = User::factory()->master()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $this->assertEquals($user->type, 'master'); // Ensure the type is 'master'
    }

    /**
     * Test the master user's ability to view all users.
     * 
     * This ensures the master user can retrieve a list of all registered users.
     */
    public function test_master_show_all_user()
    {
        $master = User::factory()->master()->create();
        $this->createdUsers[] = $master; // Store created user for cleanup
        $headers = $this->getAuthHeaders($master);

        $this->json('get', 'eunomia/users', [], $headers)
            ->assertStatus(200) // Expecting a successful response with status 200
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        // Array of user objects
                    ]
                ]
            ]);
    }

    /**
     * Test the master user's ability to update another user's details.
     * 
     * This will update the 'email' and 'phone' fields of another user.
     */
    public function test_master_update_other_user()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $master = User::factory()->master()->create();
        $this->createdUsers[] = $master; // Store created user for cleanup
        $headers = $this->getAuthHeaders($master);

        $payload = [
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];

        $this->json('post', 'eunomia/'.$user->id, $payload, $headers)
            ->assertStatus(200); // Expecting a successful update with status 200

        $user->refresh(); // Refresh to get the updated user values
        foreach ($payload as $key => $value) {
            $this->assertEquals($user->$key, $payload[$key]); // Verifying the updated values
        }
    }

    /**
     * Test the master user's ability to delete another user's account.
     * 
     * Verifies that the user is deleted and can no longer be accessed.
     */
    public function test_master_delete_other_user()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $master = User::factory()->master()->create();
        $this->createdUsers[] = $master; // Store created user for cleanup
        $headers = $this->getAuthHeaders($master);

        $this->json('delete', 'eunomia/'.$user->id, [], $headers)
            ->assertStatus(200); // Expecting a successful deletion with status 200

        // Check if the user has been deleted
        $this->json('post', 'eunomia/'.$user->id, [], $headers)
            ->assertStatus(404); // Expecting a 404 (Not Found) after deletion
    }
}

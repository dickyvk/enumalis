<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Notification;

class ZeusTest extends TestCase
{
    /**
     * Test adding a new profile for a user.
     *
     * This test simulates adding a new profile linked to a user.
     * It checks if the profile creation request returns a 201 status and verifies
     * the structure of the response JSON to match the expected fields.
     */
    public function test_add_new_profile()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'name' => fake()->name(),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury()->format('Y-m-d'),
            'gender' => fake()->numberBetween(1, 2),
            'blood_type' => fake()->numberBetween(1, 4),
            'identity_type' => fake()->numberBetween(1, 2),
            'identity_number' => fake()->numerify('3273############'),
        ];

        $this->json('post', 'zeus/profile', $payload, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    //Array of data
                ],
            ]);
    }

    /**
     * Test retrieving a user's profile.
     *
     * This test checks if a user can retrieve their profile successfully
     * and if the response contains a JSON array of profiles.
     */
    public function test_get_user_profile()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $this->createdProfiles[] = $profile; // Store created profile for cleanup
        $headers = $this->getAuthHeaders($user);

        $this->json('get', 'zeus/profile', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    //Array of data
                ],
            ]);
    }

    /**
     * Test updating a user's profile.
     *
     * This test simulates updating an existing profile and checks if the update request
     * returns a 200 status. It also verifies that the updated profile data matches
     * the payload.
     */
    public function test_update_user_profile()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $this->createdProfiles[] = $profile; // Store created profile for cleanup
        $headers = $this->getAuthHeaders($user);

        $payload = [
            'name' => fake()->name(),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury()->format('Y-m-d'),
            'gender' => fake()->numberBetween(1, 2),
            'blood_type' => fake()->numberBetween(1, 4),
            'identity_type' => fake()->numberBetween(1, 2),
            'identity_number' => fake()->numerify('3273############'),
        ];

        $this->json('post', 'zeus/profile/'.$profile->id, $payload, $headers)
            ->assertStatus(200);

        $profile->refresh();

        $accessorKeys = ['gender', 'blood_type', 'identity_type'];

        foreach ($payload as $key => $value) {
            if (in_array($key, $accessorKeys)) {
                $this->assertEquals($value, $profile->getAccessorId($key));
            } else {
                $this->assertEquals($profile->$key, $payload[$key]);
            }
        }
    }

    /**
     * Test deleting a user's profile.
     *
     * This test simulates deleting a profile and checks if the delete request
     * returns a 204 status. It then confirms that trying to access the deleted
     * profile returns a 404 status.
     */
    public function test_delete_user_profile()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $this->createdProfiles[] = $profile; // Store created profile for cleanup
        $headers = $this->getAuthHeaders($user);

        $this->json('delete', 'zeus/profile/'.$profile->id, [], $headers)
            ->assertStatus(200);

        $this->json('post', 'zeus/profile/'.$profile->id, [], $headers)
            ->assertStatus(404);
    }

    /**
     * Test sending a notification to a profile.
     *
     * This test simulates sending a notification to a specific profile by a master user.
     * It checks if the notification creation request returns a 201 status and verifies
     * the structure of the notification JSON response.
     */
    public function test_send_notification()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $this->createdProfiles[] = $profile; // Store created profile for cleanup
        $master = User::factory()->master()->create();
        $this->createdUsers[] = $master; // Store created user for cleanup
        $headers = $this->getAuthHeaders($master);

        $payload = [
            'profiles_id' => $profile->id,
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => fake()->numberBetween(0, 1),
        ];

        $this->json('post', 'zeus/notification/send', $payload, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    //Array of data
                ],
            ]);
    }

    /**
     * Test blasting a notification to multiple profiles.
     *
     * This test simulates a master user blasting a notification to all profiles.
     * It checks if the blast request returns a 201 status.
     */
    public function test_blast_notification()
    {
        // Create master user with type 1
        $master = User::factory()->master()->create();
        $this->createdUsers[] = $master; // Store created user for cleanup

        // Create multiple users and profiles
        $users = User::factory()->regular()->count(3)->create();
        $this->createdUsers = array_merge($this->createdUsers, $users->all()); // Store created user for cleanup
        $profiles = $users->map(function ($user) {
            $profile = Profile::factory()->create(['users_id' => $user->id]);
            $this->createdProfiles[] = $profile; // Store created profile for cleanup
            return $profile;
        });

        $headers = $this->getAuthHeaders($master);

        // Payload for the notification blast
        $payload = [
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => 0,
            'user_ids' => $users->pluck('id')->toArray() // Send notifications only to these users
        ];

        // Send blast notification
        $this->json('post', 'zeus/notification/blast', $payload, $headers)
            ->assertStatus(201);

        $this->refreshApplication(); // Reset application state after blasting

        // Check that notifications were created for all profiles
        foreach ($profiles as $profile) {
            $this->assertDatabaseHas('zeus.notifications', [
                'profiles_id' => $profile->id,
                'title' => $payload['title'],
                'body' => $payload['body'],
                'opened' => 0,
            ]);
        }
    }

    /**
     * Test retrieving a list of notifications for a profile.
     *
     * This test checks if a user can retrieve all notifications associated with their profile.
     * It verifies that the response contains a JSON array of notifications.
     */
    public function test_get_notification()
    {
        $user = User::factory()->regular()->create();
        $this->createdUsers[] = $user; // Store created user for cleanup
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $this->createdProfiles[] = $profile; // Store created profile for cleanup
        $notification = Notification::factory()->create(['profiles_id' => $profile->id]);
        $this->createdNotifications[] = $notification; // Store created notification for cleanup
        $headers = $this->getAuthHeaders($user);

        $this->json('get', 'zeus/notification', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    //Array of data
                ],
            ]);
    }
}

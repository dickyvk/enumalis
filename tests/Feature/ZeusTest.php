<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Notification;

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
        $array = $this->json('post', 'zeus/profile', $payload, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
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
            ]);

        $user->delete();
    }
    public function test_get_user_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'zeus/profile', [], $headers)
            ->assertStatus(200)
            ->assertJsonIsArray();

        $user->delete();
    }
    public function test_update_user_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
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
        $this->json('post', 'zeus/profile/'.$profile->id, $payload, $headers)->assertStatus(200);
        $profile = Profile::where('id', $profile->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($profile->$key, $payload[$key]);
        }

        $user->delete();
    }
    public function test_delete_user_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $array = $this->json('delete', 'zeus/profile/'.$profile->id, [], $headers)->assertStatus(204);
        $array = $this->json('post', 'zeus/profile/'.$profile->id, [], $headers)->assertStatus(404);

        $user->delete();
    }

    public function test_send_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $master = User::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'profiles_id' => $profile->id,
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $array = $this->json('post', 'zeus/notification/send', $payload, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'profiles_id',
                'title',
                'body',
                'opened',
                'created_at',
                'updated_at',
            ]);

        $user->delete();
        $master->delete();
    }
    public function test_blast_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $master = User::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $array = $this->json('post', 'zeus/notification/blast', $payload, $headers)
            ->assertStatus(201);

        $user->delete();
        $master->delete();
    }
    public function test_get_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $notification = Notification::factory()->create(['profiles_id' => $profile->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'zeus/notification', [], $headers)
            ->assertStatus(200)
            ->assertJsonIsArray();

        $user->delete();
        $notification->delete();
    }
    public function test_show_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $notification = Notification::factory()->create(['profiles_id' => $profile->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'zeus/notification/'.$notification->id, [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'profiles_id',
                'title',
                'body',
                'opened',
                'created_at',
                'updated_at',
            ]);

        $user->delete();
        $notification->delete();
    }
    public function test_update_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $notification = Notification::factory()->create(['profiles_id' => $profile->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'title' => fake()->words(3, true),
            'body' => fake()->sentence(),
            'opened' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $this->json('put', 'zeus/notification/'.$notification->id, $payload, $headers)->assertStatus(200);
        $notification = Notification::where('id', $notification->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($notification->$key, $payload[$key]);
        }

        $user->delete();
        $notification->delete();
    }
    public function test_delete_notification()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['users_id' => $user->id]);
        $notification = Notification::factory()->create(['profiles_id' => $profile->id]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $array = $this->json('delete', 'zeus/notification/'.$notification->id, [], $headers)->assertStatus(204);
        $array = $this->json('get', 'zeus/notification/'.$notification->id, [], $headers)->assertStatus(404);

        $user->delete();
    }
}

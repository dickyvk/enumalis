<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\Eunomia\User;
use App\Models\Pheme\Category;
use App\Models\Pheme\Thread;
use App\Models\Pheme\Post;

class PhemeTest extends TestCase
{
    public function test_get_category()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'pheme/category', [], $headers)->assertStatus(200);

        $user->delete();
    }
    public function test_show_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', 'pheme/category/'.$category->id, [], $headers)->assertStatus(200);

        $user->delete();
        $category->delete();
    }
    public function test_create_category()
    {
        $master = User::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'accepts_threads' => fake()->numberBetween($min = 0, $max = 1),
            'is_private' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $this->json('post', 'pheme/category', $payload, $headers)->assertStatus(201);

        $master->delete();
    }
    public function test_update_category()
    {
        $master = User::factory()->create();
        $category = Category::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'accepts_threads' => fake()->numberBetween($min = 0, $max = 1),
            'is_private' => fake()->numberBetween($min = 0, $max = 1),
        ];
        $this->json('post', 'pheme/category/'.$category->id, $payload, $headers)->assertStatus(200);
        $category = Category::where('id', $category->id)->first();
        foreach($payload as $key => $value) {
            $this->assertEquals($category->$key, $value);
        }

        $master->delete();
        $category->delete();
    }
    public function test_delete_category()
    {
        $master = User::factory()->create();
        $category = Category::factory()->create();
        $master->type = 1;
        $master->save();
        $token = $master->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $array = $this->json('delete', 'pheme/category/'.$category->id, [], $headers)->assertStatus(204);
        $array = $this->json('get', 'pheme/category/'.$category->id, [], $headers)->assertStatus(500);

        $master->delete();
    }
}
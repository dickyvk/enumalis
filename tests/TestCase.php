<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function tearDown(): void
    {
        parent::tearDown();
        // Any additional cleanup can be added here if necessary
    }

    protected function getAuthHeaders(User $user)
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }
}

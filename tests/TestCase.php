<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Eunomia\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $createdUsers = [];
    protected $createdRules = [];
    protected $createdProfiles = [];
    protected $createdNotifications = [];

    /**
     * Tear down the test environment.
     *
     * This method is called after each test to clean up any created rows
     */
    protected function tearDown(): void
    {
        foreach ($this->createdUsers as $user) {
            if ($user) {
                $user->delete();
            }
        }

        foreach ($this->createdRules as $rule) {
            if ($rule) {
                $rule->delete();
            }
        }
        
        foreach ($this->createdProfiles as $profile) {
            if ($profile) {
                $profile->delete();
            }
        }

        foreach ($this->createdNotifications as $notification) {
            if ($notification) {
                $notification->delete();
            }
        }

        parent::tearDown();
    }

    protected function getAuthHeaders(User $user)
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }
}

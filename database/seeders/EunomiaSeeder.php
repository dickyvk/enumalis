<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eunomia\User;
use App\Models\Eunomia\Rule;

class EunomiaSeeder extends Seeder
{
    private static $EUNOMIA_ROW = 10;

    public function run(): void
    {
        // Create users and link them to rules
        User::factory(self::$EUNOMIA_ROW)->create()->each(function ($user) {
            Rule::factory()->create(['users_id' => $user->id]);
        });
    }
}

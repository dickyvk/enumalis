<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rule;

class EunomiaSeeder extends Seeder
{
    private static $EUNOMIA_ROW = 10;

    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(self::$EUNOMIA_ROW)->create();
        Rule::factory(self::$EUNOMIA_ROW)->create();
    }
}

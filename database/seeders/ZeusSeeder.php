<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\Notification;

class ZeusSeeder extends Seeder
{
    private static $ZEUS_ROW = 20;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profile::factory(self::$ZEUS_ROW)->create();
        Notification::factory(self::$ZEUS_ROW)->create();
    }
}

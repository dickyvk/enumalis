<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;

class ZeusSeeder extends Seeder
{
    private static $PHEME_ROW = 20;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(self::$PHEME_ROW)->create();
        Thread::factory(self::$PHEME_ROW)->create();
        Post::factory(self::$PHEME_ROW)->create();
    }
}

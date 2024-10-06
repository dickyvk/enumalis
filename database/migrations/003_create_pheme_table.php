<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pheme';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the database if it doesn't exist
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->connection);

        // Check and create 'forum_categories' table
        if (!Schema::hasTable('forum_categories')) {
            Schema::create('forum_categories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('description')->nullable();
                $table->boolean('accepts_threads')->default(0);
                $table->integer('newest_thread_id')->unsigned()->nullable();
                $table->integer('latest_active_thread_id')->unsigned()->nullable();
                $table->integer('thread_count')->default(0);
                $table->integer('post_count')->default(0);
                $table->boolean('is_private')->default(0);
                $table->string('color_light_mode')->nullable();
                $table->string('color_dark_mode')->nullable();
                $table->timestamps();
            });
        }

        // Check and create 'forum_threads' table
        if (!Schema::hasTable('forum_threads')) {
            Schema::create('forum_threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
                $table->foreignId('categories_id')->constrained(table: 'pheme.forum_categories')->onDelete('cascade');
                $table->string('title');
                $table->boolean('pinned')->nullable()->default(0);
                $table->boolean('locked')->nullable()->default(0);
                $table->integer('first_post_id')->unsigned()->nullable();
                $table->integer('last_post_id')->unsigned()->nullable();
                $table->integer('reply_count')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Check and create 'forum_posts' table
        if (!Schema::hasTable('forum_posts')) {
            Schema::create('forum_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
                $table->foreignId('threads_id')->constrained(table: 'pheme.forum_threads')->onDelete('cascade');
                $table->text('content');
                $table->integer('sequence')->unsigned()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Check and create 'forum_categories_access' table
        if (!Schema::hasTable('forum_categories_access')) {
            Schema::create('forum_categories_access', function (Blueprint $table) {
                $table->foreignId('categories_id')->constrained(table: 'pheme.forum_categories')->onDelete('cascade');
                $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Check and create 'forum_threads_read' table
        if (!Schema::hasTable('forum_threads_read')) {
            Schema::create('forum_threads_read', function (Blueprint $table) {
                $table->foreignId('threads_id')->constrained(table: 'pheme.forum_threads')->onDelete('cascade');
                $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads_read');
        Schema::dropIfExists('forum_categories_access');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
    }
};

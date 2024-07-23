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
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->connection);
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->default(0);
            $table->string('title');
            $table->string('description')->nullable();
            $table->integer('weight')->default(0);
            $table->boolean('accepts_threads')->default(0);
            $table->integer('newest_thread_id')->unsigned()->nullable();
            $table->integer('latest_active_thread_id')->unsigned()->nullable();
            $table->integer('thread_count')->default(0);
            $table->integer('post_count')->default(0);
            $table->boolean('is_private')->default(0);
            $table->string('color_light_mode')->nullable();
            $table->string('color_dark_mode')->nullable();
            $table->timestamps();

            //$table->nestedSet();
            $table->dropColumn(['category_id', 'weight']);
        });
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
            $table->string('title');
            $table->boolean('pinned')->nullable()->default(0);
            $table->boolean('locked')->nullable()->default(0);
            $table->integer('first_post_id')->unsigned()->nullable();
            $table->integer('last_post_id')->unsigned()->nullable();
            $table->integer('reply_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
        });
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('thread_id')->unsigned();
            $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
            $table->text('content');
            $table->integer('post_id')->unsigned()->nullable();
            $table->integer('sequence')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('thread_id');
        });
        Schema::create('forum_threads_read', function (Blueprint $table) {
            $table->integer('thread_id')->unsigned();
            $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads_read');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
    }
};

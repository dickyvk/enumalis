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

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('accepts_threads')->default(0);
                $table->foreignId('newest_thread_id')->nullable()->constrained('threads')->nullOnDelete();
                $table->foreignId('latest_active_thread_id')->nullable()->constrained('threads')->nullOnDelete();
                $table->integer('thread_count')->default(0);
                $table->integer('post_count')->default(0);
                $table->boolean('is_private')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories_access')) {
            Schema::create('categories_access', function (Blueprint $table) {
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
                $table->foreignId('profiles_id')->constrained('zeus.profiles')->onDelete('cascade');
                $table->unique(['categories_id', 'profiles_id']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('threads')) {
            Schema::create('threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained('zeus.profiles')->onDelete('cascade');
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
                $table->string('title');
                $table->text('body');
                $table->boolean('pinned')->default(0);
                $table->boolean('locked')->default(0);
                $table->foreignId('first_post_id')->nullable()->constrained('posts')->nullOnDelete();
                $table->foreignId('last_post_id')->nullable()->constrained('posts')->nullOnDelete();
                $table->integer('reply_count')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('thread_histories')) {
            Schema::create('thread_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->constrained('threads')->onDelete('cascade');
                $table->text('body');
                $table->foreignId('edited_by')->constrained('zeus.profiles');
                $table->timestamp('edited_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('threads_read')) {
            Schema::create('threads_read', function (Blueprint $table) {
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->foreignId('profiles_id')->constrained('zeus.profiles')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('threads_tags')) {
            Schema::create('threads_tags', function (Blueprint $table) {
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->foreignId('tags_id')->constrained('tags')->onDelete('cascade');
                $table->primary(['threads_id', 'tags_id']);
                $table->index(['threads_id', 'tags_id']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained('zeus.profiles')->onDelete('cascade');
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('moderation_actions')) {
            Schema::create('moderation_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->nullable()->constrained('threads')->onDelete('cascade');
                $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('cascade');
                $table->foreignId('moderator_id')->constrained('zeus.profiles');
                $table->string('action'); // e.g., 'delete', 'lock', 'sticky'
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->text('body');
                $table->foreignId('profiles_id')->constrained('zeus.profiles')->onDelete('cascade');
                $table->foreignId('thread_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('post_histories')) {
            Schema::create('post_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
                $table->text('old_body');
                $table->text('new_body');
                $table->foreignId('edited_by')->constrained('zeus.profiles'); // User who edited
                $table->timestamp('edited_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('reactions')) {
            Schema::create('reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('zeus.profiles');
                $table->morphs('reactionable'); // For posts or threads
                $table->string('type'); // e.g., 'like', 'upvote'
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('zeus.profiles');
                $table->string('activity_type'); // e.g., 'post_created', 'thread_created', 'reaction_given'
                $table->morphs('activityable'); // Tracks which model (thread, post, etc.)
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('private_messages')) {
            Schema::create('private_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('zeus.profiles');
                $table->foreignId('receiver_id')->constrained('zeus.profiles');
                $table->text('message');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('post_histories');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('private_messages');
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('threads_read');
        Schema::dropIfExists('threads');
        Schema::dropIfExists('categories_access');
        Schema::dropIfExists('categories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'pheme';
    protected $databaseName;
    protected $dependablesNameZeus;

    public function __construct()
    {
        $this->databaseName = config('database.connections.' . $this->connection . '.database');
        $this->dependablesNameZeus = config('database.connections.zeus.database');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the database if it doesn't exist
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->databaseName);

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_private')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('categories_access')) {
            Schema::create('categories_access', function (Blueprint $table) {
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
                $table->foreignId('profiles_id')->constrained($this->dependablesNameZeus.'.profiles')->onDelete('cascade');
                $table->unique(['categories_id', 'profiles_id']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('threads')) {
            Schema::create('threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained($this->dependablesNameZeus.'.profiles')->onDelete('cascade');
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
                $table->string('title');
                $table->text('body');
                $table->boolean('is_pinned')->default(0);
                $table->boolean('locked')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('thread_histories')) {
            Schema::create('thread_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->text('body');
                $table->foreignId('edited_by')->constrained($this->dependablesNameZeus.'.profiles');
                $table->timestamp('edited_at')->useCurrent();
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
                $table->index(['threads_id', 'tags_id']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained($this->dependablesNameZeus.'.profiles')->onDelete('cascade');
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained($this->dependablesNameZeus.'.profiles')->onDelete('cascade');
                $table->foreignId('threads_id')->constrained('threads')->onDelete('cascade');
                $table->text('body');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('post_histories')) {
            Schema::create('post_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
                $table->text('old_body');
                $table->text('new_body');
                $table->foreignId('edited_by')->constrained($this->dependablesNameZeus.'.profiles'); // User who edited
                $table->timestamp('edited_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('reactions')) {
            Schema::create('reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained($this->dependablesNameZeus.'.profiles')->onDelete('cascade');
                $table->unsignedBigInteger('reactable_id'); // The ID of the related thread/post
                $table->string('reactable_type'); // Model type (Thread/Post)
                $table->string('reaction_type'); // Type of reaction (like, dislike, etc.)
                $table->index(['reactable_id', 'reactable_type']); // Indexes for polymorphic query efficiency
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('moderation_actions')) {
            Schema::create('moderation_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('threads_id')->nullable()->constrained('threads')->onDelete('cascade');
                $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('cascade');
                $table->foreignId('moderator_id')->constrained($this->dependablesNameZeus.'.profiles');
                $table->string('action'); // e.g., 'delete', 'lock', 'sticky'
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained($this->dependablesNameZeus.'.profiles');
                $table->string('activity_type'); // e.g., 'post_created', 'thread_created', 'reaction_given'
                $table->morphs('activityable'); // Tracks which model (thread, post, etc.)
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order of creation to avoid foreign key constraints
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('post_histories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('threads_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('thread_histories');
        Schema::dropIfExists('threads');
        Schema::dropIfExists('categories_access');
        Schema::dropIfExists('categories');

        // Optionally, drop the 'pheme' database itself if needed
        DB::connection('mysql')->statement('DROP DATABASE IF EXISTS '.$this->databaseName);
    }

};

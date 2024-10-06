<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'eunomia';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the database if it doesn't exist
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->connection);

        // Check and create 'failed_jobs' table
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Check and create 'password_reset_tokens' table
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Drop the 'personal_access_tokens' table if it exists before recreating it
        Schema::dropIfExists('personal_access_tokens');
        
        // Create 'personal_access_tokens' table
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Check and create 'users' table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('uid')->unique();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->tinyInteger('role')->default(0);
                $table->rememberToken();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Check and create 'rules' table
        if (!Schema::hasTable('rules')) {
            Schema::create('rules', function (Blueprint $table) {
                $table->foreignId('users_id')->unique()->constrained()->onDelete('cascade');
                $table->boolean('terms')->default(0);
                $table->boolean('policy')->default(0);
                $table->tinyInteger('pagination')->default(10);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropForeign(['users_id']); // Drop the foreign key constraint first
        });

        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('users');
    }
};

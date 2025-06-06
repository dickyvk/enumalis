<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'zeus';
    protected $databaseName;
    protected $dependablesNameEunomia;

    public function __construct()
    {
        $this->databaseName = config('database.connections.' . $this->connection . '.database');
        $this->dependablesNameEunomia = config('database.connections.eunomia.database');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the database if it doesn't exist
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->databaseName);

        // Check and create 'profiles' table
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('users_id')->constrained($this->dependablesNameEunomia.'.users')->onDelete('cascade');
                $table->string('name');
                $table->string('place_of_birth')->nullable();
                $table->date('date_of_birth');
                $table->tinyInteger('gender')->default(0);
                $table->tinyInteger('blood_type')->default(0);
                $table->tinyInteger('identity_type')->default(0);
                $table->string('identity_number', 16)->nullable();
                $table->timestamps();
            });
        }

        // Check and create 'notifications' table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profiles_id')->constrained($this->databaseName.'.profiles')->onDelete('cascade');
                $table->text('title')->nullable();
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('profiles');
    }
};

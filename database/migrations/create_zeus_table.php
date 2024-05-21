<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'zeus';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::connection('mysql')->statement('CREATE DATABASE IF NOT EXISTS '.$this->connection);
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained(table: 'eunomia.users')->onDelete('cascade');
            $table->string('name');
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth');
            $table->tinyInteger('gender')->default(0);
            $table->tinyInteger('blood_type')->default(0);
            $table->tinyInteger('identity_type')->default(0);
            $table->string('identity_number', 16)->nullable();
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profiles_id')->constrained(table: 'zeus.profiles')->onDelete('cascade');
            $table->text('title')->nullable();
            $table->text('body');
            $table->boolean('opened')->default(0);
            $table->timestamps();
        });
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

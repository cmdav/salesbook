<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->string('first_name', 55);
            $table->string('last_name', 55);
            $table->string('middle_name', 55)->nullable();
            $table->string('phone_number', 12);
            $table->integer('type_id')->default(1);
            $table->integer('organization_id');
            $table->date('dob', 10);
            $table->string('email', 55)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',60);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

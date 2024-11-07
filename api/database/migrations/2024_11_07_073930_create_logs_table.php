<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('route')->nullable();
            $table->string('event')->nullable();//eg.g read, create, delete, purchase
            $table->string('model_id')->nullable();//eg affected id
            $table->string('model')->nullable(); //model name e.g Current
            $table->string('activity')->nullable(); //This user created a currency
            $table->json('payload')->nullable(); // Stores request or action details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};

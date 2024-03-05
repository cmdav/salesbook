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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->string('organization_name', 55);
            $table->string('organization_url', 55)->nullable();
            $table->integer('organization_code')->unique()->index();
            $table->string('organization_logo', 100);
            $table->uuid('created_by', 32);
            $table->uuid('update_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

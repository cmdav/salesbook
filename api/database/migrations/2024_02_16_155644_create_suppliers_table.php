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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('bank_name', 55);
            $table->integer('account_number');
            $table->string('account_name', 55);
            $table->string('state', 100);
            $table->string('address', 255);
            $table->date('dob')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

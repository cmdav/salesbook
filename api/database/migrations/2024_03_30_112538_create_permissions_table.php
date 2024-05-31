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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('page_id'); 
            $table->uuid('role_id'); 
            $table->boolean('read'); 
            $table->boolean('write'); 
            $table->boolean('update'); 
            $table->boolean('del'); 
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('restrict'); 
            $table->foreign('role_id')->references('id')->on('job_roles')->onDelete('restrict'); 
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

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
        Schema::create('supplier_organizations', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('supplier_id', 32)->index();
            $table->uuid('organization_id', 32)->index();
            $table->integer('status')->default(0)->comment('0 pending, 1 active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('restrict'); 
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_organizations');
    }
};

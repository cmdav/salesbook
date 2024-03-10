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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('purchase_id');
            $table->uuid('currency'); 
            $table->uuid('store_owner'); 
            $table->integer('quantity_available')->default(0); 
            $table->integer('store_type')->default(0)->comment("0 supplier, 1 company"); 
            $table->integer('status')->default(0); 
            $table->uuid('organization_id', 32)->nullabe();
            $table->uuid('last_updated_by')->nullable(); 
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

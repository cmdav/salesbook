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
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_product_id'); 
            $table->uuid('store_id')->nullable();
            $table->integer('quantity_available')->default(0); 
            $table->uuid('last_updated_by')->nullable(); 
            $table->uuid('updated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

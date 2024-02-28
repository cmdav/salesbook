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
            $table->uuid('supplier_product_id');
            $table->uuid('currency'); 
            $table->integer('discount')->default(0); 
            $table->string('batch_no', 50); 
            $table->string('product_identifier', 50); 
            $table->integer('supplier_price');
            $table->date('expired_date')->nullable();
            $table->uuid('store_owner'); 
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

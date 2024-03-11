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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('product_type_id');
            $table->uuid('supplier_id')->nullable();
            $table->uuid('price_id');
            $table->uuid('currency_id');
            $table->integer('discount')->default(0); 
            $table->string('batch_no', 50); 
            $table->integer('quantity')->default(0); 
            $table->string('product_identifier', 50)->nullable(); 
            $table->date('expired_date')->nullable();
            $table->uuid('purchase_owner')->default(0)->comment("0 supplier, 1 company"); 
            $table->integer('status')->default(0); 
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
        Schema::dropIfExists('purchases');
    }
};
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
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('product_type_id'); 
            $table->uuid('customer_id')->nullable(); 
            $table->uuid('price_id'); 
            $table->string('batch_no')->nullable();
            $table->integer('price_sold_at');
            $table->integer('quantity');
            $table->integer('vat')->nullable()->comment('0->no, 1->yes');
            $table->string('payment_method')->default('cash');
            $table->string('transaction_id');
            //$table->uuid('sales_owner'); 
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
        Schema::dropIfExists('sales');
    }
};

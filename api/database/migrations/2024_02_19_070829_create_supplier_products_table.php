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
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('product_type_id'); 
            $table->uuid('supplier_id'); 
            $table->string('batch_no'); 
            // $table->integer('price')->nullable();
            // $table->string('product_name', 255)->nullable();
            // $table->string('product_image', 225)->nullable();
            // $table->text('product_description')->nullable(); 
            $table->uuid('created_by', 32)->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};

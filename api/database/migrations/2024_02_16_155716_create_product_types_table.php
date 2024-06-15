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
        Schema::create('product_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id'); 
            $table->string('product_type_name', 50);
            $table->string('product_type_image', 150)->nullable();
            $table->string('product_type_description');
            $table->string('type')->default(0)->comment('1=product 2 product_type');
            $table->uuid('organization_id', 32)->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->uuid('created_by', 32)->nullable();
            $table->uuid('updated_by', 32)->nullable();
            $table->timestamps();
        
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_name', 50);
            $table->text('product_description')->nullable();
            $table->text('product_image')->nullable();
            //$table->text('barcode')->nullable();
            $table->boolean('vat')->default(0)->nullable();
            // $table->uuid('measurement_id');
            $table->uuid('sub_category_id');
            $table->uuid('category_id');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();


            $table->foreign('sub_category_id')->references('id')->on('product_sub_categories')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

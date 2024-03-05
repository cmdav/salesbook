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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->string('product_name', 50);
            $table->text('product_description')->nullable(); 
            $table->string('product_image', 255)->nullable();
            $table->uuid('measurement_id');
            $table->uuid('sub_category_id');
            $table->uuid('category_id');
            $table->uuid('created_by');
            $table->uuid('update_by')->nullable();
            $table->timestamps();
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

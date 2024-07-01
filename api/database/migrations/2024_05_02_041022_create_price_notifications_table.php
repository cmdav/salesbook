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
        Schema::create('price_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_type_id'); 
            $table->uuid('supplier_id')->nullable();
            $table->integer('cost_price');
            $table->integer('branch_id');
            $table->integer('selling_price')->nullable();
            $table->boolean('status')->default(0)->comment('3 for active, 2 for rejected, 1 for pending');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

          
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('restrict'); 
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_notifications');
    }
};

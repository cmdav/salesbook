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
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('product_type_id'); 
            $table->uuid('supplier_id')->nullable();
            $table->integer('product_price')->nullable();
            $table->uuid('product_currency'); 
            $table->integer('discount')->default(0);
            $table->boolean('status')->default(0);
            $table->uuid('organization_id', 32)->nullable(); 
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
        Schema::dropIfExists('prices');
    }
};

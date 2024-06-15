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
        Schema::create('supply_to_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->uuid('organization_id');
            $table->uuid('supplier_product_type_id');
            $table->uuid('updated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict'); 
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('restrict'); 
            $table->foreign('supplier_product_type_id')->references('id')->on('supplier_products')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_to_companies');
    }
};

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
            $table->uuid('id',32)->primary();
            $table->uuid('supplier_id');
            $table->uuid('organization_id');
            $table->uuid('supplier_product_id');
            $table->uuid('updated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
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

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
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_type_id');
            $table->uuid('supplier_id')->nullable();
            $table->uuid('price_id')->nullable();
            $table->uuid('product_measurement_id')->nullable();
            $table->uuid('selling_unit_id')->nullable();
            $table->uuid('purchase_unit_id')->nullable();
            $table->integer('branch_id')->default(0);
            $table->integer('cost_price')->nullable();
            $table->integer('is_cost_price_est')->default(0);
            $table->integer('selling_price')->nullable();
            $table->integer('is_selling_price_est')->default(0);
            $table->string('batch_no')->nullable();
            $table->integer('new_cost_price')->nullable();
            $table->integer('new_selling_price')->nullable();
            $table->integer('is_new')->default(0);
            // $table->integer('auto_generated_selling_price')->nullable();
            $table->uuid('currency_id')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('status')->default(0);
            $table->uuid('organization_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            // $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            // $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('restrict');
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

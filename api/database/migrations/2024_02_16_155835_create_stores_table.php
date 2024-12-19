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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_type_id');
            $table->string('batch_no');

            // $table->uuid('store_owner');
            $table->integer('branch_id');
            // $table->uuid('selling_unit_id')->default(0);
            $table->uuid('purchase_unit_id')->default(0);
            $table->integer('capacity_qty_available')->default(0);
            $table->uuid('product_measurement_id')->nullable();
            // $table->integer('store_type')->default(0)->comment("0 supplier, 1 company");
            $table->integer('status')->default(1);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->integer('is_displayed')->default(1);
            $table->timestamps();

            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

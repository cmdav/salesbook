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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_type_id');
            $table->uuid('supplier_id')->nullable();
            $table->uuid('price_id')->nullable();
            $table->uuid('purchase_unit_id')->nullable();
            $table->uuid('selling_unit_id')->nullable();
            $table->integer('branch_id');
            $table->string('batch_no')->index();
            $table->integer('capacity_qty')->default(0);
            $table->string('product_identifier', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('status')->default(1);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->integer('is_actual')->default(0);
            $table->timestamps();

            // $table->foreign('price_id')->references('id')->on('prices')->onDelete('restrict');
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            // $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('restrict');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

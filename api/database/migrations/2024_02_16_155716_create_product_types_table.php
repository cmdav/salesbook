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
        Schema::create('product_types', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('product_type_name', 255);
            $table->string('product_type_image', 255)->nullable();
            $table->text('product_type_description');
            $table->string('barcode')->nullable();
            // $table->uuid('selling_unit_capacity_id')->nullable()->index();
            // $table->uuid('purchase_unit_id')->nullable()->index();
            // $table->uuid('selling_unit_id')->nullable()->index();
            $table->uuid('sub_category_id')->nullable()->index();
            $table->uuid('category_id')->nullable()->index();

            $table->boolean('vat')->default(0)->nullable();
            // $table->boolean('is_container_type')->default(1);
            // $table->string('type')->default(0)->comment('1=product 2 product_type');
            $table->uuid('organization_id', 32)->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->integer('is_capacity_quantity_est')->default(0);

            $table->uuid('created_by', 32)->nullable();
            $table->uuid('updated_by', 32)->nullable();
            $table->timestamps();

            $table->foreign('sub_category_id')->references('id')->on('product_sub_categories')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('restrict');
            // $table->foreign('purchase_unit_id')->references('id')->on('purchase_units')->onDelete('restrict');
            // $table->foreign('selling_unit_id')->references('id')->on('selling_units')->onDelete('restrict');
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

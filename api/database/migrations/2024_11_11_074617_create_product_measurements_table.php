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
        Schema::create('product_measurements', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->uuid('product_type_id')->index();
            $table->uuid('selling_unit_capacity_id')->nullable()->index();
            $table->uuid('purchasing_unit_id')->nullable()->index();
            $table->uuid('selling_unit_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_measurements');
    }
};

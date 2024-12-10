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
        Schema::create('purchase_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purchase_unit_name', 50)->unique();
            $table->uuid('measurement_group_id')->nullable();
            $table->uuid('parent_purchase_unit_id')->nullable()->index();
            $table->integer('unit')->default(1);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_units');
    }
};

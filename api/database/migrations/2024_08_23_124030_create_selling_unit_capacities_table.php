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
        Schema::create('selling_unit_capacities', function (Blueprint $table) {
            $table->id();
            $table->uuid('selling_unit_id')->index();
            $table->integer('selling_unit_capacity');
            $table->string('piece_name')->unique()->nullable();
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
        Schema::dropIfExists('selling_unit_capacities');
    }
};

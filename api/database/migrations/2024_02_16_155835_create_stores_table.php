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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('supplier_id', 32);
            $table->uuid('product_id', 32);
            $table->integer('discount');
            $table->string('batch_no', 32);
            $table->integer('supplier_price');
            $table->date('expired_date');
            $table->uuid('created_by', 32);

            $table->timestamps();
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

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
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('store_id',32);
            $table->uuid('organization_id',32);
            $table->uuid('customer_id',32);
            $table->integer('price');
            $table->integer('quantity');
            $table->uuid('sales_owner', 32);
            $table->uuid('created_by', 32);
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

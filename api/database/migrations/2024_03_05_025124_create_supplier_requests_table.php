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
        Schema::create('supplier_requests', function (Blueprint $table) {
            $table->uuid('id',32)->primary();
            $table->uuid('organization_id');
            $table->uuid('supplier_product_id');
            $table->string('batch_no')->nullable();
            $table->integer('quantity');
            $table->integer('status')->default(0)->comment('pending');
            $table->text('comment',15)->nullable();
            $table->date('expected_date_of_arrival')->nullable();
            $table->uuid('order_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_requests');
    }
};

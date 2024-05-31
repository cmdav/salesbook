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
        Schema::create('subscription_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id'); 
            $table->date('start_time');
            $table->date('end_time');
            $table->uuid('organization_id');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('restrict'); // Add the foreign key constraint
           $table->foreign('plan_id')->references('id')->on('subscriptions')->onDelete('restrict'); // Add the foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_statuses');
    }
};

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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 55)->nullable();
            $table->string('company_name', 55)->nullable();
            $table->string('contact_person', 55)->nullable();
            $table->string('address', 55)->nullable();
            $table->string('last_name', 55)->nullable();
            $table->string('middle_name', 55)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('email', 150);
           // $table->integer('type_id')->default(0)->comment('0 => customer, 1=>supplier, 2=>company 3=>system users');
            $table->integer('type_id')->default(0)->comment('0 =>others,1 individual, 2 for company');
            $table->uuid('created_by', 32)->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

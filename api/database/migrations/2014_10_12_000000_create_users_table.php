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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 55)->nullable();
            // $table->string('company_name', 55)->nullable();
            // $table->string('contact_person', 55)->nullable();
            // $table->string('company_address', 55)->nullable();
            $table->string('last_name', 55)->nullable();
            $table->string('middle_name', 55)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->integer('type_id')->default(0)->comment('0 =>sales-personnel,1 for sole_properietor, 2 for company', '3 supplier');
            $table->string('role_id', 36)->default(0);
            $table->uuid('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable()->default(null);
            $table->string('organization_code')->nullable();
            $table->string('active', 10)->default('active')->comment('active, suspended');
            $table->date('dob', 10)->nullable();
            $table->string('email', 55)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->uuid('token')->nullable();
            $table->string('password',60)->default('p');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('restrict'); 
            $table->foreign('branch_id')->references('id')->on('business_branches')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

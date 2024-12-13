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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('organization_name', 55)->nullable();
            // $table->string('organization_url', 55)->nullable();
            $table->integer('organization_code')->unique()->index();
            $table->integer('organization_type')->default(1)->comment("0 sole proprietor, 1 for business");
            $table->string('organization_logo', 255)->default('/uploads/logo.png');
            //$table->string('organization_email', 200)->nullable()
            //$table->string('company_name', 55)->nullable();
            $table->string('contact_person', 55)->nullable();
            $table->string('company_address', 55)->nullable();
            $table->string('company_phone_number', 55)->nullable();
            $table->string('company_email', 55)->nullable();

            $table->uuid('user_id');
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
        Schema::dropIfExists('organizations');
    }
};

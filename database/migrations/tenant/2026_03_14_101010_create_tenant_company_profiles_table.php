<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantCompanyProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('tenant_company_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('central_company_id')->nullable()->index();
            $table->string('tenant_id')->nullable()->index();
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenant_company_profiles');
    }
}
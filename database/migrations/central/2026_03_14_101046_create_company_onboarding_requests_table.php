<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyOnboardingRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('company_onboarding_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('requested_by')->constrained('enterprise_admins')->cascadeOnUpdate()->restrictOnDelete();
            $table->json('request_payload')->nullable();
            $table->enum('provision_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique('company_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_onboarding_requests');
    }
}
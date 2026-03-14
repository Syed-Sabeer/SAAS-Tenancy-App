<?php

namespace App\Jobs;

use App\Models\Central\Company;
use App\Models\Central\CompanyOnboardingRequest;
use App\Models\Central\ProvisioningLog;
use App\Models\Tenant\TenantCompanyProfile;
use App\Support\CompanyStatuses;
use App\Support\ProvisionLogStatuses;
use App\Support\ProvisionStatuses;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Central\Tenant;

class FinalizeTenantOnboardingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $companyId;
    protected $onboardingRequestId;
    protected $tenantId;

    public function __construct(int $companyId, int $onboardingRequestId, string $tenantId)
    {
        $this->companyId = $companyId;
        $this->onboardingRequestId = $onboardingRequestId;
        $this->tenantId = $tenantId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = Company::findOrFail($this->companyId);
        $onboardingRequest = CompanyOnboardingRequest::findOrFail($this->onboardingRequestId);
        $tenant = Tenant::findOrFail($this->tenantId);

        tenancy()->initialize($tenant);

        try {
            TenantCompanyProfile::updateOrCreate(
                ['tenant_id' => $this->tenantId],
                [
                    'central_company_id' => $company->id,
                    'company_name' => $company->company_name,
                    'legal_name' => $company->legal_name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'website' => $company->website,
                    'industry' => $company->industry,
                    'company_size' => $company->company_size,
                    'country' => $company->country,
                    'state' => $company->state,
                    'city' => $company->city,
                    'address_line1' => $company->address_line1,
                    'address_line2' => $company->address_line2,
                    'postal_code' => $company->postal_code,
                    'logo_path' => $company->logo_path,
                    'timezone' => env('APP_TIMEZONE', 'UTC'),
                    'currency' => env('APP_CURRENCY', 'USD'),
                ]
            );
        } finally {
            tenancy()->end();
        }

        DB::transaction(function () use ($company, $onboardingRequest) {
            $company->update([
                'status' => CompanyStatuses::ACTIVE,
                'activated_at' => now(),
            ]);

            $onboardingRequest->update([
                'provision_status' => ProvisionStatuses::COMPLETED,
                'completed_at' => now(),
                'error_message' => null,
            ]);
        });

        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $this->tenantId,
            'step' => 'provisioning.completed',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant onboarding finalized.',
            'context' => [
                'company_status' => CompanyStatuses::ACTIVE,
            ],
        ]);
    }
}

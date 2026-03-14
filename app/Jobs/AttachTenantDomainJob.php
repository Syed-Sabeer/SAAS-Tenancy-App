<?php

namespace App\Jobs;

use App\Models\Central\Company;
use App\Models\Central\ProvisioningLog;
use App\Support\ProvisionLogStatuses;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Database\Models\Domain;

class AttachTenantDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $onboardingRequestId;
    protected $tenantId;
    protected $domain;

    public function __construct(int $companyId, int $onboardingRequestId, string $tenantId, string $domain)
    {
        $this->companyId = $companyId;
        $this->onboardingRequestId = $onboardingRequestId;
        $this->tenantId = $tenantId;
        $this->domain = $domain;
    }

    public function handle()
    {
        Domain::firstOrCreate([
            'tenant_id' => $this->tenantId,
            'domain' => $this->domain,
        ]);

        $company = Company::findOrFail($this->companyId);

        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $this->tenantId,
            'step' => 'tenant.domain.attached',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant domain attached.',
            'context' => [
                'domain' => $this->domain,
            ],
        ]);
    }
}
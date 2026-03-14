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
use App\Models\Central\Tenant;

class RegisterTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $onboardingRequestId;
    protected $tenantId;
    protected $databaseName;

    public function __construct(int $companyId, int $onboardingRequestId, string $tenantId, string $databaseName)
    {
        $this->companyId = $companyId;
        $this->onboardingRequestId = $onboardingRequestId;
        $this->tenantId = $tenantId;
        $this->databaseName = $databaseName;
    }

    public function handle()
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            $tenant = Tenant::create([
                'id' => $this->tenantId,
            ]);
        }

        $tenant->setInternal('db_name', $this->databaseName);

        $company = Company::findOrFail($this->companyId);
        $company->update(['tenant_id' => $tenant->id]);

        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $tenant->id,
            'step' => 'tenant.registered',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant registered in tenancy registry.',
            'context' => [
                'tenant_id' => $tenant->id,
                'database' => $this->databaseName,
            ],
        ]);
    }
}
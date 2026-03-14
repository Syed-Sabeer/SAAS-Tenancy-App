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
use Illuminate\Support\Facades\Artisan;

class RunTenantMigrationsJob implements ShouldQueue
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
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenantId],
            '--path' => [database_path('migrations/tenant')],
            '--realpath' => true,
            '--force' => true,
        ]);

        $company = Company::findOrFail($this->companyId);
        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $this->tenantId,
            'step' => 'tenant.migrations.ran',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant migrations completed.',
            'context' => [
                'tenant_id' => $this->tenantId,
            ],
        ]);
    }
}

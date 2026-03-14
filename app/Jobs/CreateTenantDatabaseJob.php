<?php

namespace App\Jobs;

use App\Models\Central\Company;
use App\Models\Central\CompanyOnboardingRequest;
use App\Models\Central\ProvisioningLog;
use App\Support\ProvisionLogStatuses;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateTenantDatabaseJob implements ShouldQueue
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
    protected $databaseName;

    public function __construct(int $companyId, int $onboardingRequestId, string $tenantId, string $databaseName)
    {
        $this->companyId = $companyId;
        $this->onboardingRequestId = $onboardingRequestId;
        $this->tenantId = $tenantId;
        $this->databaseName = $databaseName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $connection = config('tenancy.database.central_connection', env('DB_CONNECTION', 'mysql'));

        $charset = env('TENANT_DB_CHARSET', 'utf8mb4');
        $collation = env('TENANT_DB_COLLATION', 'utf8mb4_unicode_ci');
        $databaseName = str_replace('`', '', $this->databaseName);

        DB::connection($connection)->statement(sprintf(
            "CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s",
            $databaseName,
            $charset,
            $collation
        ));

        $company = Company::findOrFail($this->companyId);
        $onboardingRequest = CompanyOnboardingRequest::findOrFail($this->onboardingRequestId);

        $requestPayload = $onboardingRequest->request_payload ?: [];
        $requestPayload['database'] = $databaseName;

        $onboardingRequest->update([
            'request_payload' => $requestPayload,
        ]);

        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $this->tenantId,
            'step' => 'tenant.database.created',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant database created or already existed.',
            'context' => ['database' => $databaseName],
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Models\Central\Company;
use App\Models\Central\CompanyOnboardingRequest;
use App\Models\Central\ProvisioningLog;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Support\ProvisionLogStatuses;
use App\Support\TenantUserRoles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Stancl\Tenancy\Database\Models\Tenant;

class CreateTenantAdminJob implements ShouldQueue
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
            $payload = $onboardingRequest->request_payload ?: [];

            $adminEmail = Arr::get($payload, 'admin_email');
            $adminName = Arr::get($payload, 'admin_name', 'Company Admin');
            $adminPassword = Arr::get($payload, 'admin_password', 'Password@123');

            if (!$adminEmail) {
                throw new RuntimeException('Onboarding request is missing admin_email.');
            }

            $user = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'password' => Hash::make($adminPassword),
                    'role' => TenantUserRoles::COMPANY_ADMIN,
                    'status' => 'active',
                ]
            );

            $role = Role::firstOrCreate([
                'name' => TenantUserRoles::COMPANY_ADMIN,
                'guard_name' => 'tenant',
            ]);

            $user->roles()->syncWithoutDetaching([$role->id]);
        } finally {
            tenancy()->end();
        }

        ProvisioningLog::create([
            'company_id' => $company->id,
            'tenant_id' => $this->tenantId,
            'step' => 'tenant.admin.created',
            'status' => ProvisionLogStatuses::SUCCESS,
            'message' => 'Tenant admin user created.',
            'context' => [
                'admin_email' => Arr::get($onboardingRequest->request_payload ?: [], 'admin_email'),
            ],
        ]);
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Central\Company;
use App\Models\Central\CompanyOnboardingRequest;
use App\Models\Central\ProvisioningLog;
use App\Models\Central\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

// Remove all old tenant registry rows and domains
$tenants = Tenant::all();
foreach ($tenants as $t) {
    Domain::where('tenant_id', $t->id)->delete();
    $t->delete();
}

// Reset company status back to draft
Company::where('subdomain', 'test')->update(['status' => 'draft', 'tenant_id' => null]);

// Reset onboarding request
$company = Company::where('subdomain', 'test')->first();
if ($company && $company->onboardingRequest) {
    $company->onboardingRequest->update([
        'provision_status' => 'pending',
        'tenant_id' => null,
        'error_message' => null,
        'started_at' => null,
        'completed_at' => null,
    ]);
}

// Delete stale provisioning logs
ProvisioningLog::truncate();

echo "Reset complete.\n";

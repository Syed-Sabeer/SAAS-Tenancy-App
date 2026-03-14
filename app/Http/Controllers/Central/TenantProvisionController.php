<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Services\TenantProvisioningService;
use Throwable;

class TenantProvisionController extends Controller
{
    use RespondsWithJson;

    /**
     * @var \App\Services\TenantProvisioningService
     */
    protected $tenantProvisioningService;

    public function __construct(TenantProvisioningService $tenantProvisioningService)
    {
        $this->tenantProvisioningService = $tenantProvisioningService;
    }

    public function store(Company $company)
    {
        try {
            $result = $this->tenantProvisioningService->provision($company);

            if (!$result['success']) {
                return $this->fail($result['message'], 409, null, $result['data'] ?? null);
            }

            return $this->success($result['message'], $result['data']);
        } catch (Throwable $exception) {
            return $this->fail('Provisioning failed.', 500, [
                'exception' => [$exception->getMessage()],
            ]);
        }
    }

    public function show(Company $company)
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $result = $this->tenantProvisioningService->getProvisionStatus($company);
        return $this->success($result['message'], $result['data']);
    }
}

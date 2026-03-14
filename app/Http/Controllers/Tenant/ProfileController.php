<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TenantProfileUpdateRequest;
use App\Models\Tenant\TenantCompanyProfile;

class ProfileController extends Controller
{
    use RespondsWithJson;

    public function show()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $profile = TenantCompanyProfile::query()->first();

        return $this->success('Tenant company profile fetched successfully.', [
            'profile' => $profile,
        ]);
    }

    public function update(TenantProfileUpdateRequest $request)
    {
        $tenantId = optional(tenant())->id;

        $profile = TenantCompanyProfile::updateOrCreate(
            ['tenant_id' => $tenantId],
            $request->validated() + ['tenant_id' => $tenantId]
        );

        return $this->success('Tenant company profile updated successfully.', [
            'profile' => $profile,
        ]);
    }
}

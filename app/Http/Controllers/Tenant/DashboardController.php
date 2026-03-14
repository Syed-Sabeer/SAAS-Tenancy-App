<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantCompanyProfile;
use App\Models\Tenant\User;

class DashboardController extends Controller
{
    use RespondsWithJson;

    public function index()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $currentUser = auth('tenant')->user();

        $summary = [
            'tenant_id' => optional(tenant())->id,
            'user' => [
                'id' => $currentUser->id,
                'name' => $currentUser->name,
                'email' => $currentUser->email,
                'role' => $currentUser->role,
            ],
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
            ],
            'company_profile' => TenantCompanyProfile::query()->first(),
        ];

        return $this->success('Tenant dashboard loaded successfully.', $summary);
    }
}

<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Support\CompanyStatuses;

class DashboardController extends Controller
{
    use RespondsWithJson;

    public function index()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', CompanyStatuses::ACTIVE)->count(),
            'provisioning_companies' => Company::where('status', CompanyStatuses::PROVISIONING)->count(),
            'suspended_companies' => Company::where('status', CompanyStatuses::SUSPENDED)->count(),
        ];

        return $this->success('Dashboard statistics fetched successfully.', $stats);
    }
}

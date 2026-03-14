<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CompanyStoreRequest;
use App\Http\Requests\Central\CompanyUpdateRequest;
use App\Models\Central\Company;
use App\Services\CompanyOnboardingService;
use App\Services\DomainGeneratorService;
use Illuminate\Support\Arr;

class CompanyController extends Controller
{
    use RespondsWithJson;

    /**
     * @var \App\Services\CompanyOnboardingService
     */
    protected $onboardingService;

    /**
     * @var \App\Services\DomainGeneratorService
     */
    protected $domainGeneratorService;

    public function __construct(CompanyOnboardingService $onboardingService, DomainGeneratorService $domainGeneratorService)
    {
        $this->onboardingService = $onboardingService;
        $this->domainGeneratorService = $domainGeneratorService;
    }

    public function index()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $companies = Company::query()
            ->with(['onboardedBy', 'onboardingRequest'])
            ->latest('id')
            ->paginate(20);

        return $this->success('Companies fetched successfully.', [
            'companies' => $companies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        return $this->success('Company create payload.', [
            'required_fields' => [
                'company_name',
                'company_code',
                'subdomain',
                'admin_name',
                'admin_email',
                'admin_password',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Central\CompanyStoreRequest  $request
     */
    public function store(CompanyStoreRequest $request)
    {
        $payload = $request->validated();
        $payload['onboarded_by'] = auth('enterprise')->id();
        $payload['subdomain'] = $this->domainGeneratorService->generateUniqueSubdomain($payload['subdomain']);

        $created = $this->onboardingService->createCompanyWithOnboarding($payload);

        return $this->success('Company created successfully.', [
            'company' => $created['company']->load(['onboardedBy', 'onboardingRequest']),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Central\Company  $company
     */
    public function show(Company $company)
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $company->load(['onboardedBy', 'onboardingRequest', 'provisioningLogs', 'subscriptions.plan']);

        return $this->success('Company details fetched successfully.', [
            'company' => $company,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Central\Company  $company
     */
    public function edit(Company $company)
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $company->load(['onboardingRequest']);

        return $this->success('Company edit payload fetched successfully.', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Central\CompanyUpdateRequest  $request
     * @param  \App\Models\Central\Company  $company
     */
    public function update(CompanyUpdateRequest $request, Company $company)
    {
        $payload = Arr::except($request->validated(), ['admin_name', 'admin_email', 'admin_password']);

        if (isset($payload['subdomain'])) {
            $payload['subdomain'] = $this->domainGeneratorService->sanitizeSubdomain($payload['subdomain']);
        }

        $company->update($payload);

        if ($company->onboardingRequest) {
            $requestPayload = $company->onboardingRequest->request_payload ?: [];

            if ($request->filled('admin_name')) {
                $requestPayload['admin_name'] = $request->input('admin_name');
            }
            if ($request->filled('admin_email')) {
                $requestPayload['admin_email'] = $request->input('admin_email');
            }
            if ($request->filled('admin_password')) {
                $requestPayload['admin_password'] = $request->input('admin_password');
            }

            $company->onboardingRequest->update([
                'request_payload' => $requestPayload,
            ]);
        }

        return $this->success('Company updated successfully.', [
            'company' => $company->fresh()->load(['onboardingRequest']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Central\Company  $company
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return $this->success('Company deleted successfully.');
    }
}

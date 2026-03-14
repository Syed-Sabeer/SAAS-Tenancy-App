<?php

namespace App\Services;

use App\Models\Central\Company;
use App\Models\Central\CompanyOnboardingRequest;
use App\Models\Central\ProvisioningLog;
use App\Support\CompanyStatuses;
use App\Support\ProvisionLogStatuses;
use App\Support\ProvisionStatuses;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyOnboardingService
{
	/**
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public function createCompanyWithOnboarding(array $data): array
	{
		return DB::transaction(function () use ($data) {
			$company = Company::create([
				'company_code' => Arr::get($data, 'company_code'),
				'company_name' => Arr::get($data, 'company_name'),
				'legal_name' => Arr::get($data, 'legal_name'),
				'email' => Arr::get($data, 'email'),
				'phone' => Arr::get($data, 'phone'),
				'website' => Arr::get($data, 'website'),
				'industry' => Arr::get($data, 'industry'),
				'company_size' => Arr::get($data, 'company_size'),
				'country' => Arr::get($data, 'country'),
				'state' => Arr::get($data, 'state'),
				'city' => Arr::get($data, 'city'),
				'address_line1' => Arr::get($data, 'address_line1'),
				'address_line2' => Arr::get($data, 'address_line2'),
				'postal_code' => Arr::get($data, 'postal_code'),
				'logo_path' => Arr::get($data, 'logo_path'),
				'subdomain' => Arr::get($data, 'subdomain'),
				'status' => CompanyStatuses::DRAFT,
				'onboarded_by' => Arr::get($data, 'onboarded_by'),
			]);

			$onboardingRequest = CompanyOnboardingRequest::create([
				'company_id' => $company->id,
				'requested_by' => Arr::get($data, 'onboarded_by'),
				'request_payload' => [
					'admin_name' => Arr::get($data, 'admin_name'),
					'admin_email' => Arr::get($data, 'admin_email'),
					'admin_password' => Arr::get($data, 'admin_password'),
				],
				'provision_status' => ProvisionStatuses::PENDING,
			]);

			ProvisioningLog::create([
				'company_id' => $company->id,
				'tenant_id' => null,
				'step' => 'onboarding.created',
				'status' => ProvisionLogStatuses::SUCCESS,
				'message' => 'Company onboarding request created.',
				'context' => [
					'company_id' => $company->id,
					'onboarding_request_id' => $onboardingRequest->id,
				],
			]);

			return [
				'company' => $company,
				'onboarding_request' => $onboardingRequest,
			];
		});
	}
}

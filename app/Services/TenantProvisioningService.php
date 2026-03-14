<?php

namespace App\Services;

use App\Jobs\AttachTenantDomainJob;
use App\Jobs\CreateTenantAdminJob;
use App\Jobs\CreateTenantDatabaseJob;
use App\Jobs\FinalizeTenantOnboardingJob;
use App\Jobs\RegisterTenantJob;
use App\Jobs\RunTenantMigrationsJob;
use App\Jobs\SeedTenantDatabaseJob;
use App\Models\Central\Company;
use App\Models\Central\ProvisioningLog;
use App\Support\CompanyStatuses;
use App\Support\ProvisionLogStatuses;
use App\Support\ProvisionStatuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class TenantProvisioningService
{
	/**
	 * @var \App\Services\DomainGeneratorService
	 */
	protected $domainGeneratorService;

	public function __construct(DomainGeneratorService $domainGeneratorService)
	{
		$this->domainGeneratorService = $domainGeneratorService;
	}

	/**
	 * @return array<string, mixed>
	 *
	 * @throws \Throwable
	 */
	public function provision(Company $company): array
	{
		$requestedCompany = $company;
		$company = null;
		$onboardingRequest = null;
		$alreadyActive = false;
		$alreadyProcessing = false;

		DB::transaction(function () use (&$company, &$onboardingRequest, &$alreadyActive, &$alreadyProcessing, $requestedCompany) {
			$company = Company::query()->with('onboardingRequest')->lockForUpdate()->findOrFail($requestedCompany->id);
			$onboardingRequest = $company->onboardingRequest;

			if (!$onboardingRequest) {
				throw new \RuntimeException('Onboarding request not found for company: ' . $company->id);
			}

			if ($company->status === CompanyStatuses::ACTIVE) {
				$alreadyActive = true;
				return;
			}

			if ($onboardingRequest->provision_status === ProvisionStatuses::PROCESSING) {
				$alreadyProcessing = true;
				return;
			}

			$tenantId = $company->tenant_id ?: (string) Str::uuid();

			$company->update([
				'status' => CompanyStatuses::PROVISIONING,
				'tenant_id' => $tenantId,
			]);

			$onboardingRequest->update([
				'tenant_id' => $tenantId,
				'provision_status' => ProvisionStatuses::PROCESSING,
				'started_at' => now(),
				'error_message' => null,
			]);

			$this->log($company, $tenantId, 'provisioning.started', ProvisionLogStatuses::SUCCESS, 'Provisioning flow started.');
		});

		if ($alreadyActive) {
			return [
				'success' => true,
				'message' => 'Company is already provisioned.',
				'data' => [
					'company_id' => $company->id,
					'tenant_id' => $company->tenant_id,
				],
			];
		}

		if ($alreadyProcessing) {
			return [
				'success' => false,
				'message' => 'Provisioning is already in progress for this company.',
				'data' => [
					'company_id' => $company->id,
					'tenant_id' => $company->tenant_id,
				],
			];
		}

		$tenantId = $company->tenant_id ?: (string) Str::uuid();
		$databaseName = $this->domainGeneratorService->buildTenantDatabaseName($company->subdomain);
		$tenantDomain = $this->domainGeneratorService->buildTenantDomain($company->subdomain);

		try {
			CreateTenantDatabaseJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId, $databaseName);
			RegisterTenantJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId, $databaseName);
			AttachTenantDomainJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId, $tenantDomain);
			RunTenantMigrationsJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId);
			SeedTenantDatabaseJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId);
			CreateTenantAdminJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId);
			FinalizeTenantOnboardingJob::dispatchSync($company->id, $onboardingRequest->id, $tenantId);

			return [
				'success' => true,
				'message' => 'Tenant provisioned successfully.',
				'data' => [
					'company_id' => $company->id,
					'tenant_id' => $tenantId,
					'database' => $databaseName,
					'domain' => $tenantDomain,
				],
			];
		} catch (Throwable $exception) {
			DB::transaction(function () use ($company, $onboardingRequest, $tenantId, $exception) {
				$company->update([
					'status' => CompanyStatuses::DRAFT,
				]);

				$onboardingRequest->update([
					'provision_status' => ProvisionStatuses::FAILED,
					'error_message' => $exception->getMessage(),
					'completed_at' => now(),
				]);

				$this->log(
					$company,
					$tenantId,
					'provisioning.failed',
					ProvisionLogStatuses::FAILED,
					'Provisioning failed.',
					[
						'exception' => $exception->getMessage(),
						'trace' => Str::limit($exception->getTraceAsString(), 4000, ''),
					]
				);
			});

			throw $exception;
		}
	}

	/**
	 * @param array<string, mixed>|null $context
	 */
	public function log(Company $company, ?string $tenantId, string $step, string $status, ?string $message = null, ?array $context = null): void
	{
		ProvisioningLog::create([
			'company_id' => $company->id,
			'tenant_id' => $tenantId,
			'step' => $step,
			'status' => $status,
			'message' => $message,
			'context' => $context,
		]);
	}

	public function getProvisionStatus(Company $company): array
	{
		$company->load(['onboardingRequest', 'provisioningLogs']);

		return [
			'success' => true,
			'message' => 'Provisioning status fetched successfully.',
			'data' => [
				'company_id' => $company->id,
				'tenant_id' => $company->tenant_id,
				'company_status' => $company->status,
				'provision_status' => optional($company->onboardingRequest)->provision_status,
				'error_message' => optional($company->onboardingRequest)->error_message,
				'logs' => $company->provisioningLogs,
			],
		];
	}
}

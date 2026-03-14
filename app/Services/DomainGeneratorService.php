<?php

namespace App\Services;

use App\Models\Central\Company;
use Illuminate\Support\Str;

class DomainGeneratorService
{
	public function sanitizeSubdomain(string $value): string
	{
		$normalized = (string) Str::of($value)
			->lower()
			->replaceMatches('/[^a-z0-9-]/', '-')
			->replaceMatches('/-+/', '-')
			->trim('-');

		if ($normalized === '') {
			return 'tenant';
		}

		return Str::limit($normalized, 50, '');
	}

	public function generateUniqueSubdomain(string $desired): string
	{
		$base = $this->sanitizeSubdomain($desired);

		if (!Company::where('subdomain', $base)->exists()) {
			return $base;
		}

		for ($i = 1; $i <= 9999; $i++) {
			$candidate = $base . '-' . $i;
			if (!Company::where('subdomain', $candidate)->exists()) {
				return $candidate;
			}
		}

		return $base . '-' . Str::lower(Str::random(6));
	}

	public function buildTenantDomain(string $subdomain): string
	{
		$baseDomain = env('TENANT_BASE_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST));
		return $this->sanitizeSubdomain($subdomain) . '.' . $baseDomain;
	}

	public function buildTenantDatabaseName(string $subdomain): string
	{
		$prefix = env('TENANT_DB_PREFIX', 'tenant_');
		return $prefix . $this->sanitizeSubdomain($subdomain);
	}
}

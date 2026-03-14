<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class InitializeTenantByDomain
{
    /**
     * @var \Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain
     */
    protected $initializeTenancy;

    /**
     * @var \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains
     */
    protected $preventCentralAccess;

    public function __construct(
        InitializeTenancyByDomainOrSubdomain $initializeTenancy,
        PreventAccessFromCentralDomains $preventCentralAccess
    ) {
        $this->initializeTenancy = $initializeTenancy;
        $this->preventCentralAccess = $preventCentralAccess;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $this->preventCentralAccess->handle($request, function ($request) use ($next) {
            return $this->initializeTenancy->handle($request, $next);
        });
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class InitializeTenantByDomain
{
    /**
    * @var \Stancl\Tenancy\Middleware\InitializeTenancyByDomain
     */
    protected $initializeTenancy;

    /**
     * @var \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains
     */
    protected $preventCentralAccess;

    public function __construct(
        InitializeTenancyByDomain $initializeTenancy,
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

<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if (function_exists('tenant') && tenant()) {
                return route('tenant.login');
            }

            if (Route::has('enterprise.login')) {
                return route('enterprise.login');
            }

            return '/login';
        }
    }
}

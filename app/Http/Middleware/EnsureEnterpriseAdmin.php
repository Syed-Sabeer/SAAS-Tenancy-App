<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEnterpriseAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('enterprise')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated enterprise admin.',
                ], 401);
            }

            return redirect()->route('enterprise.login');
        }

        $admin = Auth::guard('enterprise')->user();

        if ($admin->status !== 'active') {
            Auth::guard('enterprise')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enterprise admin account is inactive.',
                ], 403);
            }

            return redirect()->route('enterprise.login')->withErrors([
                'email' => 'Your enterprise admin account is inactive.',
            ]);
        }

        return $next($request);
    }
}

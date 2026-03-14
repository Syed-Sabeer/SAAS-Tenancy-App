<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\TenantLoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use RespondsWithJson;

    public function showLoginForm()
    {
        if (request()->expectsJson()) {
            return $this->success('Tenant login page.', [
                'guard' => 'tenant',
                'tenant_id' => optional(tenant())->id,
            ]);
        }

        return view('welcome');
    }

    public function login(TenantLoginRequest $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => 'active',
        ];

        if (!Auth::guard('tenant')->attempt($credentials, $request->boolean('remember'))) {
            return $this->fail('Invalid credentials.', 422, [
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('tenant')->user();
        $user->update(['last_login_at' => now()]);

        return $this->success('Tenant user logged in successfully.', [
            'redirect_to' => route('tenant.dashboard'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\EnterpriseLoginRequest;
use Illuminate\Support\Facades\Auth;

class EnterpriseLoginController extends Controller
{
    use RespondsWithJson;

    public function showLoginForm()
    {
        if (request()->expectsJson()) {
            return $this->success('Enterprise login page.', [
                'guard' => 'enterprise',
            ]);
        }

        return view('welcome');
    }

    public function login(EnterpriseLoginRequest $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => 'active',
        ];

        if (!Auth::guard('enterprise')->attempt($credentials, $request->boolean('remember'))) {
            return $this->fail('Invalid credentials.', 422, [
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $admin = Auth::guard('enterprise')->user();
        $admin->update(['last_login_at' => now()]);

        return $this->success('Enterprise admin logged in successfully.', [
            'redirect_to' => route('enterprise.dashboard'),
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }
}

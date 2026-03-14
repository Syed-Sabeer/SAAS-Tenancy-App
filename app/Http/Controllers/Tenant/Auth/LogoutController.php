<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    use RespondsWithJson;

    public function logout(Request $request)
    {
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success('Tenant user logged out successfully.', [
            'redirect_to' => '/login',
        ]);
    }
}

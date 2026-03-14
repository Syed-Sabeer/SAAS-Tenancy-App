<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnterpriseLogoutController extends Controller
{
    use RespondsWithJson;

    public function logout(Request $request)
    {
        Auth::guard('enterprise')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success('Enterprise admin logged out successfully.', [
            'redirect_to' => route('enterprise.login'),
        ]);
    }
}

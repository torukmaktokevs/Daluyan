<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        // Handle JSON/API logins gracefully
        if ($request->wantsJson()) {
            return response()->noContent();
        }

        $user = $request->user();

        if ($user && $user->is_admin) {
            // Force admins to the admin dashboard (ignore intended)
            return redirect()->route('admin.dashboard');
        }

        // Always send non-admins to the configured Fortify home (no "intended" bounce to /dashboard)
        return redirect()->to(config('fortify.home', '/tenant/browsing'));
    }
}

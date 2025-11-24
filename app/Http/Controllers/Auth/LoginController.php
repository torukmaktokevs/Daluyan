<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;


class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     */
    protected function redirectTo()
    {
        // Check if user has a tenant profile
        if (Auth::user()->tenant) {
            return '/tenant/browsing';
        }
        
        // Check if user is admin (you'll add this field later)
        if (Auth::user()->is_admin) {
            return '/admin/dashboard';
        }
        
        // Default fallback
        return '/dashboard';
    }
}
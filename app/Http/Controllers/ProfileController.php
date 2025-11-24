<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\ApartmentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $tenant = method_exists($user, 'tenant') ? $user->tenant : null;

        $currentLease = null;
        if ($tenant) {
            $currentLease = Lease::with('apartment')
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->first();
        }

        $isHostApproved = \App\Models\HostRequest::where('user_id', $user->id)
            ->where('status', \App\Models\HostRequest::STATUS_APPROVED)
            ->exists();

        $applications = ApartmentApplication::with(['apartment.files', 'apartment.host'])
            ->where('tenant_user_id', $user->id)
            ->latest()
            ->get();

        return view('profile.dashboard', [
            'user' => $user,
            'tenant' => $tenant,
            'currentLease' => $currentLease,
            'isHostApproved' => $isHostApproved,
            'applications' => $applications,
        ]);
    }
}

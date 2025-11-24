<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostRequest;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $recentHostRequests = HostRequest::with('user')
            ->where('status', HostRequest::STATUS_PENDING)
            ->latest()
            ->take(10)
            ->get();

        $hostRequestCounts = [
            'pending' => HostRequest::where('status', 'pending')->count(),
            'approved' => HostRequest::where('status', 'approved')->count(),
            'rejected' => HostRequest::where('status', 'rejected')->count(),
        ];

        // Top-level stats for dashboard cards
        $stats = [
            'pendingApprovals' => $hostRequestCounts['pending'],
            'newUsers7d'       => User::where('created_at', '>=', now()->subDays(7))->count(),
            'verifiedUsers'    => User::whereNotNull('email_verified_at')->count(),
            'properties'       => Apartment::count(),
        ];

        return view('admin.dashboard', compact('recentHostRequests', 'hostRequestCounts', 'stats'));
    }
}

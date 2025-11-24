<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Minimal counts placeholders; wire to real data later
        $stats = [
            'properties' => 0,
            'tenants' => 0,
            'applications_pending' => 0,
            'maintenance_open' => 0,
        ];

        return view('host.dashboard', compact('stats'));
    }
}

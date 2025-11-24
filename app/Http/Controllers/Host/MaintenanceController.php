<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $hostId = Auth::id();
        $all = MaintenanceRequest::query()
            ->with(['apartment','tenant'])
            ->where('host_user_id', $hostId)
            ->latest()
            ->get();

        $requests = [
            'open' => $all->where('status','open')->values(),
            'in_progress' => $all->where('status','in_progress')->values(),
            'completed' => $all->where('status','completed')->values(),
        ];
        $tab = $request->get('tab', 'open');
        return view('host.maintenance.index', compact('requests', 'tab'));
    }

    /**
     * Mark a maintenance request as resolved (completed).
     */
    public function resolve(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $hostId = Auth::id();

        // ensure ownership
        if ($maintenanceRequest->host_user_id !== $hostId) {
            abort(403);
        }

        if ($maintenanceRequest->status === 'completed') {
            return back()->with('info', 'Request already resolved.');
        }

        $maintenanceRequest->status = 'completed';
        $maintenanceRequest->save();

        return back()->with('success', 'Maintenance request marked as resolved.');
    }
}

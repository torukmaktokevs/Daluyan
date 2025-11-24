<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;
use App\Models\Apartment;
use App\Models\ApartmentApplication;

class MaintenanceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $requests = MaintenanceRequest::query()
            ->with('apartment')
            ->where('tenant_user_id', $userId)
            ->latest()
            ->get();

        // find approved application for this tenant (if any) and expose the apartment
        $approvedApp = ApartmentApplication::query()
            ->with('apartment')
            ->where('tenant_user_id', $userId)
            ->where('status', 'approved')
            ->first();

        $approvedApartment = $approvedApp?->apartment;

        return view('tenant.maintenance.index', compact('requests', 'approvedApartment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:191'],
            'description' => ['nullable','string','max:4000'],
            'priority' => ['nullable','string','max:50'],
        ]);

        $data['tenant_user_id'] = Auth::id();

        // If tenant has an approved apartment, bind the request to it automatically
        $approvedApp = ApartmentApplication::query()
            ->where('tenant_user_id', $data['tenant_user_id'])
            ->where('status', 'approved')
            ->first();

        if ($approvedApp && $approvedApp->apartment_id) {
            $data['apartment_id'] = $approvedApp->apartment_id;
            $apt = Apartment::find($approvedApp->apartment_id);
            $data['host_user_id'] = $apt?->host_user_id;
        }

        MaintenanceRequest::create($data);

        return redirect()->route('tenant.maintenance.index')->with('success','Maintenance request submitted.');
    }
}

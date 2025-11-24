<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\ApartmentApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $hostId = Auth::id();
        // Show tenants whose applications have been approved for this host
        $applications = ApartmentApplication::query()
            ->with(['tenant', 'apartment'])
            ->where('host_user_id', $hostId)
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('host.tenants.index', compact('applications'));
    }

    public function show(ApartmentApplication $application)
    {
        if ($application->host_user_id !== Auth::id() || $application->status !== 'approved') {
            abort(404);
        }
        $application->load(['tenant', 'apartment.files']);
        return view('host.tenants.show', [
            'application' => $application,
            'tenant' => $application->tenant,
            'apartment' => $application->apartment,
        ]);
    }

    public function setMoveInDate(Request $request, ApartmentApplication $application)
    {
        if ($application->host_user_id !== Auth::id() || $application->status !== 'approved') {
            return redirect()->route('host.tenants.show', $application)->with('error', 'Cannot set move-in date.');
        }

        $data = $request->validate([
            'movein_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        // If application has visit_date, ensure movein >= visit
        if ($application->visit_date) {
            $visit = new \DateTime($application->visit_date->format('Y-m-d'));
            $movein = new \DateTime($data['movein_date']);
            if ($movein < $visit) {
                return redirect()->route('host.tenants.show', $application)->with('error', 'Move-in date must be the same or after the visit date.');
            }
        }

        $application->update(['movein_date' => $data['movein_date']]);

        // mark apartment as unavailable (occupied)
        if ($application->apartment) {
            $application->apartment->update(['status' => 'unavailable']);
        }

        return redirect()->route('host.tenants.show', $application)->with('success', 'Move-in date set successfully.');
    }

    public function remove(ApartmentApplication $application)
    {
        if ($application->host_user_id !== Auth::id() || $application->status !== 'approved') {
            return redirect()->route('host.tenants.index')->with('error', 'Tenant cannot be removed.');
        }
        $application->update(['status' => 'terminated']);

        // Release apartment if no other approved applications exist
        $apartment = $application->apartment;
        if ($apartment) {
            $stillApproved = ApartmentApplication::where('apartment_id', $apartment->id)
                ->where('status', 'approved')
                ->exists();
            if (!$stillApproved) {
                $apartment->update(['status' => 'available']);
            }
        }
        return redirect()->route('host.tenants.index')->with('success', 'Tenant removed successfully.');
    }
}

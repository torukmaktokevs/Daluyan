<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\ApartmentApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending'); // pending|approved|declined

        $hostId = Auth::id();
        $base = ApartmentApplication::query()
            ->with(['tenant', 'apartment.files'])
            ->where('host_user_id', $hostId)
            ->latest();

        $applications = [
            'pending'  => (clone $base)->where('status', 'pending')->get(),
            'approved' => (clone $base)->where('status', 'approved')->get(),
            'declined' => (clone $base)->whereIn('status', ['declined', 'rejected', 'cancelled'])->get(),
        ];

        return view('host.applications.index', compact('applications', 'tab'));
    }

    public function approve(ApartmentApplication $application)
    {
        if ($application->host_user_id !== Auth::id()) {
            abort(403);
        }
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be approved.');
        }
        // Prevent approving if apartment already occupied
        $apartment = $application->apartment;
        if ($apartment && $apartment->status === 'unavailable') {
            return redirect()->back()->with('error', 'Apartment already occupied.');
        }

        $application->update(['status' => 'approved']);
        if ($apartment) {
            $apartment->update(['status' => 'unavailable']);
        }
        return redirect()->back()->with('success', 'Application approved successfully.');
    }

    public function decline(ApartmentApplication $application)
    {
        if ($application->host_user_id !== Auth::id()) {
            abort(403);
        }
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be declined.');
        }
        $application->update(['status' => 'declined']);
        return redirect()->back()->with('success', 'Application declined successfully.');
    }
}

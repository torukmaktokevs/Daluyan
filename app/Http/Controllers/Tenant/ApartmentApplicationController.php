<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ApartmentApplicationController extends Controller
{
    public function index()
    {
        $apps = ApartmentApplication::query()
            ->with(['apartment.files'])
            ->where('tenant_user_id', Auth::id())
            ->latest()
            ->get();

        // Group by status for simple tab UI
        $applications = [
            'pending'  => $apps->where('status', 'pending')->values(),
            'approved' => $apps->where('status', 'approved')->values(),
            'declined' => $apps->whereIn('status', ['declined','rejected','cancelled','terminated'])->values(),
        ];

        $tab = request('tab', 'pending');
        return view('tenant.applications.index', compact('applications','tab'));
    }
    public function create(Apartment $apartment)
    {
        $apartment->load('files', 'host');
        return view('tenant.apartments.apply', [
            'apartment' => $apartment,
        ]);
    }

    public function store(Request $request, Apartment $apartment)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            // Dates are optional, but if provided they must be realistic
            // - visit_date cannot be in the past
            // - movein_date cannot be in the past and must be after visit_date when both are present
            'visit_date' => ['nullable', 'date', 'after_or_equal:today'],
            'movein_date' => ['nullable', 'date', 'after_or_equal:today', 'after:visit_date'],
        ]);

        $nights = 0;
        if (!empty($data['visit_date']) && !empty($data['movein_date'])) {
            $nights = (new \DateTime($data['visit_date']))->diff(new \DateTime($data['movein_date']))->days;
        }
        $total = $nights > 0 ? ($nights * (float)$apartment->price) : null;

        ApartmentApplication::create([
            'apartment_id'   => $apartment->id,
            'host_user_id'   => $apartment->host_user_id,
            'tenant_user_id' => Auth::id(),
            'visit_date'     => $data['visit_date'] ?? null,
            'movein_date'    => $data['movein_date'] ?? null,
            'message'        => $data['message'],
            'status'         => 'pending',
            'total_price'    => $total,
        ]);

        return redirect()->route('tenant.apartments.show', $apartment)
            ->with('status', 'Application submitted! The host will review your request.');
    }

    public function cancel(ApartmentApplication $application)
    {
        if ($application->tenant_user_id !== Auth::id()) {
            abort(403);
        }
        if ($application->status !== 'pending') {
            return redirect()->route('tenant.applications.index', ['tab' => 'pending'])
                ->with('error', 'Only pending applications can be cancelled.');
        }

        $application->update(['status' => 'cancelled']);
        return redirect()->route('tenant.applications.index', ['tab' => 'pending'])
            ->with('success', 'Application cancelled.');
    }
}

<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Apartment;

class PaymentController extends Controller
{
    public function index()
    {
        // payments related to host's apartments
        $apartmentIds = Apartment::where('host_user_id', Auth::id())->pluck('id')->toArray();
        $payments = Payment::whereIn('apartment_id', $apartmentIds)->orderBy('created_at', 'desc')->get();
        return view('host.payments.index', compact('payments'));
    }

    public function create()
    {
        $properties = Apartment::where('host_user_id', Auth::id())->get();
        return view('host.payments.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'apartment_id' => 'required|integer',
            'tenant_user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);

        $apartment = Apartment::findOrFail($data['apartment_id']);
        if ($apartment->host_user_id !== Auth::id()) {
            abort(403);
        }

        $payment = Payment::create([
            'tenant_user_id' => $data['tenant_user_id'],
            'apartment_id' => $data['apartment_id'],
            'amount' => $data['amount'],
            'reference' => $data['reference'] ?? null,
            'method' => 'cash',
            'status' => 'completed',
        ]);

        return redirect()->route('host.payments.index')->with('success', 'Payment recorded.');
    }

    public function destroy(Payment $payment)
    {
        $apartment = Apartment::find($payment->apartment_id);
        if (!$apartment || $apartment->host_user_id !== Auth::id()) {
            abort(403);
        }
        $payment->delete();
        return redirect()->route('host.payments.index')->with('success', 'Payment removed.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\HostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HostRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:50'],
            'idUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'photoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $idPath = null;
        $photoPath = null;

        if ($request->hasFile('idUpload')) {
            $idPath = $request->file('idUpload')->store('host-ids', 'public');
        }
        if ($request->hasFile('photoUpload')) {
            $photoPath = $request->file('photoUpload')->store('host-photos', 'public');
        }

        HostRequest::create([
            'user_id' => Auth::id(),
            'full_name' => $validated['fullname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'id_path' => $idPath,
            'photo_path' => $photoPath,
            'status' => HostRequest::STATUS_PENDING,
        ]);

        return redirect()->route('tenant.browsing')
            ->with('status', 'Your host request has been submitted and is pending admin review.');
    }
}

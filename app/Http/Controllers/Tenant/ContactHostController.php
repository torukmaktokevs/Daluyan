<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactHostController extends Controller
{
    public function store(Request $request, Apartment $apartment)
    {
        $this->authorize('view', $apartment); // optional; ignore if no policy

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $user = Auth::user();

        ContactMessage::create([
            'apartment_id'   => $apartment->id,
            'host_user_id'   => $apartment->host_user_id,
            'sender_user_id' => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'message'        => $validated['message'],
        ]);

        // Optionally: dispatch notification/email here
        // Notification can be added later if mail is configured.

        return back()->with('message_sent', 'Your message has been sent to the host. They will contact you shortly.');
    }
}

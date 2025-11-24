<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMessage;
use App\Models\ApartmentApplication;
use Illuminate\Support\Facades\Auth;

class ApplicationMessageController extends Controller
{
    public function index($applicationId)
    {
        $application = ApartmentApplication::findOrFail($applicationId);

        // ensure the authenticated host owns this application (host_user_id)
        if (Auth::id() !== $application->host_user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $messages = ApplicationMessage::where('application_id', $application->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'message' => $m->message,
                    'attachment_url' => $m->attachment ? asset('storage/'.$m->attachment) : null,
                    'from_host' => ($m->from_user_id === Auth::id()),
                    'created_at' => $m->created_at->toDateTimeString(),
                ];
            });

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'application_id' => ['required','integer','exists:apartment_applications,id'],
            'message' => ['nullable','string','max:4000'],
            'tenant_user_id' => ['nullable','integer','exists:users,id'],
            'attachment' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:5120'],
        ]);

        $application = ApartmentApplication::findOrFail($data['application_id']);

        // Only host of the application or the tenant themselves should be allowed to send from host area
        // Here, assume host is sending from host area; ensure ownership
        if (Auth::id() !== $application->host_user_id) {
            return back()->with('error', 'Unauthorized');
        }

        $toUserId = $data['tenant_user_id'] ?? $application->tenant_user_id;

        // handle optional attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('application_messages', 'public');
        }

        $msg = ApplicationMessage::create([
            'application_id' => $application->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $toUserId,
            'message' => $data['message'],
            'attachment' => $attachmentPath,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $msg->id,
                'message' => $msg->message,
                'attachment_url' => $msg->attachment ? asset('storage/'.$msg->attachment) : null,
                'from_host' => true,
                'created_at' => $msg->created_at->toDateTimeString(),
            ]);
        }

        return back()->with('success', 'Message sent');
    }
}

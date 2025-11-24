<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostRequest;
use Illuminate\Http\Request;

class HostRequestController extends Controller
{
    public function index()
    {
        // Show only pending requests on the Host Requests page
        $requests = HostRequest::with('user')
            ->where('status', HostRequest::STATUS_PENDING)
            ->latest()
            ->paginate(15);
        return view('admin.host-requests.index', compact('requests'));
    }

    public function approve(HostRequest $hostRequest)
    {
        $hostRequest->update(['status' => HostRequest::STATUS_APPROVED]);
        return back()->with('status', 'Host request approved.');
    }

    public function reject(HostRequest $hostRequest)
    {
        $hostRequest->update(['status' => HostRequest::STATUS_REJECTED]);
        return back()->with('status', 'Host request rejected.');
    }
}

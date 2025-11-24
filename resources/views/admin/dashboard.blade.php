<x-admin-layout>
    <div class="page-head">
        <h1>Dashboard</h1>
        <p class="muted">Overview of rentals, users, and activity.</p>
    </div>

    <div class="grid stats">
        <div class="card stat-card">
            <div>
                <h3 id="cardPending">{{ $stats['pendingApprovals'] ?? 0 }}</h3>
                <p class="muted">Pending Approvals</p>
            </div>
            <i class="fa fa-clock fa-2x accent"></i>
        </div>

        <div class="card stat-card">
            <div>
                <h3 id="cardNewUsers">{{ $stats['newUsers7d'] ?? 0 }}</h3>
                <p class="muted">New Users (7d)</p>
            </div>
            <i class="fa fa-user-plus fa-2x accent"></i>
        </div>

        <div class="card stat-card">
            <div>
                <h3 id="cardVerified">{{ $stats['verifiedUsers'] ?? 0 }}</h3>
                <p class="muted">Verified Users</p>
            </div>
            <i class="fa fa-user-check fa-2x accent"></i>
        </div>

        <div class="card stat-card">
            <div>
                <h3 id="cardProperties">{{ $stats['properties'] ?? 0 }}</h3>
                <p class="muted">Properties</p>
            </div>
            <i class="fa fa-building fa-2x accent"></i>
        </div>
    </div>


        <div class="card" style="grid-column: 1 / -1;">
            <h3>Host Requests</h3>
            <div class="muted" style="margin-bottom:10px;">
                Pending: <strong>{{ $hostRequestCounts['pending'] ?? 0 }}</strong> · Approved: <strong>{{ $hostRequestCounts['approved'] ?? 0 }}</strong> · Rejected: <strong>{{ $hostRequestCounts['rejected'] ?? 0 }}</strong>
            </div>
            <div class="table-responsive">
                <table class="min-w-full" style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align:left; font-size:12px; color:#64748b;">
                            <th style="padding:8px 6px; border-bottom:1px solid #e5e7eb;">Applicant</th>
                            <th style="padding:8px 6px; border-bottom:1px solid #e5e7eb;">Email</th>
                            <th style="padding:8px 6px; border-bottom:1px solid #e5e7eb;">Phone</th>
                            <th style="padding:8px 6px; border-bottom:1px solid #e5e7eb;">Status</th>
                            <th style="padding:8px 6px; border-bottom:1px solid #e5e7eb;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHostRequests as $r)
                            <tr style="font-size:14px;">
                                <td style="padding:8px 6px;">{{ $r->full_name }}</td>
                                <td style="padding:8px 6px;">{{ $r->email }}</td>
                                <td style="padding:8px 6px;">{{ $r->phone }}</td>
                                <td style="padding:8px 6px; text-transform:capitalize;">{{ $r->status }}</td>
                                <td style="padding:8px 6px;">
                                    <form method="POST" action="{{ route('admin.host-requests.approve', $r) }}" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn" style="padding:6px 10px; background:#16a34a; color:#fff; border-radius:6px;" @disabled($r->status==='approved')>Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.host-requests.reject', $r) }}" style="display:inline-block; margin-left:6px;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn ghost" style="padding:6px 10px; border:1px solid #dc2626; color:#dc2626; border-radius:6px;" @disabled($r->status==='rejected')>Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:12px 6px; color:#6b7280;">No recent host requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ route('admin.host-requests.index') }}" class="btn outline">Open full list</a>
            </div>
        </div>
    </div>

    
</x-admin-layout>

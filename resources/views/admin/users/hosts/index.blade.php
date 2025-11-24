<x-admin-layout>
    <div class="page-head">
        <h1>Hosts</h1>
        <p class="muted">List of approved host requests</p>
    </div>

    <div class="card" style="grid-column: 1 / -1;">
        <h3>Approved Hosts</h3>
        <div class="overflow-x-auto" style="margin-top:12px;">
            <table class="min-w-full" style="width:100%">
                <thead>
                    <tr style="text-align:left;font-size:12px;color:#9aa6b2;text-transform:uppercase">
                        <th style="padding:8px 10px;">Applicant</th>
                        <th style="padding:8px 10px;">Email</th>
                        <th style="padding:8px 10px;">Phone</th>
                        <th style="padding:8px 10px;">User</th>
                        <th style="padding:8px 10px;">Approved At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approved as $r)
                        <tr style="font-size:14px;border-top:1px solid var(--border)">
                            <td style="padding:8px 10px;">{{ $r->full_name }}</td>
                            <td style="padding:8px 10px;">{{ $r->email }}</td>
                            <td style="padding:8px 10px;">{{ $r->phone }}</td>
                            <td style="padding:8px 10px;">
                                @if($r->user)
                                    {{ $r->user->name }} <span class="muted">(#{{ $r->user->id }})</span>
                                @else
                                    <span class="muted">N/A</span>
                                @endif
                            </td>
                            <td style="padding:8px 10px;">{{ optional($r->updated_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:14px 10px; text-align:center" class="muted">No approved hosts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;">
            {{ $approved->links() }}
        </div>
    </div>
</x-admin-layout>

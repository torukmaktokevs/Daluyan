<x-admin-layout>
    <div class="page-head">
        <h1>Tenants</h1>
        <p class="muted">Users who are not admins and not approved hosts</p>
    </div>

    <div class="card" style="grid-column: 1 / -1;">
        <h3>Tenants List</h3>
        <div class="overflow-x-auto" style="margin-top:12px;">
            <table class="min-w-full" style="width:100%">
                <thead>
                    <tr style="text-align:left;font-size:12px;color:#9aa6b2;text-transform:uppercase">
                        <th style="padding:8px 10px;">Name</th>
                        <th style="padding:8px 10px;">Email</th>
                        <th style="padding:8px 10px;">Joined</th>
                        <th style="padding:8px 10px;">ID</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $u)
                        <tr style="font-size:14px;border-top:1px solid var(--border)">
                            <td style="padding:8px 10px;">{{ $u->name }}</td>
                            <td style="padding:8px 10px;">{{ $u->email }}</td>
                            <td style="padding:8px 10px;">{{ optional($u->created_at)->format('Y-m-d H:i') }}</td>
                            <td style="padding:8px 10px;">#{{ $u->id }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:14px 10px; text-align:center" class="muted">No tenants to show.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;">
            {{ $tenants->links() }}
        </div>
    </div>
</x-admin-layout>
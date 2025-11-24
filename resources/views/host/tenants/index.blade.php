<x-host-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0">Tenants</h1>
    </div>

    <div class="grid">
        <div class="card" style="grid-column:1 / -1;">
            @php
                $rows = $applications ?? collect();
            @endphp
            @if($rows->isEmpty())
                <p class="muted">No tenants yet. Approved applications will appear here.</p>
            @else
                <div class="table-responsive">
                    <table class="min-w-full" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; font-size:12px; color:#94a3b8;">
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Tenant</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Apartment</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Visit Date</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Move-in Date</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $app)
                                @php
                                    $apt = $app->apartment;
                                @endphp
                                <tr style="font-size:14px;">
                                    <td style="padding:8px 6px;display:flex;gap:8px;align-items:center;">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($app->tenant?->name ?? 'Tenant') }}&background=0b1627&color=e5e7eb" alt="avatar" style="width:28px;height:28px;border-radius:9999px;border:1px solid var(--border);object-fit:cover;" />
                                        <div>
                                            <div style="font-weight:600;">{{ $app->tenant?->name ?? 'Tenant' }}</div>
                                            <div class="muted" style="font-size:12px;">{{ $app->tenant?->email }}</div>
                                        </div>
                                    </td>
                                    <td style="padding:8px 6px;">
                                        <div style="font-weight:600;">{{ $apt?->title ?? ('Apartment #'.$app->apartment_id) }}</div>
                                        <div class="muted" style="font-size:12px;">{{ $apt?->address }}</div>
                                    </td>
                                    <td style="padding:8px 6px;">
                                        @if($app->visit_date)
                                            {{ \Carbon\Carbon::parse($app->visit_date)->toFormattedDateString() }}
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:8px 6px;">
                                        @if($app->movein_date)
                                            {{ \Carbon\Carbon::parse($app->movein_date)->toFormattedDateString() }}
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:8px 6px;">
                                        <a href="{{ route('host.tenants.show', $app) }}" class="btn" style="padding:6px 10px;">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-host-layout>

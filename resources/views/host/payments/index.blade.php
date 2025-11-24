<x-host-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
            <h1 style="margin:0">Payments</h1>
            <p class="muted">Record and monitor cash transactions for your properties.</p>
        </div>
        <div>
            <a href="{{ route('host.payments.create') }}" class="btn">+ Record Cash Payment</a>
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="border-color:#16a34a;color:#dcfce7;background:#064e3b;margin-top:12px;">{{ session('success') }}</div>
    @endif

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0">Recent Cash Transactions</h3>
        @if($payments->isEmpty())
            <div style="padding:14px;color:#6b7280;">No payments recorded yet.</div>
        @else
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid var(--border);">
                        <th style="padding:10px">Date</th>
                        <th style="padding:10px">Apartment</th>
                        <th style="padding:10px">Tenant</th>
                        <th style="padding:10px">Amount</th>
                        <th style="padding:10px">Reference</th>
                        <th style="padding:10px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                            <td style="padding:10px;vertical-align:top;">{{ \Carbon\Carbon::parse($p->created_at)->toDayDateTimeString() }}</td>
                            <td style="padding:10px;vertical-align:top;">{{ optional(\App\Models\Apartment::find($p->apartment_id))->title ?? '—' }}</td>
                            <td style="padding:10px;vertical-align:top;">{{ optional(\App\Models\User::find($p->tenant_user_id))->name ?? ('#'.$p->tenant_user_id) }}</td>
                            <td style="padding:10px;vertical-align:top;">₱{{ number_format((float)($p->amount ?? 0), 2) }}</td>
                            <td style="padding:10px;vertical-align:top;">{{ $p->reference ?? '—' }}</td>
                            <td style="padding:10px;vertical-align:top;">
                                <form method="POST" action="{{ route('host.payments.destroy', $p) }}" onsubmit="return confirm('Remove this payment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn ghost" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-host-layout>

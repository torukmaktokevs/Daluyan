<x-tenant-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
            <h1 style="margin:0">Payments</h1>
            <p class="muted">This page is for payment transaction monitoring only. No online payment methods are integrated here; records are treated as cash transactions.</p>
        </div>
        <div>
            <a href="{{ route('tenant.browsing') }}" class="btn outline">← Back to Browsing</a>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0">Recent Transactions</h3>
        @if($payments->isEmpty())
            <div style="padding:14px;color:#6b7280;">No payment transactions found.</div>
        @else
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid var(--border);">
                        <th style="padding:10px">Date</th>
                        <th style="padding:10px">Reference</th>
                        <th style="padding:10px">Amount</th>
                        <th style="padding:10px">Method</th>
                        <th style="padding:10px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                            <td style="padding:10px;vertical-align:top;">{{ \Carbon\Carbon::parse($p->created_at)->toDayDateTimeString() }}</td>
                            <td style="padding:10px;vertical-align:top;">{{ $p->reference ?? ('TXN-' . ($p->id ?? '')) }}</td>
                            <td style="padding:10px;vertical-align:top;">₱{{ number_format((float)($p->amount ?? 0), 2) }}</td>
                            <td style="padding:10px;vertical-align:top;">Cash</td>
                            <td style="padding:10px;vertical-align:top;">{{ ucfirst($p->status ?? 'completed') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0">Notes</h3>
        <ul>
            <li>This page does not accept or process online payments.</li>
            <li>Transactions listed here are for your records; please contact property management to arrange or confirm cash payments.</li>
            <li>If you expect a transaction to appear here but it is missing, contact the site administrator.</li>
        </ul>
    </div>
</x-tenant-layout>

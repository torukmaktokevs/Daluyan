<div style="padding:14px;border-radius:10px;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.02);display:flex;justify-content:space-between;align-items:center;gap:12px;">
    <div style="flex:1;min-width:0;">
        <div style="font-weight:700;color:var(--text);">{{ $req->title }}</div>
        <div class="muted" style="font-size:13px;margin-top:6px;">{{ Str::limit($req->description, 160) }}</div>
        <div class="muted" style="font-size:12px;margin-top:6px;">Submitted {{ $req->created_at->diffForHumans() }}</div>
    </div>
    <div style="text-align:right;min-width:220px;">
        <div style="font-size:13px;color:var(--muted);">Apartment</div>
        <div style="font-weight:700;color:var(--text);">{{ $req->apartment?->title ?? '—' }}</div>
        <div style="margin-top:8px;">
            @php
                $status = $req->status;
                $bg = $status==='open' ? '#fef3c7' : ($status==='in_progress' ? '#e0f2fe' : '#ecfdf5');
                $color = $status==='open' ? '#92400e' : ($status==='in_progress' ? '#0369a1' : '#065f46');
            @endphp
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
                <span style="padding:6px 10px;border-radius:999px;background:{{ $bg }};color:{{ $color }};font-weight:600;">{{ ucfirst(str_replace('_',' ', $status)) }}</span>

                @if($req->status === 'open')
                    <form method="POST" action="{{ route('host.maintenance.resolve', $req) }}" style="margin:0;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn" style="background:#10b981;color:#04261b;padding:6px 10px;border-radius:8px;border:1px solid rgba(16,185,129,0.12);font-weight:600;">Resolve</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

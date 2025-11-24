<x-host-layout>
    <section class="page-head">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h1 style="margin:0;font-size:20px;font-weight:700;color:var(--text);">Maintenance Requests</h1>
                <p class="muted" style="margin:6px 0 0;color:var(--muted);">Review tenant maintenance requests and update statuses.</p>
            </div>
        </div>
    </section>

    <div style="margin-top:18px;display:flex;justify-content:center;">
        <div style="width:980px;max-width:96%;">
            <div style="background:linear-gradient(180deg, rgba(8,12,20,0.6), rgba(6,10,18,0.55));border-radius:12px;padding:18px;border:1px solid rgba(255,255,255,0.03);">
                <div style="border-bottom:1px solid rgba(255,255,255,0.02);padding-bottom:12px;margin-bottom:12px;">
                    <nav style="display:flex;gap:18px;">
                        <a href="{{ route('host.maintenance.index', ['tab' => 'open']) }}" class="{{ $tab==='open' ? 'text-amber-500 font-semibold' : 'text-gray-400' }}">Open</a>
                        <a href="{{ route('host.maintenance.index', ['tab' => 'completed']) }}" class="{{ $tab==='completed' ? 'text-amber-500 font-semibold' : 'text-gray-400' }}">Resolved</a>
                    </nav>
                </div>

                @php($list = $requests[$tab] ?? collect())
                @if($list->isEmpty())
                    <div class="muted" style="padding:28px 12px;text-align:center;color:var(--muted);">No {{ str_replace('_',' ', $tab) }} requests.</div>
                @else
                    <div style="display:grid;gap:12px;">
                        @foreach($list as $req)
                            @include('host.maintenance._row', ['req' => $req])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-host-layout>

<x-host-layout>
    @if(session('success'))
        <div style="margin-bottom:16px;padding:12px 16px;border:1px solid #10b981;background:#ecfdf5;color:#065f46;border-radius:8px;display:flex;align-items:center;gap:8px;">
            <strong>Success:</strong> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:16px;padding:12px 16px;border:1px solid #ef4444;background:#fef2f2;color:#991b1b;border-radius:8px;display:flex;align-items:center;gap:8px;">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0">Tenant Details</h1>
        <a href="{{ route('host.tenants.index') }}" class="btn outline" style="white-space:nowrap;">Back to Tenants</a>
    </div>

    <div class="grid" style="grid-template-columns:1.1fr .9fr;gap:28px;">
        <div class="card" style="padding:28px;">
            <div style="display:flex;align-items:center;gap:20px;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant?->name ?? 'Tenant') }}&background=0b1627&color=e5e7eb" alt="avatar" style="width:80px;height:80px;border-radius:16px;object-fit:cover;border:2px solid var(--border);" />
                <div>
                    <h2 style="margin:0 0 4px;font-size:1.6rem;font-weight:600;">{{ $tenant?->name }}</h2>
                    <div style="color:#64748b;font-size:14px;">{{ $tenant?->email }}</div>
                </div>
            </div>

            <hr style="margin:24px 0;border:none;border-top:1px solid var(--border);" />

            <h3 style="margin:0 0 12px;font-weight:600;">Application Summary</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                    <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Status</div>
                    <div style="margin-top:4px;font-weight:600;color:#016B61;">{{ ucfirst($application->status) }}</div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                    <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Visit Date</div>
                    <div style="margin-top:4px;font-weight:600;">{{ $application->visit_date ? $application->visit_date->toFormattedDateString() : '—' }}</div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                    <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Move-in Date</div>
                    <div style="margin-top:4px;font-weight:600;">
                        @if($application->movein_date)
                            {{ $application->movein_date->toFormattedDateString() }}
                        @else
                            <form method="POST" action="{{ route('host.tenants.movein', $application) }}" style="display:flex;gap:8px;align-items:center;">
                                @csrf
                                @method('PATCH')
                                <input type="date" name="movein_date" value="{{ old('movein_date') }}" style="padding:8px;border:1px solid #cbd5e1;border-radius:8px;" required />
                                <button type="submit" class="btn" style="background:#016B61;color:#fff;padding:8px 12px;border-radius:8px;font-weight:600;">Set</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                    <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Est. Total</div>
                    <div style="margin-top:4px;font-weight:600;">{{ $application->total_price ? '₱'.number_format((float)$application->total_price,2) : '—' }}</div>
                </div>
            </div>

            @if($application->message)
                <div style="margin-top:24px;">
                    <h4 style="margin:0 0 8px;font-weight:600;">Original Message</h4>
                    <p style="margin:0;line-height:1.55;color:#48556a;background:#f1f5f9;padding:16px 20px;border-radius:14px;white-space:pre-line;">{{ $application->message }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('host.tenants.remove', $application) }}" style="margin-top:32px;" onsubmit="return confirmTerminate(this)">
                @csrf
                @method('DELETE')
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div style="color:#64748b;font-size:14px;max-width:480px;">Terminating this tenant will mark their application as <strong>terminated</strong>. This action cannot be easily undone.</div>
                    <button type="submit" class="btn" style="background:#dc2626;color:#fff;padding:10px 18px;border-radius:10px;font-weight:600;box-shadow:0 4px 12px -2px rgba(220,38,38,.5);display:flex;align-items:center;gap:6px;">
                        <i class="fa fa-user-slash"></i> Terminate Tenant
                    </button>
                </div>
            </form>
            <script>
                function confirmTerminate(form){
                    return window.confirm('Are you sure you want to terminate this tenant? This will change their status to terminated.');
                }
            </script>
        </div>

        <div class="card" style="padding:24px;">
            <h3 style="margin:0 0 16px;font-weight:600;">Apartment</h3>
            <div style="display:flex;gap:16px;align-items:flex-start;">
                @php $thumb = optional($apartment?->files->first())->path ? asset('storage/'.optional($apartment->files->first())->path) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=400&q=60'; @endphp
                <img src="{{ $thumb }}" alt="thumb" style="width:140px;height:140px;border-radius:16px;object-fit:cover;border:1px solid var(--border);" />
                <div>
                    <div style="font-weight:600;font-size:1.1rem;">{{ $apartment?->title ?? 'Apartment' }}</div>
                    <div class="muted" style="font-size:13px;">{{ $apartment?->address }}</div>
                    <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#475569;">
                        <span style="background:#f1f5f9;padding:6px 10px;border-radius:10px;">₱{{ number_format((float)$apartment?->price,2) }}/mo</span>
                        <span style="background:#f1f5f9;padding:6px 10px;border-radius:10px;">Bedrooms: {{ $apartment?->bedrooms }}</span>
                        <span style="background:#f1f5f9;padding:6px 10px;border-radius:10px;">Bathrooms: {{ $apartment?->bathrooms }}</span>
                        @if($apartment?->area)
                        <span style="background:#f1f5f9;padding:6px 10px;border-radius:10px;">Area: {{ $apartment->area }} sqm</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($apartment && $apartment->files->count() > 1)
                <hr style="margin:24px 0;border:none;border-top:1px solid var(--border);" />
                <h4 style="margin:0 0 12px;font-weight:600;">Gallery</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:12px;">
                    @foreach($apartment->files->slice(1) as $img)
                        <img src="{{ asset('storage/'.$img->path) }}" alt="img" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:10px;border:1px solid var(--border);" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-host-layout>

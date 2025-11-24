@php
    $isLandlord = auth()->user()->is_admin ?? false; // admin role
    // Choose layout: admin > host > tenant
    $layoutComponent = $isLandlord
        ? 'admin-layout'
        : (!empty($isHostApproved) && $isHostApproved ? 'host-layout' : 'tenant-layout');
@endphp

<x-dynamic-component :component="$layoutComponent">
    <div id="tenant-dashboard" class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0">My Profile</h1>
            <p class="muted" style="margin:6px 0 0 0">Manage your info, lease, and requests.</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('tenant.browsing') }}" class="btn outline">← Back to Browsing</a>
            {{-- Host dashboard button removed: management is accessible via sidebar (Manage Apartments / Add Property) --}}
        </div>
    </div>

    <div class="grid stats">
        <div class="card stat-card">
            <div>
                <div class="muted">Apartment</div>
                <h3 style="margin:4px 0 0 0; font-weight:700;">
                    @if(!$isLandlord && $currentLease && $currentLease->apartment)
                        {{ ($currentLease->apartment->unit ?? '') ? 'Unit '.$currentLease->apartment->unit.' – ' : '' }}{{ $currentLease->apartment->name ?? ('Apt #'.$currentLease->apartment->id) }}
                    @else — @endif
                </h3>
            </div>
            <i class="fa fa-building fa-2x accent"></i>
        </div>
        <div class="card stat-card">
            <div>
                <div class="muted">Balance</div>
                <h3 style="margin:4px 0 0 0; font-weight:700;">—</h3>
            </div>
            <i class="fa fa-peso-sign fa-2x accent"></i>
        </div>
        <div class="card stat-card">
            <div>
                <div class="muted">Pending Requests</div>
                <h3 style="margin:4px 0 0 0; font-weight:700;">—</h3>
            </div>
            <i class="fa fa-wrench fa-2x accent"></i>
        </div>
        <div class="card stat-card">
            <div>
                <div class="muted">Lease Ends</div>
                <h3 style="margin:4px 0 0 0; font-weight:700;">
                    @if(!$isLandlord && $currentLease && $currentLease->end_date)
                        {{ \Carbon\Carbon::parse($currentLease->end_date)->format('F Y') }}
                    @else — @endif
                </h3>
            </div>
            <i class="fa fa-calendar fa-2x accent"></i>
        </div>
    </div>

    <div class="grid" id="my-profile">
        <div class="card">
            <h3>My Profile</h3>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=111827&color=e5e7eb" alt="User" style="width:56px;height:56px;border-radius:10px;object-fit:cover;border:1px solid var(--border);" />
                <div>
                    <strong>{{ $user->name }}</strong>
                    <div class="muted">{{ $isLandlord ? 'Landlord' : 'Tenant' }}</div>
                </div>
            </div>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> —</p>
            <p><strong>Address:</strong> —</p>
            <div style="margin-top:10px;">
                <a href="{{ route('profile.show') }}" class="btn">Edit Profile</a>
            </div>
        </div>

        <div class="card" id="my-apartment">
            <h3>My Apartment</h3>
            @if(!$isLandlord && $currentLease && $currentLease->apartment)
                <p><strong>Current Unit:</strong> {{ $currentLease->apartment->unit ?? '—' }}</p>
                <p><strong>Lease Start:</strong> {{ $currentLease->start_date ? \Carbon\Carbon::parse($currentLease->start_date)->toFormattedDateString() : '—' }}</p>
                <p><strong>Lease End:</strong> {{ $currentLease->end_date ? \Carbon\Carbon::parse($currentLease->end_date)->toFormattedDateString() : '—' }}</p>
                <a class="btn ghost" href="#">Download Contract</a>
            @else
                <p class="muted">—</p>
            @endif
        </div>

        
        

        <div class="card" id="applications" style="grid-column: 1 / -1;">
            <h3>Application Status</h3>
            @if(isset($applications) && $applications->count())
                <ul class="activity">
                    @foreach($applications as $app)
                        @php
                            $apt = $app->apartment;
                            $thumb = optional(optional($apt)->files->first())->path ?? null;
                            $img = $thumb ? asset('storage/'.$thumb) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=400&q=60';
                        @endphp
                        <li style="display:flex;gap:12px;align-items:center;">
                            <img src="{{ $img }}" alt="thumb" style="width:56px;height:56px;border-radius:10px;object-fit:cover;border:1px solid var(--border);"/>
                            <div style="flex:1;">
                                <div style="font-weight:600;"><a href="{{ route('tenant.apartments.show', $apt) }}">{{ $apt->title ?? 'Apartment #'.$app->apartment_id }}</a></div>
                                <div class="muted" style="font-size:14px;">
                                    @if($app->visit_date)
                                        Visit: {{ \Carbon\Carbon::parse($app->visit_date)->toFormattedDateString() }}
                                    @else
                                        Visit date not set
                                    @endif
                                    @if($app->movein_date) • Move-in: {{ \Carbon\Carbon::parse($app->movein_date)->toFormattedDateString() }} @endif
                                    @if($app->total_price) • ₱{{ number_format((float)$app->total_price, 2) }} total @endif
                                </div>
                            </div>
                            <span class="muted" style="text-transform:capitalize;">{{ $app->status }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="muted">No applications yet.</p>
            @endif
        </div>

    </div>

</x-dynamic-component>

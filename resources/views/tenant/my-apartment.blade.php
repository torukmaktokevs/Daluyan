<x-tenant-layout>
    @php
        $apt = $apartment;
    @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
            <h1 style="margin:0;font-size:clamp(1.8rem,2.5vw,2.4rem);font-weight:700;letter-spacing:-.5px;">My Apartment</h1>
            <p class="muted" style="margin:4px 0 0;">Your approved rental details and quick actions.</p>
        </div>
        @if($apt)
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="{{ route('tenant.apartments.show', $apt) }}" class="btn outline" style="white-space:nowrap;">Open Full Listing</a>
                <button type="button" id="moveOutBtn" class="btn btn-decline" style="white-space:nowrap;">Move Out</button>
            </div>
        @endif
    </div>

    @if(!$apt)
        <div class="card" style="padding:32px;display:flex;flex-direction:column;align-items:center;text-align:center;gap:12px;">
            <div style="width:84px;height:84px;border-radius:16px;background:linear-gradient(135deg,#9ECFD4,#E5E9C5);display:flex;align-items:center;justify-content:center;font-size:40px;color:#016B61;">
                <i class="fa fa-building"></i>
            </div>
            <h2 style="margin:0;font-weight:600;">No Apartment Assigned Yet</h2>
            <p class="muted" style="max-width:440px;">Once a host approves your application, your apartment details will appear here. Browse available places to find your next home.</p>
            <a href="{{ route('tenant.browsing') }}" class="btn" style="background:#016B61;">Browse Apartments</a>
        </div>
    @else
        <div class="grid" style="grid-template-columns:1.15fr .85fr;gap:28px;">
            <!-- Left: Hero & Details -->
            <div>
                <div style="position:relative;border-radius:24px;overflow:hidden;box-shadow:0 12px 32px -8px rgba(0,0,0,.25);">
                    @php $hero = optional($images->first())->path ? asset('storage/'.$images->first()->path) : 'https://images.unsplash.com/photo-1486304873000-235643847519?auto=format&fit=crop&w=1200&q=60'; @endphp
                    <img src="{{ $hero }}" alt="Apartment hero" style="width:100%;height:360px;object-fit:cover;filter:brightness(.9);" />
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,.15));"></div>
                    <div style="position:absolute;left:24px;bottom:24px;color:#fff;">
                        <h2 style="margin:0 0 6px;font-size:2rem;font-weight:700;">{{ $apt->title }}</h2>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:14px;">
                            <span style="background:rgba(255,255,255,.15);padding:6px 12px;border-radius:999px;backdrop-filter:blur(4px);">₱{{ number_format((float)$apt->price,2) }}/mo</span>
                            <span style="background:rgba(255,255,255,.15);padding:6px 12px;border-radius:999px;backdrop-filter:blur(4px);"><i class="fa fa-map-pin"></i> {{ $apt->address }}</span>
                            @if($apt->area)
                                <span style="background:rgba(255,255,255,.15);padding:6px 12px;border-radius:999px;backdrop-filter:blur(4px);"><i class="fa fa-ruler-combined"></i> {{ $apt->area }} sqm</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top:28px;padding:28px;">
                    <h3 style="margin:0 0 16px;font-size:1.4rem;font-weight:600;">Rental Summary</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                            <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Status</div>
                            <div style="margin-top:4px;font-weight:600;color:#016B61;display:flex;align-items:center;gap:6px;">
                                <i class="fa fa-check-circle" style="color:#10b981;"></i> Approved
                            </div>
                        </div>
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                            <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Visit Date</div>
                            <div style="margin-top:4px;font-weight:600;">{{ $application?->visit_date ? $application->visit_date->toFormattedDateString() : '—' }}</div>
                        </div>
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                            <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Move-in Date</div>
                            <div style="margin-top:4px;font-weight:600;">{{ $application?->movein_date ? $application->movein_date->toFormattedDateString() : '—' }}</div>
                        </div>
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;">
                            <div style="font-size:12px;text-transform:uppercase;color:#64748b;font-weight:600;">Est. Total</div>
                            <div style="margin-top:4px;font-weight:600;">{{ $application?->total_price ? '₱'.number_format((float)$application->total_price,2) : '—' }}</div>
                        </div>
                    </div>

                    @if($application?->message)
                        <div style="margin-top:24px;">
                            <h4 style="margin:0 0 8px;font-weight:600;">Your Application Message</h4>
                            <p style="margin:0;line-height:1.55;color:#48556a;background:#f1f5f9;padding:16px 20px;border-radius:14px;white-space:pre-line;">{{ $application->message }}</p>
                        </div>
                    @endif
                </div>

                @if($images->count() > 1)
                    <div class="card" style="margin-top:28px;padding:24px;">
                        <h3 style="margin:0 0 16px;font-size:1.3rem;font-weight:600;">Gallery</h3>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;">
                            @foreach($images->slice(1) as $img)
                                <div style="position:relative;border-radius:12px;overflow:hidden;">
                                    <img src="{{ asset('storage/'.$img->path) }}" alt="Image" style="width:100%;aspect-ratio:1/1;object-fit:cover;" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Host & Contact -->
            <div style="display:flex;flex-direction:column;gap:28px;">
                <div class="card" style="padding:28px;">
                    <h3 style="margin:0 0 18px;font-size:1.3rem;font-weight:600;">Host Information</h3>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($application?->host?->name ?? 'Host') }}&background=016B61&color=fff" alt="Host" style="width:64px;height:64px;border-radius:16px;object-fit:cover;border:3px solid #ddf2f1;" />
                        <div>
                            <div style="font-weight:600;font-size:1.1rem;">{{ $application?->host?->name ?? 'Host' }}</div>
                            <div class="muted" style="font-size:13px;">{{ $application?->host?->email }}</div>
                        </div>
                    </div>
                    <div style="margin-top:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;">
                        <div style="background:#f1f5f9;border-radius:10px;padding:10px 12px;font-size:12px;display:flex;align-items:center;gap:6px;"><i class="fa fa-check" style="color:#10b981;"></i> Verified Host</div>
                        <div style="background:#f1f5f9;border-radius:10px;padding:10px 12px;font-size:12px;display:flex;align-items:center;gap:6px;"><i class="fa fa-shield-halved" style="color:#6366f1;"></i> Secure Listing</div>
                    </div>
                    
                </div>

                <div class="card" style="padding:24px;">
                    <h3 style="margin:0 0 14px;font-size:1.2rem;font-weight:600;">Quick Facts</h3>
                    <ul style="list-style:none;padding:0;margin:0;display:grid;gap:10px;font-size:14px;">
                        <li style="display:flex;align-items:center;gap:10px;"><i class="fa fa-bed" style="color:#016B61;width:18px;text-align:center;"></i> {{ $apt->bedrooms }} Bedrooms</li>
                        <li style="display:flex;align-items:center;gap:10px;"><i class="fa fa-bath" style="color:#016B61;width:18px;text-align:center;"></i> {{ $apt->bathrooms }} Bathrooms</li>
                        @if($apt->area)
                        <li style="display:flex;align-items:center;gap:10px;"><i class="fa fa-ruler-combined" style="color:#016B61;width:18px;text-align:center;"></i> {{ $apt->area }} sqm Floor Area</li>
                        @endif
                        <li style="display:flex;align-items:center;gap:10px;"><i class="fa fa-location-dot" style="color:#016B61;width:18px;text-align:center;"></i> {{ $apt->address }}</li>
                    </ul>
                </div>

                
            </div>
        </div>
    @endif

    <!-- Move Out / Rating Modal -->
    <div id="moveOutModal" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.6);z-index:1400;align-items:center;justify-content:center;padding:24px;">
        <div style="background:var(--panel);width:520px;max-width:100%;border-radius:12px;padding:18px;box-shadow:0 12px 40px rgba(2,6,23,0.6);">
            <h3 style="margin:0 0 8px;">Rate your stay and confirm move out</h3>
            <p class="muted" style="margin:0 0 12px;">Please leave a rating and optional comment. This will remove the apartment from your account.</p>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <div id="moveOutStars" style="font-size:28px;color:#cbd5e1;cursor:pointer;">
                    <span class="star" data-value="1">★</span>
                    <span class="star" data-value="2">★</span>
                    <span class="star" data-value="3">★</span>
                    <span class="star" data-value="4">★</span>
                    <span class="star" data-value="5">★</span>
                </div>
                <div id="moveOutRatingLabel" class="muted" style="font-size:13px;">No rating</div>
            </div>
            <textarea id="moveOutComment" placeholder="Leave an optional comment..." style="width:100%;min-height:100px;padding:10px;border-radius:8px;border:1px solid rgba(0,0,0,0.06);background:transparent;color:var(--text);resize:vertical;margin-bottom:12px;"></textarea>
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" id="moveOutCancel" class="btn btn-outline">Cancel</button>
                <button type="button" id="moveOutSubmit" class="btn btn-decline">Confirm Move Out</button>
            </div>
        </div>
    </div>
</x-tenant-layout>

<script>
    (function(){
        const moveBtn = document.getElementById('moveOutBtn');
        const modal = document.getElementById('moveOutModal');
        const cancel = document.getElementById('moveOutCancel');
        const submit = document.getElementById('moveOutSubmit');
        const stars = document.querySelectorAll('#moveOutStars .star');
        const ratingLabel = document.getElementById('moveOutRatingLabel');
        const commentEl = document.getElementById('moveOutComment');
        let rating = 0;
        const aptId = {{ $apt ? (int)$apt->id : 'null' }};
        const token = '{{ csrf_token() }}';

        function openModal(){ if(modal) modal.style.display = 'flex'; }
        function closeModal(){ if(modal) modal.style.display = 'none'; }

        if (moveBtn) moveBtn.addEventListener('click', openModal);
        if (cancel) cancel.addEventListener('click', closeModal);

        if (stars && stars.length) {
            stars.forEach(s => {
                s.addEventListener('mouseover', e => {
                    const v = parseInt(s.dataset.value || '0',10);
                    highlightStars(v);
                });
                s.addEventListener('click', e => {
                    rating = parseInt(s.dataset.value || '0',10);
                    ratingLabel.textContent = rating ? rating + ' star' + (rating>1?'s':'') : 'No rating';
                    highlightStars(rating);
                });
            });
            modal && modal.addEventListener('mouseleave', () => highlightStars(rating));
        }

        function highlightStars(n){
            stars.forEach(s => {
                const v = parseInt(s.dataset.value||'0',10);
                s.style.color = v <= n ? '#f59e0b' : '#cbd5e1';
            });
        }

        if (submit) submit.addEventListener('click', async function(){
            if (!aptId) { alert('Apartment not found'); return; }
            submit.disabled = true; submit.style.opacity = '0.6';
            try {
                const fd = new FormData();
                fd.append('_token', token);
                fd.append('rating', rating);
                fd.append('comment', (commentEl && commentEl.value) ? commentEl.value : '');

                const res = await fetch(`/tenant/apartments/${aptId}/move-out`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd
                });
                if (!res.ok) {
                    const err = await res.json().catch(()=>({message:'Failed'}));
                    alert(err.message||'Failed to move out');
                    return;
                }
                // success — reload to reflect change
                location.reload();
            } catch (err) {
                console.error('move out error', err); alert('Network error');
            } finally { submit.disabled = false; submit.style.opacity = ''; }
        });
    })();
</script>

<x-tenant-layout>
    <style>
        /* Style select and its dropdown options to match the dark panel background */
        select.form-input {
            background: rgba(255,255,255,0.02) !important;
            color: var(--text) !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Option styling — Chrome/Chromium will honor these in most cases */
        select.form-input option {
            background: #071124; /* dark dropdown background */
            color: #dbeafe; /* light text */
        }

        /* Remove default arrow on IE/Edge and keep styling consistent */
        select.form-input::-ms-expand { display: none; }
    </style>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0">Maintenance Requests</h1>
            <p class="muted" style="margin:4px 0 0;">Report issues to your host and track their progress.</p>
        </div>
    </div>

    <div class="grid">
        <div class="card" style="grid-column:1 / -1;padding:22px;">
            @if(session('success'))
                <div style="margin-bottom:12px;padding:12px 16px;background:rgba(16,185,129,0.12);color:#06b6a4;border:1px solid rgba(16,185,129,0.18);border-radius:8px;">{{ session('success') }}</div>
            @endif
            <div style="display:flex;justify-content:center;padding-top:6px;">
                <div style="width:820px;max-width:96%;border-radius:12px;background:linear-gradient(180deg, rgba(8,12,20,0.6), rgba(6,10,18,0.55));padding:20px;border:1px solid rgba(255,255,255,0.03);box-shadow:0 6px 30px rgba(2,6,23,0.45);">

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;color:var(--muted);">
                        <div>
                            <div style="font-size:20px;font-weight:700;color:var(--text);">Maintenance Requests</div>
                            <div style="font-size:13px;margin-top:6px;color:var(--muted);">Report issues to your host and track their progress.</div>
                        </div>
                        <div style="text-align:right;min-width:160px;">
                            @if(!empty($approvedApartment))
                                <div style="font-size:13px;color:var(--muted);">Apartment</div>
                                <div style="font-weight:700;color:var(--text);">{{ $approvedApartment->title }}</div>
                            @else
                                <div style="font-size:13px;color:#f97316;font-weight:700;">No approved apartment</div>
                            @endif
                        </div>
                    </div>

                    <div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;">
                        <div style="flex:1;min-width:320px;">
                            <div style="background:rgba(255,255,255,0.01);border-radius:10px;padding:14px;border:1px solid rgba(255,255,255,0.02);">
                                <div style="font-weight:700;margin-bottom:8px;color:var(--text);">Create Maintenance Request</div>
                                <div class="muted" style="font-size:13px;margin-bottom:12px;">We'll notify your host after submission.</div>

                                <form method="POST" action="{{ route('tenant.maintenance.store') }}" style="display:flex;flex-direction:column;gap:12px;">
                                    @csrf
                                    @if(!empty($approvedApartment))
                                        <input type="hidden" name="apartment_id" value="{{ $approvedApartment->id }}" />
                                    @endif

                                    <div style="display:flex;gap:12px;">
                                        <input name="title" placeholder="Short title (e.g. Leaky kitchen faucet)" required class="form-input" style="flex:1;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);color:var(--text);padding:10px 12px;border-radius:8px;" />
                                        <select name="priority" class="form-input" style="width:160px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);color:var(--text);padding:10px 12px;border-radius:8px;">
                                            <option value="">Priority (optional)</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                    <textarea name="description" rows="5" placeholder="Describe the problem in detail (include location, time, any photos if available)" class="form-textarea" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);color:var(--text);padding:12px;border-radius:8px;min-height:120px;"></textarea>

                                    <div style="display:flex;justify-content:flex-end;margin-top:6px;">
                                        <button class="btn" style="background:#14b8a6;color:#04261b;padding:10px 16px;border-radius:8px;border:1px solid rgba(20,184,166,0.12);">Submit Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div style="width:360px;min-width:240px;">
                            <div style="font-weight:700;margin-bottom:10px;color:var(--text);">Your Requests</div>

                            @if($requests->isEmpty())
                                <div class="muted">You have no maintenance requests yet.</div>
                            @else
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    @foreach($requests as $r)
                                        @php
                                            $status = $r->status;
                                            $badgeColor = match($status) {
                                                'open' => 'background:#fef3c7;color:#92400e;',
                                                'in_progress' => 'background:#e0f2fe;color:#0369a1;',
                                                'completed' => 'background:#ecfdf5;color:#065f46;',
                                                default => 'background:#eef2ff;color:#4f46e5;'
                                            };
                                        @endphp

                                        <div style="padding:12px;border-radius:10px;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.02);">
                                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                                                <div style="flex:1;min-width:0;">
                                                    <div style="font-weight:700;color:var(--text);">{{ $r->title }}</div>
                                                    <div class="muted" style="font-size:13px;margin-top:6px;">{{ Str::limit($r->description, 140) }}</div>
                                                    <div class="muted" style="font-size:12px;margin-top:8px;">{{ $r->created_at->diffForHumans() }}</div>
                                                </div>
                                                <div style="text-align:right;min-width:120px;">
                                                    <div style="border-radius:999px;padding:6px 10px;font-weight:600;{{ $badgeColor }}">{{ ucfirst(str_replace('_',' ', $status)) }}</div>
                                                    <div style="font-size:13px;margin-top:8px;color:var(--muted);">{{ $r->apartment?->title ?? '—' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tenant-layout>
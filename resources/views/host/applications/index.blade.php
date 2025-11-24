<x-host-layout>
    @if(session('success'))
        <div style="margin-bottom:16px;padding:12px 16px;border:1px solid #10b981;background:#ecfdf5;color:#065f46;border-radius:8px;display:flex;align-items:center;gap:8px;">
            <strong style="font-weight:600;">Success:</strong> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:16px;padding:12px 16px;border:1px solid #ef4444;background:#fef2f2;color:#991b1b;border-radius:8px;display:flex;align-items:center;gap:8px;">
            <strong style="font-weight:600;">Error:</strong> {{ session('error') }}
        </div>
    @endif
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0">Applications</h1>
    </div>

    <div class="grid">
        <div class="card" style="grid-column:1 / -1;">
            <div style="border-bottom:1px solid var(--border);margin:-16px -16px 16px -16px;padding:0 16px;">
                <nav style="display:flex;gap:16px;">
                    @php
                        $tabs = [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'declined' => 'Declined',
                        ];
                    @endphp
                    @foreach($tabs as $key=>$label)
                        <a href="{{ route('host.applications.index', ['tab' => $key]) }}" style="padding:12px 0;border-bottom:2px solid {{ $tab===$key ? 'var(--accent)' : 'transparent' }};color:{{ $tab===$key ? 'var(--accent)' : 'var(--muted)' }};text-decoration:none;">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            @php
                $rows = $applications[$tab] ?? collect();
            @endphp
            @if($rows->isEmpty())
                <p class="muted">No {{ $tab }} applications.</p>
            @else
                <div class="applications-grid" style="display:flex;flex-direction:column;gap:14px;">
                    @foreach($rows as $app)
                        @php
                            $apt = $app->apartment;
                            $thumb = optional(optional($apt)->files->first())->path ?? null;
                            $img = $thumb ? asset('storage/'.$thumb) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=400&q=60';
                        @endphp

                        <div class="app-card" style="display:flex;align-items:center;justify-content:space-between;background:var(--panel);padding:14px;border-radius:10px;border:1px solid rgba(255,255,255,0.03);">
                            <div style="display:flex;gap:12px;align-items:center;min-width:0;">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($app->tenant?->name ?? 'Tenant') }}&background=0b1627&color=e5e7eb" alt="avatar" style="width:44px;height:44px;border-radius:9999px;border:1px solid var(--border);object-fit:cover;flex-shrink:0;" />
                                <div style="min-width:0;">
                                    <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $app->tenant?->name ?? 'Tenant' }}</div>
                                    <div class="muted" style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $app->tenant?->email }}</div>
                                    <div style="margin-top:6px;display:flex;gap:10px;align-items:center;">
                                        <img src="{{ $img }}" alt="apt" style="width:56px;height:56px;border-radius:8px;object-fit:cover;border:1px solid var(--border);flex-shrink:0;" />
                                        <div style="min-width:0;">
                                            <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $apt?->title ?? ('Apartment #'.$app->apartment_id) }}</div>
                                            <div class="muted" style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $apt?->address }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex;gap:12px;align-items:center;">
                                <div style="text-align:center;min-width:110px;">
                                    <div style="font-weight:600;">@if($app->visit_date){{ \Carbon\Carbon::parse($app->visit_date)->toFormattedDateString() }}@else — @endif</div>
                                    <div class="muted" style="font-size:12px;">Visit</div>
                                </div>
                                <div style="text-align:center;min-width:110px;">
                                    <div style="font-weight:600;">@if($app->movein_date){{ \Carbon\Carbon::parse($app->movein_date)->toFormattedDateString() }}@else — @endif</div>
                                    <div class="muted" style="font-size:12px;">Move-in</div>
                                </div>

                                <div class="app-actions">
                                    <span class="status-badge status-{{ $app->status }}">{{ ucfirst($app->status) }}</span>

                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-outline open-message-btn" data-app-id="{{ $app->id }}" data-tenant-name="{{ htmlspecialchars($app->tenant?->name ?? 'Tenant', ENT_QUOTES) }}" data-tenant-user-id="{{ $app->tenant_user_id ?? '' }}">Message</button>
                                        @if($app->status === 'pending')
                                            <form action="{{ route('host.applications.approve', $app->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-approve">Approve</button>
                                            </form>
                                            <form action="{{ route('host.applications.decline', $app->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-decline">Decline</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <!-- Messaging pane modal -->
    <div id="messagePane" style="display:none;position:fixed;right:18px;top:72px;width:520px;max-width:calc(100% - 32px);height:80vh;background:var(--panel);border:1px solid rgba(255,255,255,0.03);box-shadow:0 8px 24px rgba(2,6,23,0.6);border-radius:8px;z-index:1200;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border);">
            <strong id="messagePaneTitle">Conversation</strong>
            <button type="button" onclick="closeMessagePane()" style="background:none;border:none;color:var(--muted);font-size:18px;">&times;</button>
        </div>
        <div id="messagePaneBody" style="padding:12px;height:calc(80vh - 140px);overflow:auto;">No messages yet.</div>
        <form id="messagePaneForm" method="POST" action="{{ route('host.messages.send') }}" style="display:flex;gap:8px;padding:12px;border-top:1px solid var(--border);align-items:flex-end;">
            @csrf
            <input type="hidden" name="application_id" id="message_application_id" value="" />
            <input type="hidden" name="tenant_user_id" id="message_tenant_user_id" value="" />
            <div style="flex:1;display:flex;gap:10px;align-items:flex-end;">
                <textarea name="message" id="message_input" placeholder="Write a message..." style="flex:1;border-radius:6px;padding:8px;background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--text);resize:none;height:86px;"></textarea>
                <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="file" id="message_attachment" name="attachment" accept="image/*,.pdf" style="position:absolute;left:-9999px;" />
                        <label for="message_attachment" class="file-btn">Attach file</label>
                        <span id="message_attachment_name" class="file-name muted" style="font-size:12px;color:var(--muted);"></span>
                    </div>
                    <button type="button" id="messagePaneSendBtn" class="btn btn-approve" style="min-width:84px;">Send</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Button styles */
        .btn { padding:8px 12px;border-radius:8px;font-weight:600;border:none;cursor:pointer;transition:all .12s ease;display:inline-flex;align-items:center;gap:8px }
        .btn:active{transform:translateY(1px)}
        .btn-outline { background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--muted); }
        .btn-outline:hover { background:rgba(255,255,255,0.02);color:var(--text); }
        .btn-approve { background:#10b981;color:white;border:1px solid rgba(0,0,0,0.06); }
        .btn-approve:hover { background:#059669 !important; }
        .btn-decline { background:#ef4444;color:white;border:1px solid rgba(0,0,0,0.06); }
        .btn-decline:hover { background:#dc2626 !important; }

        .table-responsive { overflow-x: auto; }

        /* Action alignment inside application card */
        .app-actions { display:flex;flex-direction:column;align-items:flex-end;gap:8px;min-width:160px; }
        .action-buttons { display:flex;gap:8px;align-items:center; }
        .status-badge { padding:6px 12px;border-radius:10px;font-weight:700;min-width:76px;text-align:center;display:inline-block }
        .status-badge.status-pending { background:#fef3c7;color:#92400e;border:1px solid rgba(0,0,0,0.02) }
        .status-badge.status-approved { background:#d1fae5;color:#065f46;border:1px solid rgba(0,0,0,0.02) }
        .status-badge.status-declined { background:#fee2e2;color:#991b1b;border:1px solid rgba(0,0,0,0.02) }

        /* Message pane styles */
        #messagePaneBody { padding:12px; display:flex; flex-direction:column; gap:8px; }
        .msg { max-width:85%; padding:10px 12px; border-radius:12px; font-size:14px; line-height:1.4; display:flex;flex-direction:column; }
        .msg.me { align-self:flex-end; background: rgba(255,255,255,0.03); color:var(--text); border-top-right-radius:4px; }
        .msg.them { align-self:flex-start; background: rgba(16,185,129,0.12); color:var(--text); border-top-left-radius:4px; }
        .msg .meta { display:block; font-size:11px; color:var(--muted); margin-top:6px; }
        .file-btn { padding:6px 10px;border-radius:6px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:var(--muted);cursor:pointer;font-size:13px }
        .file-btn:hover { background:rgba(255,255,255,0.02); color:var(--text); }
        .file-name { max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    </style>

    <script>
        window.openMessagePane = function(applicationId, tenantName, tenantUserId) {
            // Hide tenant pane if present
            const tenantPane = document.getElementById('tenantMessagePane');
            if (tenantPane) tenantPane.style.display = 'none';
            const pane = document.getElementById('messagePane');
            if (!pane) return;
            pane.style.display = 'block';
            const titleEl = document.getElementById('messagePaneTitle');
            if (titleEl) titleEl.textContent = 'Conversation — ' + tenantName;
            const appInput = document.getElementById('message_application_id'); if (appInput) appInput.value = applicationId;
            // Populate tenant recipient id if provided
            const tenantInput = document.getElementById('message_tenant_user_id'); if (tenantInput) tenantInput.value = tenantUserId ?? '';
            // Optionally load messages via fetch if you implement a route like /host/applications/{id}/messages
            const body = document.getElementById('messagePaneBody');
            body.innerHTML = '<div class="muted">Loading conversation…</div>';
            // Try fetching conversation (graceful fallback)
            fetch(`/host/applications/${applicationId}/messages`, {headers: {Accept: 'application/json'}}).then(r => {
                if (!r.ok) throw new Error('no messages route');
                return r.json();
            }).then(data => {
                if (!Array.isArray(data)) throw new Error('invalid');
                body.innerHTML = '';
                    data.forEach(m => {
                        const el = document.createElement('div');
                        // on host page, m.from_host is true when message sent by the current host
                        el.className = 'msg ' + (m.from_host ? 'me' : 'them');
                        if (m.message) {
                            const text = document.createElement('div');
                            text.className = 'text';
                            text.textContent = m.message;
                            el.appendChild(text);
                        }
                        if (m.attachment_url) {
                            const a = document.createElement('a');
                            a.href = m.attachment_url; a.target = '_blank';
                            if (m.attachment_url.match(/\.(jpg|jpeg|png)$/i)) {
                                const img = document.createElement('img'); img.src = m.attachment_url; img.style.maxWidth = '220px'; img.style.display = 'block'; img.style.borderRadius = '6px'; a.appendChild(img);
                            } else { a.textContent = 'View attachment'; }
                            el.appendChild(a);
                        }
                        const meta = document.createElement('span');
                        meta.className = 'meta';
                        meta.textContent = m.created_at ? new Date(m.created_at).toLocaleString() : '';
                        el.appendChild(meta);
                        body.appendChild(el);
                    });
                // scroll to bottom
                body.scrollTop = body.scrollHeight;
            }).catch(() => {
                body.innerHTML = '<div class="muted">No messages available. Use the form below to send a message.</div>';
            });
        }

        window.closeMessagePane = function(){
            const pane = document.getElementById('messagePane'); if (pane) pane.style.display = 'none';
        }

        // Delegate click for message buttons to avoid inline onclick and quoting issues
        document.addEventListener('click', function(e){
            const btn = e.target.closest && e.target.closest('.open-message-btn');
            if (!btn) return;
            const appId = btn.dataset.appId;
            const tenantName = btn.dataset.tenantName || 'Tenant';
            const tenantUserId = btn.dataset.tenantUserId || null;
            try { window.openMessagePane(appId, tenantName, tenantUserId); } catch (err) { console.error('openMessagePane error', err); }
        });

        // AJAX submit for the message pane form (named function + progressive enhancement)
        (function(){
            const form = document.getElementById('messagePaneForm');
            if (!form) return;

            // keep submit listener but dispatch to named function
            form.addEventListener('submit', function(e){ e.preventDefault(); window.sendHostMessage(); });

            window.sendHostMessage = async function(){
                const btn = document.getElementById('messagePaneSendBtn');
                const bodyEl = document.getElementById('messagePaneBody');
                const applicationId = form.querySelector('#message_application_id').value;
                const tenantId = form.querySelector('#message_tenant_user_id').value;
                const messageInput = form.querySelector('#message_input');
                const message = (messageInput.value || '').trim();

                const attachmentInput = form.querySelector('#message_attachment');
                if (!message && (!attachmentInput || !attachmentInput.files || !attachmentInput.files.length)) return;

                if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
                const tokenInput = form.querySelector('input[name="_token"]');
                const token = tokenInput ? tokenInput.value : null;

                try {
                    const fd = new FormData();
                    fd.append('application_id', applicationId);
                    fd.append('tenant_user_id', tenantId);
                    if (message) fd.append('message', message);
                    if (attachmentInput && attachmentInput.files && attachmentInput.files.length) fd.append('attachment', attachmentInput.files[0]);

                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        body: fd,
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(()=>({message: 'Failed to send'}));
                        alert(err.message || 'Failed to send message');
                        return;
                    }

                    const data = await res.json();
                    const el = document.createElement('div');
                    el.className = 'msg me';
                    if (data.message) { const text = document.createElement('div'); text.className = 'text'; text.textContent = data.message || message; el.appendChild(text); }
                    if (data.attachment_url) { const a = document.createElement('a'); a.href = data.attachment_url; a.target = '_blank'; if (data.attachment_url.match(/\.(jpg|jpeg|png)$/i)) { const img = document.createElement('img'); img.src = data.attachment_url; img.style.maxWidth = '220px'; img.style.display = 'block'; img.style.borderRadius = '6px'; a.appendChild(img); } else { a.textContent = 'View attachment'; } el.appendChild(a); }
                    const meta = document.createElement('span'); meta.className = 'meta'; meta.textContent = data.created_at ? new Date(data.created_at).toLocaleString() : new Date().toLocaleString(); el.appendChild(meta);
                    bodyEl.appendChild(el);
                    bodyEl.scrollTop = bodyEl.scrollHeight;
                    if (messageInput) messageInput.value = '';
                    if (attachmentInput) attachmentInput.value = '';
                } catch (err) {
                    console.error(err);
                    alert('Network error while sending message');
                } finally {
                    if (btn) { btn.disabled = false; btn.style.opacity = ''; }
                }
            }
        })();

        // show selected filename for attachment in host pane and hook Send button
        (function(){
            const fileInput = document.getElementById('message_attachment');
            const nameEl = document.getElementById('message_attachment_name');
            if (fileInput && nameEl) {
                fileInput.addEventListener('change', function(){
                    if (fileInput.files && fileInput.files.length) nameEl.textContent = fileInput.files[0].name;
                    else nameEl.textContent = '';
                });
            }

            const sendBtn = document.getElementById('messagePaneSendBtn');
            if (sendBtn) sendBtn.addEventListener('click', function(){
                if (window.sendHostMessage) return window.sendHostMessage();
                // fallback: submit the form
                const f = document.getElementById('messagePaneForm'); if (f) f.requestSubmit?.();
            });
        })();
    </script>
</x-host-layout>

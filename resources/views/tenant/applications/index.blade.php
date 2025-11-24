<x-tenant-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0">My Applications</h1>
            <p class="muted" style="margin:4px 0 0;">Track the status of your apartment applications.</p>
        </div>
    </div>

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
                        <a href="{{ route('tenant.applications.index', ['tab' => $key]) }}" style="padding:12px 0;border-bottom:2px solid {{ $tab===$key ? 'var(--accent)' : 'transparent' }};color:{{ $tab===$key ? 'var(--accent)' : 'var(--muted)' }};text-decoration:none;">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            @php $rows = $applications[$tab] ?? collect(); @endphp
            @if($rows->isEmpty())
                <p class="muted">No {{ $tab }} applications.</p>
            @else
                <div class="table-responsive">
                    <table class="min-w-full" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; font-size:12px; color:#94a3b8;">
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Apartment</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Visit Date</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Move-in Date</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Message</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Status</th>
                                <th style="padding:8px 6px; border-bottom:1px solid var(--border);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $app)
                                @php
                                    $apt = $app->apartment;
                                    $thumb = optional(optional($apt)->files->first())->path ?? null;
                                    $img = $thumb ? asset('storage/'.$thumb) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=400&q=60';
                                @endphp
                                <tr style="font-size:14px;">
                                    <td style="padding:8px 6px;display:flex;gap:8px;align-items:center;">
                                        <img src="{{ $img }}" alt="thumb" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid var(--border);" />
                                        <div>
                                            <div style="font-weight:600;">{{ $apt?->title ?? ('Apartment #'.$app->apartment_id) }}</div>
                                            <div class="muted" style="font-size:12px;">{{ $apt?->address }}</div>
                                        </div>
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
                                    <td style="padding:8px 6px;max-width:260px;">
                                        <div style="display:flex;flex-direction:column;gap:6px;max-width:260px;">
                                            <button type="button" class="btn btn-outline" onclick="openTenantMessagePane({{ $app->id }}, '{{ addslashes($apt?->title ?? 'Apartment') }}')">Message</button>
                                            @if(!empty($app->message))
                                                <div class="muted" style="font-size:12px;">{{ Str::limit($app->message, 60) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding:8px 6px;text-transform:capitalize;">
                                        <span style="
                                            padding: 4px 8px;
                                            border-radius: 4px;
                                            font-size: 12px;
                                            font-weight: 500;
                                            background: {{ 
                                                $app->status === 'pending' ? '#fef3c7' : 
                                                ($app->status === 'approved' ? '#d1fae5' : 
                                                ($app->status === 'declined' || $app->status === 'rejected' ? '#fee2e2' : '#f3f4f6')) 
                                            }};
                                            color: {{ 
                                                $app->status === 'pending' ? '#b45309' : 
                                                ($app->status === 'approved' ? '#065f46' : 
                                                ($app->status === 'declined' || $app->status === 'rejected' ? '#b91c1c' : '#6b7280')) 
                                            }};
                                        ">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                    <td style="padding:8px 6px;white-space:nowrap;">
                                        <a href="{{ route('tenant.apartments.show', $app->apartment_id) }}" class="btn btn-outline" style="padding:6px 10px;">View</a>
                                        <!-- Upload moved into message pane -->
                                        @if($app->status === 'pending')
                                            <form action="{{ route('tenant.applications.cancel', $app->id) }}" method="POST" style="display:inline-block;margin-left:6px;" onsubmit="return confirm('Cancel this application?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-cancel" style="padding:6px 10px;">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Tenant message pane modal -->
    <div id="tenantMessagePane" style="display:none;position:fixed;right:18px;top:72px;width:520px;max-width:calc(100% - 32px);height:80vh;background:var(--panel);border:1px solid rgba(255,255,255,0.03);box-shadow:0 8px 24px rgba(2,6,23,0.6);border-radius:8px;z-index:1200;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border);">
            <strong id="tenantMessagePaneTitle">Conversation</strong>
            <button type="button" onclick="closeTenantMessagePane()" style="background:none;border:none;color:var(--muted);font-size:18px;">&times;</button>
        </div>
        <div id="tenantMessagePaneBody" style="padding:12px;height:calc(80vh - 140px);overflow:auto;">No messages yet.</div>
        <form id="tenantMessagePaneForm" method="POST" action="{{ route('tenant.messages.send') }}" onsubmit="event.preventDefault(); (window.sendTenantMessage || function(){ alert('Messaging not ready. Please reload the page.'); })();" style="display:flex;gap:8px;padding:12px;border-top:1px solid var(--border);align-items:flex-end;">
            @csrf
            <input type="hidden" name="application_id" id="tenant_message_application_id" value="" />
            <div style="flex:1;display:flex;gap:10px;align-items:flex-end;">
                <textarea name="message" id="tenant_message_input" placeholder="Write a message..." style="flex:1;border-radius:6px;padding:8px;background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--text);resize:none;height:86px;"></textarea>
                <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="file" id="tenant_message_attachment" name="attachment" accept="image/*,.pdf" style="position:absolute;left:-9999px;" />
                        <label for="tenant_message_attachment" class="file-btn">Attach file</label>
                        <span id="tenant_message_attachment_name" class="file-name muted" style="font-size:12px;color:var(--muted);"></span>
                    </div>
                    <button type="submit" id="tenantMessageSendBtn" class="btn btn-approve" style="min-width:84px;">Send</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Upload moved into message pane; upload modal removed -->

    <style>
        /* Buttons and panes styling (tenant) */
        .btn { padding:8px 12px;border-radius:8px;font-weight:600;border:none;cursor:pointer;transition:all .12s ease;display:inline-flex;align-items:center;gap:8px }
        .btn-outline { background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--muted); }
        .btn-outline:hover { background:rgba(255,255,255,0.02);color:var(--text); }
        .btn-approve { background:#10b981;color:white;border:1px solid rgba(0,0,0,0.06); }
        .btn-cancel { background:#dc2626;color:white;border:1px solid rgba(0,0,0,0.06); }
        /* Message styles */
        #tenantMessagePaneBody { padding:12px; display:flex; flex-direction:column; gap:8px; }
        .msg { max-width:85%; padding:10px 12px; border-radius:12px; font-size:14px; line-height:1.4; display:flex;flex-direction:column; }
        .msg.me { align-self:flex-end; background: rgba(255,255,255,0.03); color:var(--text); border-top-right-radius:4px; }
        .msg.them { align-self:flex-start; background: rgba(16,185,129,0.12); color:var(--text); border-top-left-radius:4px; }
        .msg .meta { display:block; font-size:11px; color:var(--muted); margin-top:6px; }
        .file-btn { padding:6px 10px;border-radius:6px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:var(--muted);cursor:pointer;font-size:13px }
        .file-btn:hover { background:rgba(255,255,255,0.02); color:var(--text); }
        .file-name { max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    </style>

    <script>
        // Ensure a placeholder exists so clicks never perform a normal form POST
        window.sendTenantMessage = window.sendTenantMessage || function(){ console.warn('sendTenantMessage not yet initialized'); alert('Messaging not ready. Please reload the page.'); };
        try {
            window.openTenantMessagePane = function(applicationId, title) {
            // Hide host pane if present
            const hostPane = document.getElementById('messagePane');
            if (hostPane) hostPane.style.display = 'none';
            const pane = document.getElementById('tenantMessagePane');
            if (!pane) return;
            pane.style.display = 'block';
            const titleEl = document.getElementById('tenantMessagePaneTitle'); if (titleEl) titleEl.textContent = 'Conversation — ' + title;
            const appInput = document.getElementById('tenant_message_application_id'); if (appInput) appInput.value = applicationId;
            const body = document.getElementById('tenantMessagePaneBody');
            if (body) body.innerHTML = '<div class="muted">Loading conversation…</div>';
            fetch(`/tenant/applications/${applicationId}/messages`, {headers: {Accept: 'application/json'}}).then(r => {
                if (!r.ok) throw new Error('no messages route');
                return r.json();
            }).then(data => {
                if (!Array.isArray(data)) throw new Error('invalid');
                body.innerHTML = '';
                    data.forEach(m => {
                        const el = document.createElement('div');
                        // on tenant page, m.from_tenant === true means message sent by current tenant
                        el.className = 'msg ' + (m.from_tenant ? 'me' : 'them');
                        const text = document.createElement('div');
                        text.className = 'text';
                        if (m.message) text.textContent = m.message;
                        if (m.attachment_url) {
                            // render image or link
                            const a = document.createElement('a');
                            a.href = m.attachment_url;
                            a.target = '_blank';
                            if (m.attachment_url.match(/\.(jpg|jpeg|png)$/i)) {
                                const img = document.createElement('img');
                                img.src = m.attachment_url;
                                img.style.maxWidth = '220px';
                                img.style.display = 'block';
                                img.style.borderRadius = '6px';
                                a.appendChild(img);
                                if (m.message) a.style.marginTop = '8px';
                            } else {
                                a.textContent = 'View attachment';
                            }
                            el.appendChild(a);
                        }
                        const meta = document.createElement('span');
                        meta.className = 'meta';
                        meta.textContent = m.created_at ? new Date(m.created_at).toLocaleString() : '';
                        if (m.message) el.appendChild(text);
                        el.appendChild(meta);
                        body.appendChild(el);
                    });
                body.scrollTop = body.scrollHeight;
            }).catch(() => {
                body.innerHTML = '<div class="muted">No messages available. Use the form below to send a message.</div>';
            });
        }
        } catch (err) {
            console.error('openTenantMessagePane initialization error', err);
            // Fallback: define a noop to avoid blocking other scripts
            window.openTenantMessagePane = function(){ console.warn('openTenantMessagePane unavailable'); };
        }

        window.closeTenantMessagePane = function(){ const pane = document.getElementById('tenantMessagePane'); if (pane) pane.style.display = 'none'; }

        // message submit
            // message submit — initialize safely so an earlier error doesn't leave sendTenantMessage undefined
            try {
                (function(){
            const form = document.getElementById('tenantMessagePaneForm');
            if (!form) return;

            form.addEventListener('submit', function(e){ e.preventDefault(); window.sendTenantMessage(); });

            window.sendTenantMessage = async function(){
                console.debug('sendTenantMessage() called');
                const btn = document.getElementById('tenantMessageSendBtn');
                const bodyEl = document.getElementById('tenantMessagePaneBody');
                const applicationId = form.querySelector('#tenant_message_application_id').value;
                const messageInput = form.querySelector('#tenant_message_input');
                const attachmentInput = form.querySelector('#tenant_message_attachment');
                const message = (messageInput.value || '').trim();
                const hasAttachment = !!(attachmentInput && attachmentInput.files && attachmentInput.files.length);
                if (!message && !hasAttachment) {
                    console.debug('Nothing to send: no message and no attachment');
                    alert('Please enter a message or attach a file before sending.');
                    return;
                }
                if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
                const tokenInput = form.querySelector('input[name="_token"]');
                const token = tokenInput ? tokenInput.value : null;
                try {
                    const fd = new FormData();
                    fd.append('application_id', applicationId);
                    if (message) fd.append('message', message);
                    if (hasAttachment) fd.append('attachment', attachmentInput.files[0]);

                    console.debug('Posting message to', form.action, { applicationId, message, hasAttachment });

                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Accept':'application/json','X-CSRF-TOKEN': token },
                        body: fd
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(()=>({message:'Failed to send'}));
                        console.debug('Response not ok', err);
                        alert(err.message||'Failed to send');
                        return;
                    }
                    const data = await res.json();
                    console.debug('Message sent, server returned', data);
                    const el = document.createElement('div'); el.className = 'msg me';
                    if (data.message) { const text = document.createElement('div'); text.className = 'text'; text.textContent = data.message; el.appendChild(text); }
                    if (data.attachment_url) { const a = document.createElement('a'); a.href = data.attachment_url; a.target = '_blank'; if (data.attachment_url.match(/\.(jpg|jpeg|png)$/i)) { const img = document.createElement('img'); img.src = data.attachment_url; img.style.maxWidth = '220px'; img.style.display = 'block'; img.style.borderRadius = '6px'; a.appendChild(img); } else { a.textContent = 'View attachment'; } el.appendChild(a); }
                    const meta = document.createElement('span'); meta.className = 'meta'; meta.textContent = data.created_at ? new Date(data.created_at).toLocaleString() : new Date().toLocaleString(); el.appendChild(meta);
                    bodyEl.appendChild(el); bodyEl.scrollTop = bodyEl.scrollHeight; messageInput.value = ''; if (attachmentInput) attachmentInput.value = '';
                } catch (err) { console.error('sendTenantMessage error', err); alert('Network error while sending message'); }
                finally { if (btn) { btn.disabled = false; btn.style.opacity = ''; } }
            };
        })();
            } catch (initErr) {
                console.error('Tenant messaging init failed', initErr);
                window.sendTenantMessage = function(){ alert('Messaging failed to initialize: ' + (initErr && initErr.message ? initErr.message : 'unknown error')); };
            }

            // show selected filename for attachment in tenant pane
            (function(){
                const fileInput = document.getElementById('tenant_message_attachment');
                const nameEl = document.getElementById('tenant_message_attachment_name');
                if (fileInput && nameEl) {
                    fileInput.addEventListener('change', function(){
                        if (fileInput.files && fileInput.files.length) nameEl.textContent = fileInput.files[0].name;
                        else nameEl.textContent = '';
                    });
                }
            })();

        // upload modal removed; attachments handled inside message pane
    </script>
</x-tenant-layout>

<x-host-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
            <h1 style="margin:0">Record Cash Payment</h1>
            <p class="muted">Create a cash transaction that will appear on tenant payment records.</p>
        </div>
        <div>
            <a href="{{ route('host.payments.index') }}" class="btn outline">Back to Payments</a>
        </div>
    </div>

    <div class="card" style="margin-top:12px;">
        <form method="POST" action="{{ route('host.payments.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="field">
                    <label>Property</label>
                    <select name="apartment_id" class="form-input" required>
                        <option value="">Select property</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->title }} — {{ $prop->address }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Tenant (user id)</label>
                    <input name="tenant_user_id" type="number" class="form-input" placeholder="Tenant user id" required />
                </div>

                <div class="field">
                    <label>Amount (₱)</label>
                    <input name="amount" type="number" step="0.01" min="0" class="form-input" required />
                </div>

                <div class="field">
                    <label>Reference (optional)</label>
                    <input name="reference" type="text" class="form-input" />
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:12px;gap:8px;">
                <a href="{{ route('host.payments.index') }}" class="btn ghost">Cancel</a>
                <button type="submit" class="btn">Record Payment</button>
            </div>
        </form>
    </div>
</x-host-layout>

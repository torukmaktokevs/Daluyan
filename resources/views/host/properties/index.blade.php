<x-host-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0">My Properties</h1>
        <div>
            <a href="{{ route('host.properties.create') }}" class="btn">+ Add Property</a>
        </div>
    </div>

    @if($properties->isEmpty())
        <div class="card" style="grid-column:1 / -1;">
            <p class="muted">No properties yet. Click “Add Property” to get started.</p>
        </div>
    @else
        <div class="grid">
            @foreach ($properties as $property)
                @php
                    $cover = $property->files->firstWhere('mime_type', 'like', 'image/%') ?? $property->files->first();
                    $coverUrl = $cover ? asset('storage/'.$cover->path) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&q=60';
                @endphp
                <div class="card" style="grid-column:span 6; padding:0; overflow:hidden;">
                    <div style="position:relative;">
                        <img src="{{ $coverUrl }}" alt="{{ $property->title }}" style="width:100%;height:180px;object-fit:cover;display:block;" />
                        <span style="position:absolute;top:10px;left:10px;background:#0b1627;border:1px solid var(--border);color:var(--text);padding:4px 8px;border-radius:9999px;font-size:12px;text-transform:capitalize;">{{ $property->status }}</span>
                    </div>
                    <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                            <strong style="font-size:16px;">{{ $property->title }}</strong>
                            <div class="accent" style="white-space:nowrap;">₱{{ number_format((float) $property->price, 2) }}</div>
                        </div>
                        <div class="muted" style="font-size:14px;">{{ $property->address }}</div>
                        <div class="muted" style="display:flex;gap:14px;font-size:14px;">
                            <span><strong>{{ $property->bedrooms }}</strong> bd</span>
                            <span><strong>{{ $property->bathrooms }}</strong> ba</span>
                            @if($property->area)
                                <span><strong>{{ $property->area }}</strong> sqm</span>
                            @endif
                        </div>
                        @if(!empty($property->amenities))
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @foreach (array_slice((array) $property->amenities, 0, 4) as $amenity)
                                    <span style="border:1px solid var(--border);padding:2px 6px;border-radius:9999px;font-size:12px;color:var(--muted);">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px;">
                            <a href="{{ route('host.properties.show', $property) }}" class="btn ghost">View</a>
                            <a href="{{ route('host.properties.edit', $property) }}" class="btn">Edit</a>
                            <form method="POST" action="{{ route('host.properties.destroy', $property) }}" style="display:inline-block;" onsubmit="return confirm('Delete this property? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn ghost" style="color:#b91c1c;border-color:transparent;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-host-layout>

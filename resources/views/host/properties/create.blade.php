<x-host-layout>
    <div class="page-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        @php $isEditing = isset($property) && $property; @endphp
        <h1 style="margin:0">{{ $isEditing ? 'Edit Property' : 'Add New Property' }}</h1>
        <a href="{{ route('host.properties.index') }}" class="btn outline">Back to Properties</a>
    </div>

    @if ($errors->any())
        <div class="card" style="border-color:#7f1d1d;color:#fecaca;background:#450a0a;">
            <div style="font-weight:600;margin-bottom:6px;">Please fix the following:</div>
            <ul style="margin:0 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @push('styles')
    <style>
      .form-grid{display:grid;gap:12px;grid-template-columns:repeat(12,minmax(0,1fr))}
      .form-col-6{grid-column:span 6}
      .form-col-12{grid-column:span 12}
            .form-input, .form-textarea, .form-select{width:100%;background:#0b1627;border:1px solid var(--border);color:var(--text);padding:8px 10px;border-radius:8px}
      .form-textarea{min-height:100px}
      .field{display:flex;flex-direction:column;gap:6px}
      .field label{color:var(--muted);font-size:14px}
      .uploader{cursor:pointer;border:1px dashed var(--border);padding:12px;border-radius:10px;text-align:center;color:var(--muted)}
            /* Preview thumbnails: fixed size 80x80 */
            .previews{display:grid;grid-auto-flow:column;grid-auto-columns:80px;gap:8px;margin-bottom:8px;align-items:start}
            .previews > div{width:80px;height:80px;overflow:hidden;border-radius:8px}
            .previews img{width:100%;height:100%;object-fit:cover;display:block}
      .amenity-row{border:1px solid var(--border);border-radius:10px;padding:10px}
      @media (max-width: 1024px){.form-col-6{grid-column:span 12}}
    </style>
    @endpush

    <form action="{{ $isEditing ? route('host.properties.update', $property) : route('host.properties.store') }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;">
        @csrf
        @if($isEditing)
            @method('PUT')
        @endif
        <div class="card">
            <h3 style="margin-top:0">Amenities</h3>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <label class="muted">Add amenities</label>
                <button type="button" id="addAmenityRowBtn" class="btn">+ Add Amenity</button>
            </div>
            <div id="amenityRows" style="display:flex;flex-direction:column;gap:10px;">
                                @php
                                    $oldAmenityNames = old('amenity_names', []);
                                    if (empty($oldAmenityNames) && $isEditing) {
                                            $oldAmenityNames = (array)($property->amenities ?? []);
                                    }
                                @endphp
                <div class="amenity-row" data-amenity-row>
                    <div class="form-grid">
                        <div class="form-col-6 field">
                            <label>Amenity name</label>
                            <input type="text" name="amenity_names[]" placeholder="e.g. Pool" class="form-input">
                        </div>
                        <div class="form-col-6 field">
                            <label>Upload Images (up to 4)</label>
                            <div style="display:flex;align-items:flex-start;gap:10px;">
                                <div style="flex:1">
                                    <input type="file" name="amenity_images[0][]" accept="image/*" multiple class="file-input" data-multi style="display:none">
                                    <div class="previews hidden"></div>
                                    <div class="uploader">Drop up to 4 images here or click to browse<br/><small class="muted">PNG, JPG, WEBP up to 8MB each</small></div>
                                </div>
                                <button type="button" class="remove-amenity-row btn ghost hidden">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach ($oldAmenityNames as $name)
                    <div class="amenity-row" data-amenity-row>
                        <div class="form-grid">
                            <div class="form-col-6 field">
                                <label>Amenity name</label>
                                <input type="text" name="amenity_names[]" value="{{ $name }}" class="form-input">
                            </div>
                            <div class="form-col-6 field">
                                <label>Upload Images (up to 4)</label>
                                <div style="display:flex;align-items:flex-start;gap:10px;">
                                    <div style="flex:1">
                                        <input type="file" name="amenity_images[0][]" accept="image/*" multiple class="file-input" data-multi style="display:none">
                                        <div class="previews hidden"></div>
                                        <div class="uploader">Drop up to 4 images here or click to browse<br/><small class="muted">PNG, JPG, WEBP up to 8MB each</small></div>
                                    </div>
                                    <button type="button" class="remove-amenity-row btn ghost">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0">Property Details</h3>
            <div class="form-grid">
                <div class="form-col-6 field">
                    <label>Property Title</label>
                    <input name="title" type="text" value="{{ old('title', $property->title ?? '') }}" class="form-input" placeholder="Modern 2BR Apartment" required>
                </div>
                <div class="form-col-6 field">
                    <label>Property Location</label>
                    <input name="address" type="text" value="{{ old('address', $property->address ?? '') }}" class="form-input" placeholder="123 Main St, City" required>
                </div>
                <div class="form-col-6 field">
                    <label>Property Type</label>
                    <input name="type" type="text" class="form-input" placeholder="Apartment, Condo, House (optional)" value="{{ old('type', $property->type ?? '') }}">
                </div>
                <div class="form-col-6 field">
                    <label>Price (₱)</label>
                    <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $property->price ?? '') }}" class="form-input" required>
                </div>
                <div class="form-col-6 field">
                    <label>Bedrooms</label>
                    <input name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $property->bedrooms ?? 0) }}" class="form-input" required>
                </div>
                <div class="form-col-6 field">
                    <label>Bathrooms</label>
                    <input name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $property->bathrooms ?? 0) }}" class="form-input" required>
                </div>
                <div class="form-col-6 field">
                    <label>Floor Area (sqm)</label>
                    <input name="area" type="number" min="0" value="{{ old('area', $property->area ?? '') }}" class="form-input">
                </div>
                <div class="form-col-12 field">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="form-textarea" placeholder="Describe the property, nearby landmarks, policies, etc.">{{ old('description', $property->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('host.properties.index') }}" class="btn ghost">Cancel</a>
            <button type="submit" class="btn">Submit</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Amenity rows
            const amenityRows = document.getElementById('amenityRows');
            const addAmenityRowBtn = document.getElementById('addAmenityRowBtn');
            
            function renumberAmenityRows() {
                const rows = amenityRows.querySelectorAll('[data-amenity-row]');
                rows.forEach((row, idx) => {
                    const input = row.querySelector('input.file-input[data-multi]');
                    if (input) input.name = `amenity_images[${idx}][]`;
                });
            }

            function wireUploader(row) {
                const fileInput = row.querySelector('input.file-input[data-multi]');
                const uploader = row.querySelector('.uploader');
                const previewsGrid = row.querySelector('.previews');

                function renderPreviews(files) {
                    previewsGrid.innerHTML = '';
                        const list = Array.from(files).slice(0, 4); // enforce max 4 previews
                        if (list.length === 0) { previewsGrid.classList.add('hidden'); return; }
                        list.forEach(f => {
                            const reader = new FileReader();
                            reader.onload = (ev) => {
                                const wrap = document.createElement('div');
                                wrap.className = 'overflow-hidden rounded-md ring-1 ring-gray-200';
                                // Ensure fixed pixel size for thumbnail
                                wrap.style.width = '80px';
                                wrap.style.height = '80px';
                                wrap.innerHTML = `<img src="${ev.target?.result}" style="width:100%;height:100%;object-fit:cover;" />`;
                                previewsGrid.appendChild(wrap);
                            };
                            reader.readAsDataURL(f);
                        });
                        previewsGrid.classList.remove('hidden');
                }

                uploader?.addEventListener('click', () => fileInput?.click());
                uploader?.addEventListener('dragover', (e) => { e.preventDefault(); uploader.classList.add('border-indigo-400'); });
                uploader?.addEventListener('dragleave', () => uploader.classList.remove('border-indigo-400'));
                uploader?.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploader.classList.remove('border-indigo-400');
                    if (!fileInput) return;
                    const existing = fileInput.files ? Array.from(fileInput.files) : [];
                    const incoming = Array.from(e.dataTransfer?.files || []);
                    const merged = existing.concat(incoming).slice(0, 4);
                    const dt = new DataTransfer();
                    merged.forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                    fileInput.dispatchEvent(new Event('change'));
                });

                fileInput?.addEventListener('change', () => {
                    if (!fileInput.files) { previewsGrid.classList.add('hidden'); return; }
                    // keep only up to 4 files
                    if (fileInput.files.length > 4) {
                        const dt = new DataTransfer();
                        Array.from(fileInput.files).slice(0, 4).forEach(f => dt.items.add(f));
                        fileInput.files = dt.files;
                    }
                    renderPreviews(fileInput.files);
                });

                // Show remove button for non-first amenity rows
                const removeBtn = row.querySelector('.remove-amenity-row');
                if (removeBtn) {
                    removeBtn.classList.toggle('hidden', amenityRows.querySelectorAll('.amenity-row').length === 1);
                    removeBtn.addEventListener('click', () => {
                        if (amenityRows.querySelectorAll('.amenity-row').length > 1) {
                            row.remove();
                            renumberAmenityRows();
                        }
                    });
                }
            }

            // Wire existing rows
            amenityRows.querySelectorAll('.amenity-row').forEach(wireUploader);
            renumberAmenityRows();

            addAmenityRowBtn?.addEventListener('click', () => {
                const template = document.createElement('div');
                template.className = 'amenity-row';
                template.innerHTML = `
                    <div class="form-grid">
                        <div class="form-col-6 field">
                            <label>Amenity name</label>
                            <input type="text" name="amenity_names[]" placeholder="e.g. Parking" class="form-input">
                        </div>
                        <div class="form-col-6 field">
                            <label>Upload Images (up to 4)</label>
                            <div style="display:flex;align-items:flex-start;gap:10px;">
                                <div style="flex:1">
                                    <input type="file" name="amenity_images[0][]" accept="image/*" multiple class="file-input" data-multi style="display:none">
                                    <div class="previews hidden"></div>
                                    <div class="uploader">Drop up to 4 images here or click to browse<br/><small class="muted">PNG, JPG, WEBP up to 8MB each</small></div>
                                </div>
                                <button type="button" class="remove-amenity-row btn ghost">Remove</button>
                            </div>
                        </div>
                    </div>`;
                amenityRows.appendChild(template);
                wireUploader(template);
                renumberAmenityRows();
            });
        });
    </script>
</x-host-layout>

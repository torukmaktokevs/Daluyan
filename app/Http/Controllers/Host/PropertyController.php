<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        // TODO: load host's properties when schema is ready
        $properties = Apartment::query()
            ->with('files')
            ->where('host_user_id', Auth::id())
            ->latest()
            ->get();
        return view('host.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('host.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'address' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'bedrooms' => ['required','integer','min:0'],
            'bathrooms' => ['required','integer','min:0'],
            'area' => ['nullable','integer','min:0'],
            // Amenity rows: amenity_names[] text paired with amenity_images[] file
            'amenity_names' => ['array'],
            'amenity_names.*' => ['nullable','string','max:100'],
            'amenity_images' => ['array'],           // rows
            'amenity_images.*' => ['array','max:4'], // up to 4 files per amenity row
            'amenity_images.*.*' => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:8192'],
            // Back-compat in case amenities[] is sent (chips)
            'amenities' => ['array'],
            'amenities.*' => ['nullable','string','max:100'],
            'part_labels' => ['array'],
            'part_labels.*' => ['nullable','string','max:100'],
            'photos' => ['array'],
            'photos.*' => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:8192'],
        ], [
            'amenity_images.*.*.file' => 'Some amenity images failed to upload. Ensure each file is under 8MB and increase PHP upload_max_filesize and post_max_size if needed.',
            'amenity_images.*.max' => 'Each amenity can have at most 4 images.',
            'photos.*.file' => 'One of the property photos failed to upload. Ensure file size limits are sufficient.',
        ]);

        // Create the apartment (property)
        $apartment = Apartment::create([
            'host_user_id' => Auth::id(),
            'title' => $validated['title'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'area' => $validated['area'] ?? null,
            'amenities' => array_values(array_filter(
                $validated['amenity_names'] ?? $validated['amenities'] ?? []
            )),
            'status' => 'available',
        ]);

        // Ensure a DocumentType exists for property photos
        $docType = DocumentType::firstOrCreate(
            ['name' => 'Property Photo'],
            [
                'description' => 'Photos uploaded by hosts for their properties',
                'is_required' => false,
                'allowed_extensions' => ['jpg','jpeg','png','webp'],
                'max_size' => 8192,
            ]
        );

        // Ensure a DocumentType exists for amenity photos
        $amenityDocType = DocumentType::firstOrCreate(
            ['name' => 'Amenity Photo'],
            [
                'description' => 'Amenity images for properties',
                'is_required' => false,
                'allowed_extensions' => ['jpg','jpeg','png','webp'],
                'max_size' => 8192,
            ]
        );

        // Handle image uploads with part labels
        $labels = $validated['part_labels'] ?? [];
        $photos = $request->file('photos', []);

        foreach ($photos as $i => $photo) {
            if (!$photo) { continue; }
            $label = $labels[$i] ?? null;

            $storedPath = $photo->store('property-photos/'.Auth::id(), 'public');
            $original = $photo->getClientOriginalName();
            $extension = $photo->getClientOriginalExtension();
            $mime = $photo->getClientMimeType();
            $sizeKb = round($photo->getSize() / 1024, 2);

            // Create via relation so morph columns are set
            $apartment->files()->create([
                'name' => pathinfo($original, PATHINFO_FILENAME),
                'original_name' => $original,
                'path' => $storedPath,
                'extension' => $extension,
                'size' => $sizeKb,
                'mime_type' => $mime,
                'document_type_id' => $docType->id,
                'uploaded_by' => Auth::id(),
                'description' => $label,
                'is_public' => true,
            ]);
        }

        // Handle amenity image uploads (paired with amenity_names)
        $amenityNames = $validated['amenity_names'] ?? [];
        $amenityImages = $request->file('amenity_images', []);
        foreach ($amenityImages as $i => $images) {
            $label = $amenityNames[$i] ?? 'Amenity';
            foreach (($images ?? []) as $image) {
                if (!$image) { continue; }
                $storedPath = $image->store('property-amenities/'.Auth::id(), 'public');
                $original = $image->getClientOriginalName();
                $extension = $image->getClientOriginalExtension();
                $mime = $image->getClientMimeType();
                $sizeKb = round($image->getSize() / 1024, 2);

                $apartment->files()->create([
                    'name' => pathinfo($original, PATHINFO_FILENAME),
                    'original_name' => $original,
                    'path' => $storedPath,
                    'extension' => $extension,
                    'size' => $sizeKb,
                    'mime_type' => $mime,
                    'document_type_id' => $amenityDocType->id,
                    'uploaded_by' => Auth::id(),
                    'description' => $label,
                    'is_public' => true,
                ]);
            }
        }

        return redirect()->route('host.properties.index')
            ->with('success', 'Property created successfully.');
    }

    public function show(Apartment $property)
    {
        // For a quick host-side view, redirect to the tenant-facing apartment page
        return redirect()->route('tenant.apartments.show', $property);
    }

    public function edit(Apartment $property)
    {
        // Ensure owner
        if ($property->host_user_id !== Auth::id()) {
            abort(403);
        }
        return view('host.properties.create', ['property' => $property]);
    }

    public function update(Request $request, Apartment $property)
    {
        if ($property->host_user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'address' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'bedrooms' => ['required','integer','min:0'],
            'bathrooms' => ['required','integer','min:0'],
            'area' => ['nullable','integer','min:0'],
            'amenities' => ['array'],
            'amenities.*' => ['nullable','string','max:100'],
        ]);

        $property->update([
            'title' => $validated['title'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'area' => $validated['area'] ?? null,
            'amenities' => array_values(array_filter($validated['amenities'] ?? [])),
        ]);

        return redirect()->route('host.properties.index')->with('success', 'Property updated.');
    }

    public function destroy(Apartment $property)
    {
        if ($property->host_user_id !== Auth::id()) {
            abort(403);
        }
        // Remove associated files if any
        try {
            foreach ($property->files as $f) {
                if ($f->path) {
                    Storage::disk('public')->delete($f->path);
                }
                $f->delete();
            }
        } catch (\Exception $e) {
            // continue with delete
        }
        $property->delete();
        return redirect()->route('host.properties.index')->with('success', 'Property deleted.');
    }
}

<?php
// app/Http/Controllers/FileController.php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\DocumentType;
use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Display a listing of the files.
     * URL: GET /files
     */
    public function index(Request $request)
    {
        // Start building our query
        $query = File::with(['documentType', 'uploadedBy', 'fileable']);

        // Filter by document type if provided
        if ($request->has('type') && $request->type) {
            $query->whereHas('documentType', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->type . '%');
            });
        }

        // Filter by entity type (apartment, tenant, lease)
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('fileable_type', $request->entity_type);
        }

        // Get paginated results (20 per page)
        $files = $query->latest()->paginate(20);

        return view('files.index', compact('files'));
    }

    /**
     * Show the form for creating a new file.
     * URL: GET /files/create
     */
    public function create()
    {
        // Get all data needed for the upload form
        $documentTypes = DocumentType::all();
        $apartments = Apartment::all();
        $tenants = Tenant::with('user')->get();
        $leases = Lease::with(['apartment', 'tenant.user'])->get();

        return view('files.create', compact('documentTypes', 'apartments', 'tenants', 'leases'));
    }

    /**
     * Store a newly uploaded file.
     * URL: POST /files
     */
    public function store(Request $request)
    {
        // Step 1: Validate the form data
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max file size
            'document_type_id' => 'required|exists:document_types,id',
            'fileable_type' => 'required|in:App\Models\Apartment,App\Models\Tenant,App\Models\Lease',
            'fileable_id' => 'required|integer',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'expires_at' => 'nullable|date|after:today'
        ]);

        // Step 2: Check if the entity (apartment/tenant/lease) exists
        $modelClass = $validated['fileable_type'];
        $entity = $modelClass::findOrFail($validated['fileable_id']);

        // Step 3: Get document type restrictions
        $documentType = DocumentType::findOrFail($validated['document_type_id']);

        // Step 4: Validate file against document type rules
        $file = $request->file('file');
        $allowedExtensions = $documentType->allowed_extensions ?? ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $maxSize = $documentType->max_size ? $documentType->max_size * 1024 : 10240; // Convert KB to bytes

        // Check file extension
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
            return back()->withErrors(['file' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)]);
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            return back()->withErrors(['file' => 'File too large. Maximum size: ' . ($documentType->max_size ?? 10) . 'MB']);
        }

        // Step 5: Generate unique filename and storage path
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension; // Random name for security
        $path = 'files/' . date('Y/m'); // Organize by year/month

        // Step 6: Store the physical file
        $filePath = $file->storeAs($path, $filename, 'local');

        // Step 7: Create database record
        $fileRecord = File::create([
            'name' => $filename,
            'original_name' => $originalName,
            'path' => $filePath,
            'extension' => $extension,
            'size' => $file->getSize() / 1024, // Convert to KB
            'mime_type' => $file->getMimeType(),
            'document_type_id' => $validated['document_type_id'],
            'fileable_type' => $validated['fileable_type'],
            'fileable_id' => $validated['fileable_id'],
            'uploaded_by' => auth()->id(),
            'description' => $validated['description'],
            'is_public' => $validated['is_public'] ?? false,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('files.index')
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Display the specified file.
     * URL: GET /files/{file}
     */
    public function show(File $file)
    {
        // Load relationships for the view
        $file->load(['documentType', 'uploadedBy', 'fileable', 'shares']);
        return view('files.show', compact('file'));
    }

    /**
     * Download the specified file.
     * URL: GET /files/{file}/download
     */
    public function download(File $file)
    {
        // Check if file exists in storage
        if (!Storage::disk('local')->exists($file->path)) {
            abort(404, 'File not found.');
        }

        // Check permissions
        if (!$file->is_public && !auth()->check()) {
            abort(403, 'Unauthorized access.');
        }

        // Return file download response
        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    /**
     * Preview the file (for images and PDFs).
     * URL: GET /files/{file}/preview
     */
    public function preview(File $file)
    {
        if (!Storage::disk('local')->exists($file->path)) {
            abort(404, 'File not found.');
        }

        if (!$file->is_public && !auth()->check()) {
            abort(403, 'Unauthorized access.');
        }

        // For images and PDFs, show in browser
        if ($file->isImage() || $file->isPdf()) {
            return response()->file(Storage::disk('local')->path($file->path));
        }

        // For other files, force download
        return $this->download($file);
    }

    /**
     * Remove the specified file.
     * URL: DELETE /files/{file}
     */
    public function destroy(File $file)
    {
        // Delete physical file from storage
        Storage::disk('local')->delete($file->path);

        // Delete database record
        $file->delete();

        return redirect()->route('files.index')
            ->with('success', 'File deleted successfully.');
    }
}
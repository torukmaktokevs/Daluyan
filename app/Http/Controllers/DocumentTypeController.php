<?php
// app/Http/Controllers/DocumentTypeController.php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of document types.
     */
    public function index()
    {
        $documentTypes = DocumentType::latest()->paginate(10);
        return view('document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new document type.
     */
    public function create()
    {
        return view('document-types.create');
    }

    /**
     * Store a newly created document type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name',
            'description' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'allowed_extensions' => 'nullable|array',
            'allowed_extensions.*' => 'string|max:10',
            'max_size' => 'nullable|integer|min:1|max:10240' // Max 10MB
        ]);

        DocumentType::create($validated);

        return redirect()->route('document-types.index')
            ->with('success', 'Document type created successfully.');
    }

    /**
     * Show the form for editing the specified document type.
     */
    public function edit(DocumentType $documentType)
    {
        return view('document-types.edit', compact('documentType'));
    }

    /**
     * Update the specified document type.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id,
            'description' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'allowed_extensions' => 'nullable|array',
            'allowed_extensions.*' => 'string|max:10',
            'max_size' => 'nullable|integer|min:1|max:10240'
        ]);

        $documentType->update($validated);

        return redirect()->route('document-types.index')
            ->with('success', 'Document type updated successfully.');
    }

    /**
     * Remove the specified document type.
     */
    public function destroy(DocumentType $documentType)
    {
        // Prevent deletion if there are associated files
        if ($documentType->files()->exists()) {
            return redirect()->route('document-types.index')
                ->with('error', 'Cannot delete document type that has files associated.');
        }

        $documentType->delete();

        return redirect()->route('document-types.index')
            ->with('success', 'Document type deleted successfully.');
    }
}
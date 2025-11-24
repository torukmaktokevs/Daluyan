<?php
// app/Http/Controllers/FileShareController.php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileShareController extends Controller
{
    /**
     * Create a shareable link for a file.
     */
    public function store(Request $request, File $file)
    {
        $validated = $request->validate([
            'expires_at' => 'nullable|date|after:now',
            'max_downloads' => 'nullable|integer|min:1',
            'password' => 'nullable|string|min:3'
        ]);

        $fileShare = FileShare::create([
            'file_id' => $file->id,
            'expires_at' => $validated['expires_at'] ?? null,
            'max_downloads' => $validated['max_downloads'] ?? null,
            'password' => $validated['password'] ? bcrypt($validated['password']) : null,
        ]);

        return back()->with('success', 'Shareable link created successfully.')
            ->with('share_url', url("/shared/{$fileShare->token}"));
    }

    /**
     * Download a file via share link.
     */
    public function download($token)
    {
        $fileShare = FileShare::where('token', $token)->firstOrFail();

        if (!$fileShare->canBeDownloaded()) {
            abort(403, 'This share link is no longer valid.');
        }

        // Increment download count
        $fileShare->incrementDownloadCount();

        return Storage::disk('local')->download(
            $fileShare->file->path, 
            $fileShare->file->original_name
        );
    }

    /**
     * Revoke a share link.
     */
    public function destroy(FileShare $fileShare)
    {
        $fileShare->update(['is_active' => false]);

        return back()->with('success', 'Share link revoked successfully.');
    }
}
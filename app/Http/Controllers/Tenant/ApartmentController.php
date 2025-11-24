<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ApartmentReview;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApartmentController extends Controller
{
    public function show(Apartment $apartment)
    {
        $apartment->load(['files', 'host']);

        // Separate images for gallery; all image files
        $images = $apartment->files->filter(function ($f) {
            return str_starts_with($f->mime_type ?? '', 'image/');
        })->values();

        return view('tenant.apartments.show', [
            'apartment' => $apartment,
            'images' => $images,
        ]);
    }

    public function myApartment()
    {
        $application = ApartmentApplication::query()
            ->with(['apartment.files', 'host'])
            ->where('tenant_user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->first();

        $apartment = $application?->apartment;
        $images = collect();
        if ($apartment) {
            $images = $apartment->files->filter(function ($f) {
                return str_starts_with($f->mime_type ?? '', 'image/');
            })->values();
        }

        return view('tenant.my-apartment', [
            'application' => $application,
            'apartment' => $apartment,
            'images' => $images,
        ]);
    }

    /**
     * Handle tenant move-out: save rating/comment and mark application moved out.
     */
    public function moveOut(Request $request, Apartment $apartment)
    {
        $data = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $userId = Auth::id();

        $application = ApartmentApplication::query()
            ->where('apartment_id', $apartment->id)
            ->where('tenant_user_id', $userId)
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found or not approved'], 404);
        }

        $reviewSaved = false;

        // Use a transaction to ensure application status and apartment status updates are consistent.
        DB::beginTransaction();
        try {
            // create review record if rating/comment provided
            if (!empty($data['rating']) || !empty($data['comment'])) {
                try {
                    $review = ApartmentReview::create([
                        'apartment_id' => $apartment->id,
                        'tenant_user_id' => $userId,
                        'rating' => $data['rating'] ?? null,
                        'comment' => $data['comment'] ?? null,
                    ]);
                    if ($review && $review->id) {
                        $reviewSaved = true;
                    }
                } catch (\Throwable $e) {
                    // Log and continue — user can run migrations to enable reviews
                    Log::error('Failed to save apartment review during moveOut: ' . $e->getMessage());
                    $reviewSaved = false;
                }
            }

            // mark application as moved out
            $application->status = 'moved_out';
            $application->save();

            // If a review was successfully saved, mark apartment as available
            if ($reviewSaved) {
                try {
                    $apartment->status = 'available';
                    $apartment->save();
                } catch (\Throwable $e) {
                    Log::error('Failed to update apartment status during moveOut: ' . $e->getMessage());
                }
            }

            DB::commit();
            return response()->json(['message' => 'Moved out successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('moveOut transaction failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to move out'], 500);
        }
    }

    /**
     * Store a review without moving out.
     */
    public function storeReview(Request $request, Apartment $apartment)
    {
        $data = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        try {
            $review = ApartmentReview::create([
                'apartment_id' => $apartment->id,
                'tenant_user_id' => Auth::id(),
                'rating' => $data['rating'] ?? null,
                'comment' => $data['comment'] ?? null,
            ]);
            return response()->json(['message' => 'Review saved', 'review_id' => $review->id]);
        } catch (\Throwable $e) {
            Log::error('Failed to save apartment review: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to save review'], 500);
        }
    }
}

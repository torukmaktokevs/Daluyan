<?php
// routes/web.php - TEMPORARY VERSION FOR FILE SYSTEM TESTING

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FileShareController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\HostRequestController;
use App\Http\Controllers\Admin\HostRequestController as AdminHostRequestController;
use App\Models\HostRequest as HostRequestModel;
use App\Models\User;
use App\Models\Apartment;
use App\Http\Controllers\Tenant\ApartmentController as TenantApartmentController;


// Public routes
Route::get('/', function () {
    $featuredApartments = \App\Models\Apartment::query()
        ->with('files')
        ->where(function($q){
            $q->whereNull('status')->orWhere('status', '!=', 'unavailable');
        })
        ->latest()
        ->take(6)
        ->get();
    return view('welcome', compact('featuredApartments'));
});

// Authentication protected routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
     // Tenant browsing
    Route::get('/tenant/browsing', function () {
        $apartments = Apartment::query()
            ->with('files')
            ->where(function($q){
                $q->whereNull('status')->orWhere('status', '!=', 'unavailable');
            })
            ->latest()
            ->paginate(18);
        return view('tenant.browsing', compact('apartments'));
    })->name('tenant.browsing');

    // Tenant apartment details
    Route::get('/tenant/apartments/{apartment}', [TenantApartmentController::class, 'show'])
        ->name('tenant.apartments.show');
    Route::get('/tenant/apartments/{apartment}/apply', [\App\Http\Controllers\Tenant\ApartmentApplicationController::class, 'create'])
        ->name('tenant.apartments.apply.create');
    Route::post('/tenant/apartments/{apartment}/apply', [\App\Http\Controllers\Tenant\ApartmentApplicationController::class, 'store'])
        ->name('tenant.apartments.apply.store');
    // Tenant application status list
    Route::get('/tenant/applications', [\App\Http\Controllers\Tenant\ApartmentApplicationController::class, 'index'])
        ->name('tenant.applications.index');
    Route::patch('/tenant/applications/{application}/cancel', [\App\Http\Controllers\Tenant\ApartmentApplicationController::class, 'cancel'])
        ->name('tenant.applications.cancel');
    // Tenant application messages (minimal closures to satisfy view routes)
    // Tenant application messages (returns messages and supports attachments)
    Route::get('/tenant/applications/{application}/messages', function ($application) {
        $user = auth()->user();
        $app = \App\Models\ApartmentApplication::findOrFail($application);
        if ($app->tenant_user_id !== $user->id) {
            abort(403);
        }
        $messages = \App\Models\ApplicationMessage::where('application_id', $app->id)->orderBy('created_at')->get()->map(function($m) use ($user){
            return [
                'id' => $m->id,
                'message' => $m->message,
                'attachment_url' => $m->attachment ? asset('storage/'.$m->attachment) : null,
                'from_tenant' => $m->from_user_id === $user->id,
                'created_at' => $m->created_at->toDateTimeString(),
            ];
        });
        return response()->json($messages);
    })->name('tenant.applications.messages');

    Route::post('/tenant/messages/send', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'application_id' => 'required|integer',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $app = \App\Models\ApartmentApplication::findOrFail($data['application_id']);
        if ($app->tenant_user_id !== auth()->id()) {
            abort(403);
        }
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('application_messages', 'public');
        }
        $msg = \App\Models\ApplicationMessage::create([
            'application_id' => $app->id,
            'from_user_id' => auth()->id(),
            'to_user_id' => $app->host_user_id,
            'message' => $data['message'] ?? '',
            'attachment' => $attachmentPath,
        ]);
        return response()->json([
            'id' => $msg->id,
            'message' => $msg->message,
            'attachment_url' => $msg->attachment ? asset('storage/'.$msg->attachment) : null,
            'from_tenant' => true,
            'created_at' => $msg->created_at->toDateTimeString(),
        ]);
    })->name('tenant.messages.send');
    // Tenant maintenance
    Route::get('/tenant/maintenance', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'index'])->name('tenant.maintenance.index');
    Route::post('/tenant/maintenance', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'store'])->name('tenant.maintenance.store');
    // Contact host for a given apartment
    Route::post('/tenant/apartments/{apartment}/contact', [\App\Http\Controllers\Tenant\ContactHostController::class, 'store'])
        ->name('tenant.apartments.contact');

    // Tenant move-out (leave rating + comment and mark application moved out)
    Route::post('/tenant/apartments/{apartment}/move-out', [\App\Http\Controllers\Tenant\ApartmentController::class, 'moveOut'])
        ->name('tenant.apartments.moveout');

    // Tenant post a review (rating + comment) without moving out
    Route::post('/tenant/apartments/{apartment}/reviews', [\App\Http\Controllers\Tenant\ApartmentController::class, 'storeReview'])
        ->name('tenant.apartments.reviews.store');

    // Tenant payments (monitoring-only)
    Route::get('/tenant/payments', function () {
        $payments = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('payments')) {
                $payments = \Illuminate\Support\Facades\DB::table('payments')
                    ->where('tenant_user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            $payments = collect();
        }
        return view('tenant.payments.index', compact('payments'));
    })->name('tenant.payments.index');
    
    // Other tenant pages
    Route::get('/tenant/documents', [TenantDashboardController::class, 'documents'])->name('tenant.documents');
    Route::get('/tenant/lease', [TenantDashboardController::class, 'lease'])->name('tenant.lease');

    // Temporary dashboard - simple version
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // My Profile dashboard
    Route::get('/profile', [ProfileController::class, 'dashboard'])->name('profile.dashboard');
    // Tenant-scoped alias for profile dashboard
    Route::get('/tenant/profile/dashboard', [ProfileController::class, 'dashboard'])->name('tenant.profile.dashboard');
    // Tenant approved apartment page
    Route::get('/tenant/my-apartment', [TenantApartmentController::class, 'myApartment'])->name('tenant.my-apartment');

    // Host onboarding
    Route::get('/host/become', function () {
        return view('host.personal-info');
    })->name('host.personal');
    Route::post('/host/become', [HostRequestController::class, 'store'])->name('host.requests.store');

    // Approved Host area
    Route::prefix('host')->name('host.')->middleware('host')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Host\DashboardController::class, 'index'])->name('dashboard');

        // Properties
    Route::get('/properties', [\App\Http\Controllers\Host\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [\App\Http\Controllers\Host\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [\App\Http\Controllers\Host\PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [\App\Http\Controllers\Host\PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/edit', [\App\Http\Controllers\Host\PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [\App\Http\Controllers\Host\PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [\App\Http\Controllers\Host\PropertyController::class, 'destroy'])->name('properties.destroy');

        // Tenants
    Route::get('/tenants', [\App\Http\Controllers\Host\TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{application}', [\App\Http\Controllers\Host\TenantController::class, 'show'])->name('tenants.show');
    Route::delete('/tenants/{application}', [\App\Http\Controllers\Host\TenantController::class, 'remove'])->name('tenants.remove');
        Route::patch('/tenants/{application}/movein', [\App\Http\Controllers\Host\TenantController::class, 'setMoveInDate'])->name('tenants.movein');

        // Applications
    Route::get('/applications', [\App\Http\Controllers\Host\ApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}/approve', [\App\Http\Controllers\Host\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::patch('/applications/{application}/decline', [\App\Http\Controllers\Host\ApplicationController::class, 'decline'])->name('applications.decline');

        // Messages for applications (host)
    Route::get('/applications/{application}/messages', [\App\Http\Controllers\Host\ApplicationMessageController::class, 'index'])->name('applications.messages');
    Route::post('/messages/send', [\App\Http\Controllers\Host\ApplicationMessageController::class, 'store'])->name('messages.send');

        // Maintenance
        Route::get('/maintenance', [\App\Http\Controllers\Host\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::patch('/maintenance/{maintenanceRequest}/resolve', [\App\Http\Controllers\Host\MaintenanceController::class, 'resolve'])->name('maintenance.resolve');

        // Host payments: record cash transactions (monitoring)
        Route::get('/payments', [\App\Http\Controllers\Host\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [\App\Http\Controllers\Host\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [\App\Http\Controllers\Host\PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\Host\PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/host-requests', [AdminHostRequestController::class, 'index'])->name('host-requests.index');
        Route::patch('/host-requests/{hostRequest}/approve', [AdminHostRequestController::class, 'approve'])->name('host-requests.approve');
        Route::patch('/host-requests/{hostRequest}/reject', [AdminHostRequestController::class, 'reject'])->name('host-requests.reject');

        // Admin Users: Tenants & Hosts placeholder pages
        Route::get('/users/tenants', function () {
            // Tenants: users who are not admins AND not approved hosts
            $tenants = User::query()
                ->where('is_admin', false)
                ->whereNotIn('id', HostRequestModel::select('user_id')->where('status', HostRequestModel::STATUS_APPROVED))
                ->latest()
                ->paginate(15);
            return view('admin.users.tenants.index', compact('tenants'));
        })->name('users.tenants.index');

        Route::get('/users/hosts', function () {
            $approved = HostRequestModel::with('user')
                ->where('status', HostRequestModel::STATUS_APPROVED)
                ->latest()
                ->paginate(15);
            return view('admin.users.hosts.index', compact('approved'));
        })->name('users.hosts.index');
    });
    
    // COMMENT OUT PROBLEMATIC ROUTES FOR NOW
    // We'll add these back when we create the controllers
    // Route::resource('apartments', ApartmentController::class);
    // Route::resource('tenants', TenantController::class);
    // Route::resource('leases', LeaseController::class);
    // Route::resource('payments', PaymentController::class);
    
    // FILE MANAGEMENT ROUTES (these should work)
    Route::prefix('files')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('files.index');
        Route::get('/create', [FileController::class, 'create'])->name('files.create');
        Route::post('/', [FileController::class, 'store'])->name('files.store');
        Route::get('/{file}', [FileController::class, 'show'])->name('files.show');
        Route::get('/{file}/download', [FileController::class, 'download'])->name('files.download');
        Route::get('/{file}/preview', [FileController::class, 'preview'])->name('files.preview');
        Route::delete('/{file}', [FileController::class, 'destroy'])->name('files.destroy');
        
        Route::post('/{file}/share', [FileShareController::class, 'store'])->name('files.share');
        Route::delete('/shares/{fileShare}', [FileShareController::class, 'destroy'])->name('files.shares.destroy');
    });

    // Document type management routes
    Route::resource('document-types', DocumentTypeController::class)->except(['show']);
});

// Public file sharing route (no auth required)
Route::get('/shared/{token}', [FileShareController::class, 'download'])->name('files.shared.download');
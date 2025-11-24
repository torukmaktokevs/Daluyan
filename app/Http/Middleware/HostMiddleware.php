<?php

namespace App\Http\Middleware;

use App\Models\HostRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HostMiddleware
{
    /**
     * Ensure the authenticated user is an approved host.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $isApproved = HostRequest::where('user_id', $user->id)
            ->where('status', HostRequest::STATUS_APPROVED)
            ->exists();

        if (!$isApproved) {
            abort(403, 'Only approved hosts can access this area.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsurePermissionLike
{
    /**
     * Handle an incoming request.
     * Usage: middleware('permission-like:permission-prefix')
     * This middleware allows access if user has any permission that starts with the given prefix
     */
    public function handle(Request $request, Closure $next, string $permissionPrefix)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Access denied. Please login first.');
        }

        // Check if user has any permission that matches the prefix
        if (!$user->hasPermissionLike($permissionPrefix)) {
            abort(403, "Access denied. You don't have permission with prefix: {$permissionPrefix}");
        }

        return $next($request);
    }
}

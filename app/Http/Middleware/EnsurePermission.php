<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     * Usage: middleware('permission:permission-name')
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Access denied. Please login first.');
        }

        // Check if user has the specific permission
        if (!$user->hasPermissionTo($permission)) {
            abort(403, "Access denied. You don't have permission: {$permission}");
        }

        return $next($request);
    }
}

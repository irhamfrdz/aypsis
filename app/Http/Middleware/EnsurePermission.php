<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     * Usage: middleware('permission:permission-name')
     */
    public function handle(Request $request, Closure $next, string $permissions)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Access denied. Please login first.');
        }

        // Support multiple permissions separated by | (OR logic)
        $permissionArray = explode('|', $permissions);
        $hasPermission = false;

        foreach ($permissionArray as $perm) {
            if ($user->hasPermissionTo(trim($perm))) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, "Access denied. You don't have permission: " . str_replace('|', ' or ', $permissions));
        }

        return $next($request);
    }
}
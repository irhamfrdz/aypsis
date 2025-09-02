<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $has = $user->roles()->where('name', $role)->exists();
        if (!$has) {
            abort(403);
        }

        return $next($request);
    }
}

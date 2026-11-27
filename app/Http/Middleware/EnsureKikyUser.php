<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKikyUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $name = strtolower((string) ($user->name ?? ''));

        if (! $user?->isSuperAdmin() && $name !== 'kiky') {
            abort(403, 'Fitur ini hanya dapat diakses oleh user Admin atau Kiky.');
        }

        return $next($request);
    }
}

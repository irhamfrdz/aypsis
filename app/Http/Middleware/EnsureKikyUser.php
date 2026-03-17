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
        $username = strtolower((string) ($user->username ?? ''));
        $name = strtolower((string) ($user->name ?? ''));

        if ($username !== 'kiky' && $name !== 'kiky') {
            abort(403, 'Fitur ini hanya dapat diakses oleh user Kiky.');
        }

        return $next($request);
    }
}
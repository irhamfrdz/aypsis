<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class EnsureKaryawanPresent
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && empty($user->karyawan_id)) {
            // Allow access to logout and karyawan creation routes.
            // Use Route::has() to avoid calling route() for non-existent names which throws.
            $allowed = [];

            try {
                $allowed[] = route('logout');
            } catch (\InvalidArgumentException $e) {
                // ignore
            }


            // Prefer the onboarding (non-master) create route for users without karyawan
            if (Route::has('karyawan.create')) {
                $allowed[] = route('karyawan.create');
            }

            if (Route::has('master.karyawan.create')) {
                $allowed[] = route('master.karyawan.create');
            }

            $current = $request->url();
            if (!in_array($current, array_filter($allowed))) {
                // Prefer existing named route for redirect - prefer onboarding route first
                Log::warning('EnsureKaryawanPresent blocking user without karyawan', [
                    'user_id' => $user->id ?? null,
                    'route' => optional($request->route())->getName(),
                    'url' => $request->fullUrl(),
                ]);
                if (Route::has('karyawan.create')) {
                    return redirect()->route('karyawan.create');
                }
                if (Route::has('master.karyawan.create')) {
                    return redirect()->route('master.karyawan.create');
                }

                // Fallback: abort with 403 if no create route exists
                return abort(403, 'Karyawan creation route not found.');
            }
        }
        return $next($request);
    }
}

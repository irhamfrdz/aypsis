<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->status !== 'approved') {
            // Allow user to complete profile, logout and auth routes
            $allowedNamed = [
                // prefer master.karyawan.create but accept karyawan.create if present
                'master.karyawan.create',
                'karyawan.create',
                'master.karyawan.store',
                'karyawan.store',
                'karyawan.crew-checklist-new',
                'master.karyawan.crew-checklist-new',
                'karyawan.crew-checklist.update',
                'master.karyawan.crew-checklist.update',
                'logout',
                'password.change',
            ];

            $routeName = optional($request->route())->getName();
            if (!in_array($routeName, $allowedNamed)) {
                Log::warning('EnsureUserApproved blocking non-approved user', [
                    'user_id' => $user->id ?? null,
                    'status' => $user->status ?? null,
                    'route' => $routeName,
                    'url' => $request->fullUrl(),
                ]);
                // redirect to the existing create route, preferring master prefixed route
                if (Route::has('master.karyawan.create')) {
                    return redirect()->route('master.karyawan.create')->with('error', 'Akun belum disetujui oleh admin.');
                }
                if (Route::has('karyawan.create')) {
                    return redirect()->route('karyawan.create')->with('error', 'Akun belum disetujui oleh admin.');
                }

                return abort(403, 'Akun belum disetujui oleh admin.');
            }
        }
        return $next($request);
    }
}

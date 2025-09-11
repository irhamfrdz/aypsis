<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureCrewChecklistComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // Checklist tidak wajib lengkap, hanya pastikan default item ada (jika ingin tetap generate item default)
        if ($user && method_exists($user, 'karyawan') && $user->karyawan) {
            $k = $user->karyawan;
            if (method_exists($k, 'isAbk') && $k->isAbk()) {
                // Pastikan default checklist item sudah ada, tapi tidak paksa redirect jika belum lengkap
                $defaults = \App\Models\CrewEquipment::getDefaultItems();
                $have = $k->crewChecklists()->pluck('item_name')->toArray();
                $missing = array_diff($defaults, $have);
                if (!empty($missing)) {
                    foreach ($missing as $item) {
                        $k->crewChecklists()->create([
                            'item_name' => $item,
                            'status' => 'tidak'
                        ]);
                    }
                }
            }
        }
        return $next($request);
    }
}

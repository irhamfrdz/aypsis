<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiSyncController extends Controller
{
    /**
     * Endpoint untuk menerima data absensi secara batch (JSON) dari lokal
     */
    public function push(Request $request)
    {
        // Simple security check (Authorization header or custom secret)
        $secret = config('app.sync_secret', 'aypsis-sync-12345');
        if ($request->header('X-Sync-Secret') !== $secret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'mesin_id' => 'required|integer',
            'logs' => 'required|array',
            'logs.*.nik' => 'required|string',
            'logs.*.waktu' => 'required|date_format:Y-m-d H:i:s',
            'logs.*.tipe' => 'required|string|in:Masuk,Pulang'
        ]);

        $mesinId = $request->mesin_id;
        $logs = $request->logs;

        // Cache existing logs to avoid N+1 queries
        $existingLogs = Absensi::select('nik', 'waktu', 'tipe')
            ->whereDate('waktu', '>=', Carbon::now()->subMonths(1)) // only check recent to save memory
            ->get()
            ->mapWithKeys(function ($item) {
                $timeStr = $item->waktu instanceof Carbon 
                    ? $item->waktu->format('Y-m-d H:i:s') 
                    : Carbon::parse($item->waktu)->format('Y-m-d H:i:s');
                return [$item->nik . '_' . $timeStr . '_' . $item->tipe => true];
            })
            ->toArray();

        // Cache employees
        $employees = Karyawan::select('id', 'nik')
            ->whereNotNull('nik')
            ->get()
            ->pluck('id', 'nik')
            ->toArray();

        $syncedCount = 0;
        
        foreach ($logs as $log) {
            $nik = trim($log['nik']);
            if (is_numeric($nik)) {
                $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
            }
            $type = $log['tipe'];
            $logTime = Carbon::parse($log['waktu'])->format('Y-m-d H:i:s');

            $key = $nik . '_' . $logTime . '_' . $type;
            if (isset($existingLogs[$key])) {
                continue; // Skip if already exists
            }

            Absensi::create([
                'nik' => $nik,
                'waktu' => $logTime,
                'tipe' => $type,
                'karyawan_id' => $employees[$nik] ?? null,
                'mesin_id' => $mesinId,
                'keterangan' => 'Sinkronisasi otomatis (API PUSH)',
            ]);

            $existingLogs[$key] = true;
            $syncedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Sinkronisasi berhasil! {$syncedCount} data absensi baru telah diimpor.",
            'synced_count' => $syncedCount
        ]);
    }

    /**
     * Endpoint untuk menarik data absensi terbaru dari server online ke lokal (Jembatan)
     */
    public function pull(Request $request)
    {
        $secret = config('app.sync_secret', 'aypsis-sync-12345');
        if ($request->header('X-Sync-Secret') !== $secret && $request->query('secret') !== $secret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        $nik = $request->query('nik');

        $query = Absensi::whereDate('waktu', $date);
        
        if ($nik) {
            $query->where('nik', $nik);
        }

        $logs = $query->orderBy('waktu', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}

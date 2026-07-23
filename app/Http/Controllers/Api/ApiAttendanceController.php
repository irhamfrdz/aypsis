<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiAttendanceController extends Controller
{
    /**
     * Store new attendance log (Clock In / Clock Out).
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:Masuk,Pulang,masuk,pulang,istirahat_keluar,istirahat_masuk',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|string',
            'device' => 'nullable|string',
            'detail_lokasi' => 'nullable|string',
        ]);

        $user = $request->user();
        
        if (! $user->karyawan_id) {
            return response()->json([
                'success' => false,
                'message' => 'User ini tidak terhubung dengan data karyawan mana pun.',
            ], 400);
        }

        $karyawan = $user->karyawan;
        $today = Carbon::today('Asia/Jakarta');
        
        // Normalize tipe to match what we store (or just keep what's sent)
        $tipe = strtolower($request->tipe) == 'masuk' ? 'Masuk' : 
               (strtolower($request->tipe) == 'pulang' ? 'Pulang' : $request->tipe);

        // Check if already checked in/out today for this specific type
        $existing = Absensi::where('karyawan_id', $karyawan->id)
            ->where('tipe', $tipe)
            ->whereDate('waktu', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah melakukan absensi {$tipe} hari ini.",
            ], 400);
        }

        // Create absensi entry
        $absensi = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'nik' => $karyawan->nik,
            'waktu' => Carbon::now('Asia/Jakarta'),
            'tipe' => $tipe,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
            'foto' => $request->foto,
            'device' => $request->device,
            'detail_lokasi' => $request->detail_lokasi,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'HADIR', // Add default status for success, or based on logic if required by app
            'message' => "Absensi {$tipe} berhasil dicatat.",
            'data' => $absensi,
        ]);
    }

    /**
     * Get today's attendance status.
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $nik = $request->query('nik') ?? ($user->karyawan ? $user->karyawan->nik : null);
        $karyawan_id = $request->query('karyawan_id') ?? $user->karyawan_id;

        if (!$nik && !$karyawan_id) {
             return response()->json([
                 'success' => false,
                 'message' => 'Data karyawan tidak ditemukan.'
             ], 400);
        }

        $today = Carbon::today('Asia/Jakarta');
        
        $query = Absensi::whereDate('waktu', $today);
        if ($karyawan_id) {
             $query->where('karyawan_id', $karyawan_id);
        } else {
             $query->where('nik', $nik);
        }
        
        $records = $query->get();
        
        // Match the casing used in normalized store method
        $result = [
            'checkIn' => $records->where('tipe', 'Masuk')->first(),
            'checkOut' => $records->where('tipe', 'Pulang')->first(),
            'istirahatKeluar' => $records->where('tipe', 'istirahat_keluar')->first(),
            'istirahatMasuk' => $records->where('tipe', 'istirahat_masuk')->first(),
        ];
        
        return response()->json($result);
    }

    /**
     * Get attendance history of the logged-in user.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if (! $user->karyawan_id) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 400);
        }

        // Retrieve latest 30 attendance records
        $history = Absensi::where('karyawan_id', $user->karyawan_id)
            ->orderBy('waktu', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}

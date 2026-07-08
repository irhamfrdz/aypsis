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
            'tipe' => 'required|in:Masuk,Pulang',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
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

        // Check if already checked in/out today
        $existing = Absensi::where('karyawan_id', $karyawan->id)
            ->where('tipe', $request->tipe)
            ->whereDate('waktu', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah melakukan absensi {$request->tipe} hari ini.",
            ], 400);
        }

        // Create absensi entry
        $absensi = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'nik' => $karyawan->nik,
            'waktu' => Carbon::now('Asia/Jakarta'),
            'tipe' => $request->tipe,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Absensi {$request->tipe} berhasil dicatat.",
            'data' => $absensi,
        ]);
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

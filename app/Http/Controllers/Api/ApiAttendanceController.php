<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Proses base64 foto menjadi file fisik
        $fotoUrl = null;
        if ($request->foto) {
            $fotoData = $request->foto;
            // Cek apakah ada prefix data:image/...;base64,
            if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $type)) {
                $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
                $ext = strtolower($type[1]);
            } else {
                $ext = 'jpg';
            }
            
            $decodedData = base64_decode($fotoData);
            $fileName = 'selfie_' . $karyawan->nik . '_' . time() . '.' . $ext;
            
            $uploadPath = public_path('uploads/absensi');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            file_put_contents($uploadPath . '/' . $fileName, $decodedData);
            $fotoUrl = '/uploads/absensi/' . $fileName;
        }

        // Tentukan waktu absensi
        $waktuAbsensi = Carbon::now('Asia/Jakarta');

        // Toleransi waktu masuk: jika absen antara 09:01 – 09:05, simpan sebagai 09:00
        // (berlaku hanya untuk tipe Masuk yang dikirim via PWA)
        if ($tipe === 'Masuk') {
            $jamMenit = (int) $waktuAbsensi->format('Hi'); // contoh: 0901, 0905
            if ($jamMenit >= 901 && $jamMenit <= 905) {
                $waktuAbsensi = $waktuAbsensi->copy()->setTime(9, 0, 0);
            }
        }

        // Validasi Lokasi Absensi Wajib & Radius Geofence
        $statusAbsensi = 'HADIR';
        $detailLokasi = $request->detail_lokasi;

        // 1. Ambil lokasi khusus yang menugaskan karyawan ini
        $assignedLocations = DB::table('lokasi_absensis as la')
            ->join('lokasi_absensi_karyawan as lak', 'la.id', '=', 'lak.lokasi_absensi_id')
            ->where('lak.karyawan_id', $karyawan->id)
            ->where('la.is_active', 1)
            ->select('la.*')
            ->get();

        // 2. Jika karyawan tidak punya penugasan khusus, ambil lokasi yang berlaku untuk 'semua'
        if ($assignedLocations->isEmpty()) {
            $assignedLocations = DB::table('lokasi_absensis')
                ->where('is_active', 1)
                ->where(function ($q) {
                    $q->where('tipe_penugasan', 'semua')
                      ->orWhereNull('tipe_penugasan');
                })
                ->get();
        }

        // 3. Fallback jika masih kosong, gunakan seluruh lokasi aktif
        if ($assignedLocations->isEmpty()) {
            $assignedLocations = DB::table('lokasi_absensis')->where('is_active', 1)->get();
        }

        $nearestLoc = null;
        $nearestDistance = null;
        $isInRadius = false;

        if ($request->filled('latitude') && $request->filled('longitude') && $assignedLocations->isNotEmpty()) {
            $lat1 = deg2rad((float) $request->latitude);
            $lon1 = deg2rad((float) $request->longitude);

            foreach ($assignedLocations as $loc) {
                if (!is_numeric($loc->latitude) || !is_numeric($loc->longitude)) {
                    continue;
                }

                $lat2 = deg2rad((float) $loc->latitude);
                $lon2 = deg2rad((float) $loc->longitude);
                $dLat = $lat2 - $lat1;
                $dLon = $lon2 - $lon1;
                $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
                $distance = 6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a));

                if ($nearestDistance === null || $distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestLoc = $loc;
                }

                // Jika di dalam radius titik ini
                if ($distance <= (int) $loc->radius) {
                    $isInRadius = true;
                    $nearestDistance = $distance;
                    $nearestLoc = $loc;
                    break;
                }
            }

            if ($nearestLoc) {
                $distRound = round($nearestDistance);
                if ($isInRadius) {
                    $statusAbsensi = 'HADIR';
                    $detailLokasi = "{$nearestLoc->nama_lokasi} (Jarak: {$distRound}m, Radius: {$nearestLoc->radius}m)";
                } else {
                    $statusAbsensi = 'PERSETUJUAN';
                    $detailLokasi = "Di luar radius {$nearestLoc->nama_lokasi} (Jarak: {$distRound}m, Radius: {$nearestLoc->radius}m)";
                }
            }
        }

        // Create absensi entry
        $absensi = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'nik' => $karyawan->nik,
            'waktu' => $waktuAbsensi,
            'tipe' => $tipe,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
            'foto' => $fotoUrl,
            'device' => $request->device,
            'detail_lokasi' => $detailLokasi,
            'status' => $statusAbsensi,
        ]);

        $message = ($statusAbsensi === 'PERSETUJUAN' && $nearestLoc)
            ? "Absensi {$tipe} tercatat di luar radius lokasi wajib ({$nearestLoc->nama_lokasi}) dan memerlukan persetujuan."
            : "Absensi {$tipe} berhasil dicatat.";

        return response()->json([
            'success' => true,
            'status' => $statusAbsensi,
            'message' => $message,
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

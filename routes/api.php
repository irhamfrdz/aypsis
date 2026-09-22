<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AbsensiSyncController;

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);

// Endpoint Push Absensi dari Mesin/Lokal
Route::post('/absensi/push', [AbsensiSyncController::class, 'push']);

// Endpoint Pull Absensi dari Server Online ke Lokal (Jembatan)
Route::get('/absensi/pull', [AbsensiSyncController::class, 'pull']);

// Endpoint Notifikasi Absensi Baru dari Node.js
Route::post('/absensi/notify', function (Request $request) {
    $secret = $request->header('X-Sync-Secret') ?? $request->input('secret');
    if ($secret !== config('app.sync_secret', 'aypsis-sync-12345')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $data = $request->validate([
        'absensi_id' => 'required|integer',
    ]);

    $absensi = \App\Models\Absensi::find($data['absensi_id']);
    if (!$absensi) {
        return response()->json(['success' => false, 'message' => 'Absensi not found'], 404);
    }

    // Kirim notifikasi hanya ke user 'adit' dan 'kiky'
    $users = \App\Models\User::whereIn('username', ['adit', 'kiky'])->get();
    
    $karyawanNama = $absensi->karyawan 
        ? $absensi->karyawan->nama_lengkap 
        : 'Karyawan NIK: ' . $absensi->nik;
    $waktuFormatted = $absensi->waktu instanceof \Carbon\Carbon 
        ? $absensi->waktu->format('H:i:s') 
        : \Carbon\Carbon::parse($absensi->waktu)->format('H:i:s');
        
    $title = "Absensi Baru: {$absensi->tipe}";
    $body = "{$karyawanNama} telah melakukan absen {$absensi->tipe} pukul {$waktuFormatted}.";

    foreach ($users as $user) {
        // Notifikasi web/database
        $user->notify(new \App\Notifications\AbsensiMasukNotification($absensi));
        
        // Notifikasi HP/Expo Push
        if ($user->expo_push_token) {
            \App\Services\ExpoNotificationService::send(
                $user->expo_push_token,
                $title,
                $body,
                ['absensi_id' => $absensi->id, 'nik' => $absensi->nik]
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Notifications dispatched successfully.'
    ]);
});

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    Route::post('/attendance', [ApiAttendanceController::class, 'store']);
    Route::post('/attendance/record', [ApiAttendanceController::class, 'store']);
    Route::get('/attendance/history', [ApiAttendanceController::class, 'history']);
    Route::get('/attendance/today', [ApiAttendanceController::class, 'today']);
    Route::get('/attendance/locations', function(Request $request) {
        $user = $request->user();
        $karyawanId = $user ? $user->karyawan_id : null;
        
        $locations = \Illuminate\Support\Facades\DB::table('lokasi_absensis')->where('is_active', 1)->orderBy('created_at', 'desc')->get();
        
        if ($karyawanId) {
            $assignedLocIds = \Illuminate\Support\Facades\DB::table('lokasi_absensi_karyawan')
                ->where('karyawan_id', $karyawanId)
                ->pluck('lokasi_absensi_id')
                ->toArray();
                
            foreach ($locations as $loc) {
                if (($loc->tipe_penugasan ?? 'semua') === 'khusus') {
                    $loc->is_assigned = in_array($loc->id, $assignedLocIds);
                } else {
                    $loc->is_assigned = true;
                }
            }
        }
        
        return response()->json($locations);
    });
    
    Route::post('/attendance/detect-face', function(Request $request) {
        return response()->json(['success' => true, 'hasFace' => true]);
    });
    
    Route::post('/user/push-token', function (Request $request) {
        $data = $request->validate([
            'token' => 'required|string',
        ]);
        
        $request->user()->update([
            'expo_push_token' => $data['token']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Expo Push Token saved successfully.'
        ]);
    });
});

// Lokasi Absensi API endpoints (public/shared with Node.js port mapping)
use Illuminate\Support\Facades\DB;

Route::get('/lokasi-absensi/karyawans', function() {
    $karyawans = DB::table('karyawans')
        ->leftJoin('users', 'users.karyawan_id', '=', 'karyawans.id')
        ->whereNull('karyawans.tanggal_berhenti')
        ->select(
            'karyawans.id',
            'karyawans.nik',
            'karyawans.nama_lengkap',
            'karyawans.nama_panggilan',
            'karyawans.divisi',
            'karyawans.pekerjaan',
            'users.id as user_id',
            'users.username'
        )
        ->orderBy('karyawans.nama_lengkap', 'asc')
        ->get();
        
    return response()->json($karyawans);
});

Route::get('/lokasi-absensi', function() {
    $locations = DB::table('lokasi_absensis')->orderBy('created_at', 'desc')->get();
    
    $locationIds = $locations->pluck('id')->toArray();
    $assignments = DB::table('lokasi_absensi_karyawan as lak')
        ->join('karyawans as k', 'lak.karyawan_id', '=', 'k.id')
        ->whereIn('lak.lokasi_absensi_id', $locationIds)
        ->select('lak.lokasi_absensi_id', 'lak.karyawan_id', 'k.nama_lengkap', 'k.nik', 'k.divisi')
        ->get()
        ->groupBy('lokasi_absensi_id');
        
    foreach ($locations as $loc) {
        $assigned = $assignments->get($loc->id, collect());
        $loc->assigned_karyawan_ids = $assigned->pluck('karyawan_id')->toArray();
        $loc->assigned_karyawans = $assigned->values();
        $loc->tipe_penugasan = $loc->tipe_penugasan ?? 'semua';
    }
    
    return response()->json($locations);
});

Route::post('/lokasi-absensi', function(Request $request) {
    $data = $request->validate([
        'nama_lokasi' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'nullable|integer',
        'keterangan' => 'nullable|string',
        'is_active' => 'nullable',
        'tipe_penugasan' => 'nullable|in:semua,khusus',
        'karyawan_ids' => 'nullable|array',
        'karyawan_ids.*' => 'integer'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    $tipePenugasan = $data['tipe_penugasan'] ?? 'semua';
    
    $id = DB::table('lokasi_absensis')->insertGetId([
        'nama_lokasi' => $data['nama_lokasi'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'radius' => $data['radius'] ?? 100,
        'keterangan' => $data['keterangan'] ?? null,
        'is_active' => $isActive,
        'tipe_penugasan' => $tipePenugasan,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    if ($tipePenugasan === 'khusus' && !empty($data['karyawan_ids'])) {
        $karyawanIds = array_unique($data['karyawan_ids']);
        $userMap = DB::table('users')->whereIn('karyawan_id', $karyawanIds)->pluck('id', 'karyawan_id');
        
        $inserts = [];
        foreach ($karyawanIds as $kId) {
            $inserts[] = [
                'lokasi_absensi_id' => $id,
                'karyawan_id' => $kId,
                'user_id' => $userMap->get($kId) ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('lokasi_absensi_karyawan')->insert($inserts);
    }
    
    return response()->json([
        'message' => 'Lokasi absensi berhasil ditambahkan.',
        'id' => $id
    ]);
});

Route::put('/lokasi-absensi/{id}', function(Request $request, $id) {
    $data = $request->validate([
        'nama_lokasi' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'nullable|integer',
        'keterangan' => 'nullable|string',
        'is_active' => 'nullable',
        'tipe_penugasan' => 'nullable|in:semua,khusus',
        'karyawan_ids' => 'nullable|array',
        'karyawan_ids.*' => 'integer'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    $tipePenugasan = $data['tipe_penugasan'] ?? 'semua';
    
    DB::table('lokasi_absensis')->where('id', $id)->update([
        'nama_lokasi' => $data['nama_lokasi'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'radius' => $data['radius'] ?? 100,
        'keterangan' => $data['keterangan'] ?? null,
        'is_active' => $isActive,
        'tipe_penugasan' => $tipePenugasan,
        'updated_at' => now()
    ]);
    
    // Sinkronisasi penugasan karyawan
    DB::table('lokasi_absensi_karyawan')->where('lokasi_absensi_id', $id)->delete();
    if ($tipePenugasan === 'khusus' && !empty($data['karyawan_ids'])) {
        $karyawanIds = array_unique($data['karyawan_ids']);
        $userMap = DB::table('users')->whereIn('karyawan_id', $karyawanIds)->pluck('id', 'karyawan_id');
        
        $inserts = [];
        foreach ($karyawanIds as $kId) {
            $inserts[] = [
                'lokasi_absensi_id' => $id,
                'karyawan_id' => $kId,
                'user_id' => $userMap->get($kId) ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('lokasi_absensi_karyawan')->insert($inserts);
    }
    
    return response()->json([
        'message' => 'Lokasi absensi berhasil diperbarui.'
    ]);
});

Route::delete('/lokasi-absensi/{id}', function($id) {
    DB::table('lokasi_absensi_karyawan')->where('lokasi_absensi_id', $id)->delete();
    DB::table('lokasi_absensis')->where('id', $id)->delete();
    return response()->json([
        'message' => 'Lokasi absensi berhasil dihapus.'
    ]);
});

// Jam Kerja (Working Hours) API endpoints
Route::get('/working-hours', function() {
    return response()->json(DB::table('jam_kerjas')->orderBy('created_at', 'desc')->get());
});

Route::post('/working-hours', function(Request $request) {
    $data = $request->validate([
        'nama_shift' => 'required|string',
        'jam_masuk' => 'required',
        'jam_keluar' => 'required',
        'toleransi_keterlambatan' => 'nullable|integer',
        'is_active' => 'nullable'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    
    $id = DB::table('jam_kerjas')->insertGetId([
        'nama_shift' => $data['nama_shift'],
        'jam_masuk' => $data['jam_masuk'],
        'jam_keluar' => $data['jam_keluar'],
        'toleransi_keterlambatan' => $data['toleransi_keterlambatan'] ?? 0,
        'is_active' => $isActive,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    return response()->json([
        'message' => 'Jam kerja berhasil ditambahkan.',
        'id' => $id
    ]);
});

Route::put('/working-hours/{id}', function(Request $request, $id) {
    $data = $request->validate([
        'nama_shift' => 'required|string',
        'jam_masuk' => 'required',
        'jam_keluar' => 'required',
        'toleransi_keterlambatan' => 'nullable|integer',
        'is_active' => 'nullable'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    
    DB::table('jam_kerjas')->where('id', $id)->update([
        'nama_shift' => $data['nama_shift'],
        'jam_masuk' => $data['jam_masuk'],
        'jam_keluar' => $data['jam_keluar'],
        'toleransi_keterlambatan' => $data['toleransi_keterlambatan'] ?? 0,
        'is_active' => $isActive,
        'updated_at' => now()
    ]);
    
    return response()->json([
        'message' => 'Jam kerja berhasil diperbarui.'
    ]);
});

Route::delete('/working-hours/{id}', function($id) {
    DB::table('jam_kerjas')->where('id', $id)->delete();
    return response()->json([
        'message' => 'Jam kerja berhasil dihapus.'
    ]);
});

// Hari Libur (Holidays) API endpoints
Route::get('/holidays', function() {
    return response()->json(DB::table('hari_liburs')->orderBy('tanggal', 'asc')->get());
});

Route::post('/holidays', function(Request $request) {
    $data = $request->validate([
        'tanggal' => 'required|date',
        'keterangan' => 'required|string'
    ]);
    
    $existing = DB::table('hari_liburs')->where('tanggal', $data['tanggal'])->first();
    if ($existing) {
        return response()->json(['error' => 'Tanggal tersebut sudah diatur sebagai hari libur'], 400);
    }
    
    $id = DB::table('hari_liburs')->insertGetId([
        'tanggal' => $data['tanggal'],
        'keterangan' => $data['keterangan'],
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    return response()->json([
        'message' => 'Hari libur berhasil ditambahkan.',
        'id' => $id
    ]);
});

Route::put('/holidays/{id}', function(Request $request, $id) {
    $data = $request->validate([
        'tanggal' => 'required|date',
        'keterangan' => 'required|string'
    ]);
    
    $existing = DB::table('hari_liburs')->where('tanggal', $data['tanggal'])->where('id', '!=', $id)->first();
    if ($existing) {
        return response()->json(['error' => 'Tanggal tersebut sudah diatur sebagai hari libur'], 400);
    }
    
    DB::table('hari_liburs')->where('id', $id)->update([
        'tanggal' => $data['tanggal'],
        'keterangan' => $data['keterangan'],
        'updated_at' => now()
    ]);
    
    return response()->json([
        'message' => 'Hari libur berhasil diperbarui.'
    ]);
});

Route::delete('/holidays/{id}', function($id) {
    DB::table('hari_liburs')->where('id', $id)->delete();
    return response()->json([
        'message' => 'Hari libur berhasil dihapus.'
    ]);
});



// Stowage Plan API routes
use App\Http\Controllers\Api\StowagePlanController;

Route::get('/stowage-plans/ships', [StowagePlanController::class, 'getShips']);
Route::get('/stowage-plans/by-ship', [StowagePlanController::class, 'getByShip']);
Route::get('/stowage-plans/manifests-without-plan', [StowagePlanController::class, 'getManifestsWithoutPlan']);
Route::get('/stowage-plans', [StowagePlanController::class, 'index']);
Route::post('/stowage-plans/cancel', [StowagePlanController::class, 'cancel']);
Route::post('/stowage-plans', [StowagePlanController::class, 'store']);


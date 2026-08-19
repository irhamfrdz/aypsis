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
    Route::get('/attendance/locations', function() {
        return response()->json(\Illuminate\Support\Facades\DB::table('lokasi_absensis')->orderBy('created_at', 'desc')->get());
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

Route::get('/lokasi-absensi', function() {
    return response()->json(DB::table('lokasi_absensis')->orderBy('created_at', 'desc')->get());
});

Route::post('/lokasi-absensi', function(Request $request) {
    $data = $request->validate([
        'nama_lokasi' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'nullable|integer',
        'keterangan' => 'nullable|string',
        'is_active' => 'nullable'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    
    $id = DB::table('lokasi_absensis')->insertGetId([
        'nama_lokasi' => $data['nama_lokasi'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'radius' => $data['radius'] ?? 100,
        'keterangan' => $data['keterangan'] ?? null,
        'is_active' => $isActive,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
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
        'is_active' => 'nullable'
    ]);
    
    $isActive = isset($data['is_active']) ? ($data['is_active'] == 1 ? 1 : 0) : 1;
    
    DB::table('lokasi_absensis')->where('id', $id)->update([
        'nama_lokasi' => $data['nama_lokasi'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'radius' => $data['radius'] ?? 100,
        'keterangan' => $data['keterangan'] ?? null,
        'is_active' => $isActive,
        'updated_at' => now()
    ]);
    
    return response()->json([
        'message' => 'Lokasi absensi berhasil diperbarui.'
    ]);
});

Route::delete('/lokasi-absensi/{id}', function($id) {
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

// Admin Approval API endpoints
Route::get('/admin/pending-attendance', function() {
    $rows = DB::table('absensis as a')
        ->leftJoin('karyawans as k', function($join) {
            $join->on('a.karyawan_id', '=', 'k.id')
                 ->orOn('a.nik', '=', 'k.nik');
        })
        ->select('a.*', 'k.nama_lengkap', 'k.divisi', 'k.pekerjaan')
        ->where('a.status', 'PERSETUJUAN')
        ->orderBy('a.waktu', 'desc')
        ->get();
    return response()->json($rows);
});

Route::post('/attendance/approve', function(Request $request) {
    $data = $request->validate(['attendance_id' => 'required|integer']);
    
    $updateData = [
        'status' => 'HADIR',
        'updated_at' => now()
    ];
    
    if ($request->hasFile('admin_lampiran')) {
        $file = $request->file('admin_lampiran');
        $filename = time() . '_approve_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/admin_attachments'), $filename);
        $updateData['admin_lampiran'] = '/uploads/admin_attachments/' . $filename;
    }
    
    DB::table('absensis')->where('id', $data['attendance_id'])->update($updateData);
    
    return response()->json(['message' => 'Absensi berhasil disetujui, status berubah menjadi HADIR.']);
});

Route::post('/attendance/reject', function(Request $request) {
    $data = $request->validate(['attendance_id' => 'required|integer']);
    
    $updateData = [
        'status' => 'DITOLAK',
        'updated_at' => now()
    ];
    
    if ($request->hasFile('admin_lampiran')) {
        $file = $request->file('admin_lampiran');
        $filename = time() . '_reject_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/admin_attachments'), $filename);
        $updateData['admin_lampiran'] = '/uploads/admin_attachments/' . $filename;
    }
    
    DB::table('absensis')->where('id', $data['attendance_id'])->update($updateData);
    
    return response()->json(['message' => 'Absensi berhasil ditolak, status berubah menjadi DITOLAK.']);
});

Route::get('/admin/pending-permissions', function() {
    $cutis = DB::table('cutis')
        ->join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
        ->whereIn('cutis.status', ['PENDING', 'Pending', 'pending'])
        ->select(
            'cutis.id',
            'cutis.karyawan_id',
            'karyawans.nik',
            'karyawans.nama_lengkap as nama',
            'karyawans.divisi',
            'cutis.jenis_cuti as jenis_izin',
            'cutis.tanggal_mulai',
            'cutis.tanggal_selesai',
            DB::raw("NULL as waktu"),
            'cutis.keterangan as alasan',
            DB::raw("NULL as lampiran"),
            'cutis.status',
            'cutis.created_at',
            'cutis.updated_at',
            DB::raw("'cutis' as tabel_sumber")
        );

    $rows = DB::table('permohonan_izins')
        ->whereIn('status', ['PENDING', 'Pending', 'pending'])
        ->select(
            'id',
            'karyawan_id',
            'nik',
            'nama',
            'divisi',
            'jenis_izin',
            'tanggal_mulai',
            'tanggal_selesai',
            'waktu',
            'alasan',
            'lampiran',
            'status',
            'created_at',
            'updated_at',
            DB::raw("'permohonan_izins' as tabel_sumber")
        )
        ->union($cutis)
        ->orderBy('created_at', 'desc')
        ->get();
        
    foreach ($rows as $row) {
        if (strtolower($row->jenis_izin) === 'tahunan') {
            $tahun = date('Y', strtotime($row->tanggal_mulai));
            $saldo = DB::table('saldo_cutis')
                ->where('karyawan_id', $row->karyawan_id)
                ->where('tahun', $tahun)
                ->first();
            
            $row->sisa_cuti = $saldo ? $saldo->sisa_cuti : 12;
        }
    }
        
    return response()->json($rows);
});

Route::post('/admin/permissions/approve', function(Request $request) {
    $data = $request->validate([
        'permission_id' => 'required|integer',
        'tabel_sumber' => 'nullable|string'
    ]);
    
    $table = $data['tabel_sumber'] ?? 'permohonan_izins';
    
    $permission = DB::table($table)->where('id', $data['permission_id'])->first();
    if (!$permission) {
        return response()->json(['error' => 'Permohonan tidak ditemukan.'], 404);
    }
    
    // Jika permohonan sebelumnya belum APPROVED dan jenisnya adalah tahunan
    $jenis = $table === 'cutis' ? $permission->jenis_cuti : $permission->jenis_izin;
    if (strtoupper($permission->status) !== 'APPROVED' && strtolower($jenis) === 'tahunan') {
        $karyawan_id = $permission->karyawan_id;
        $tahun = date('Y', strtotime($permission->tanggal_mulai));
        
        $start = \Carbon\Carbon::parse($permission->tanggal_mulai)->startOfDay();
        $end = \Carbon\Carbon::parse($permission->tanggal_selesai)->startOfDay();
        $diffDays = $start->diffInDays($end) + 1;
        
        $saldo = \App\Models\SaldoCuti::where('karyawan_id', $karyawan_id)
                    ->where('tahun', $tahun)
                    ->first();
                    
        if ($saldo) {
            $saldo->cuti_terpakai += $diffDays;
            $saldo->sisa_cuti = $saldo->total_cuti - $saldo->cuti_terpakai;
            $saldo->save();
        } else {
            \App\Models\SaldoCuti::create([
                'karyawan_id' => $karyawan_id,
                'tahun' => $tahun,
                'total_cuti' => 12,
                'cuti_terpakai' => $diffDays,
                'sisa_cuti' => 12 - $diffDays
            ]);
        }
    }
    
    $updateData = [
        'status' => 'APPROVED',
        'updated_at' => now()
    ];
    
    if ($request->hasFile('admin_lampiran')) {
        $file = $request->file('admin_lampiran');
        $filename = time() . '_approve_izin_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/admin_attachments'), $filename);
        $updateData['admin_lampiran'] = '/uploads/admin_attachments/' . $filename;
    }
    
    DB::table($table)->where('id', $data['permission_id'])->update($updateData);
    
    return response()->json(['message' => 'Permohonan berhasil disetujui.']);
});

Route::post('/admin/permissions/reject', function(Request $request) {
    $data = $request->validate([
        'permission_id' => 'required|integer',
        'tabel_sumber' => 'nullable|string'
    ]);
    
    $table = $data['tabel_sumber'] ?? 'permohonan_izins';
    
    $updateData = [
        'status' => 'REJECTED',
        'updated_at' => now()
    ];
    
    if ($request->hasFile('admin_lampiran')) {
        $file = $request->file('admin_lampiran');
        $filename = time() . '_reject_izin_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/admin_attachments'), $filename);
        $updateData['admin_lampiran'] = '/uploads/admin_attachments/' . $filename;
    }
    
    DB::table($table)->where('id', $data['permission_id'])->update($updateData);
    
    return response()->json(['message' => 'Permohonan ditolak.']);
});

// Stowage Plan API routes
use App\Http\Controllers\Api\StowagePlanController;

Route::get('/stowage-plans/ships', [StowagePlanController::class, 'getShips']);
Route::get('/stowage-plans/by-ship', [StowagePlanController::class, 'getByShip']);
Route::get('/stowage-plans/manifests-without-plan', [StowagePlanController::class, 'getManifestsWithoutPlan']);
Route::get('/stowage-plans', [StowagePlanController::class, 'index']);
Route::post('/stowage-plans/cancel', [StowagePlanController::class, 'cancel']);
Route::post('/stowage-plans', [StowagePlanController::class, 'store']);


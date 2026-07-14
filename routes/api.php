<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AbsensiSyncController;

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);

// Endpoint Push Absensi dari Mesin/Lokal
Route::post('/absensi/push', [AbsensiSyncController::class, 'push']);

// Endpoint Pull Absensi dari Server Online ke Lokal (Jembatan)
Route::get('/absensi/pull', [AbsensiSyncController::class, 'pull']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    Route::post('/attendance', [ApiAttendanceController::class, 'store']);
    Route::get('/attendance/history', [ApiAttendanceController::class, 'history']);
});

// Lokasi Absensi API endpoints (public/shared with Node.js port mapping)
use Illuminate\Http\Request;
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
    
    DB::table('absensis')->where('id', $data['attendance_id'])->update([
        'status' => 'HADIR',
        'updated_at' => now()
    ]);
    
    return response()->json(['message' => 'Absensi berhasil disetujui, status berubah menjadi HADIR.']);
});

Route::post('/attendance/reject', function(Request $request) {
    $data = $request->validate(['attendance_id' => 'required|integer']);
    
    DB::table('absensis')->where('id', $data['attendance_id'])->update([
        'status' => 'DITOLAK',
        'updated_at' => now()
    ]);
    
    return response()->json(['message' => 'Absensi berhasil ditolak, status berubah menjadi DITOLAK.']);
});

Route::get('/admin/pending-permissions', function() {
    $rows = DB::table('permohonan_izins')
        ->where('status', 'PENDING')
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json($rows);
});

Route::post('/admin/permissions/approve', function(Request $request) {
    $data = $request->validate(['permission_id' => 'required|integer']);
    
    DB::table('permohonan_izins')->where('id', $data['permission_id'])->update([
        'status' => 'APPROVED',
        'updated_at' => now()
    ]);
    
    return response()->json(['message' => 'Permohonan izin berhasil disetujui.']);
});

Route::post('/admin/permissions/reject', function(Request $request) {
    $data = $request->validate(['permission_id' => 'required|integer']);
    
    DB::table('permohonan_izins')->where('id', $data['permission_id'])->update([
        'status' => 'REJECTED',
        'updated_at' => now()
    ]);
    
    return response()->json(['message' => 'Permohonan izin ditolak.']);
});


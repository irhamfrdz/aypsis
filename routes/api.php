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

<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AbsensiSyncController;

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);

// Endpoint Push Absensi dari Mesin/Lokal
Route::post('/absensi/push', [AbsensiSyncController::class, 'push']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    Route::post('/attendance', [ApiAttendanceController::class, 'store']);
    Route::get('/attendance/history', [ApiAttendanceController::class, 'history']);
});

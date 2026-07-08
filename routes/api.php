<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    Route::post('/attendance', [ApiAttendanceController::class, 'store']);
    Route::get('/attendance/history', [ApiAttendanceController::class, 'history']);
});

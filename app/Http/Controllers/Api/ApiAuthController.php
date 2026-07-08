<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Check if user is approved
        if ($user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum disetujui oleh admin atau status tidak aktif.',
            ], 403);
        }

        // Load karyawan details
        $user->load('karyawan');

        // Create Sanctum Token
        $token = $user->createToken('mobile-absensi-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name, // getNameAttribute accessor
                    'role' => $user->karyawan->divisi ?? 'Karyawan',
                    'karyawan' => $user->karyawan,
                ],
            ],
        ]);
    }

    /**
     * Get the logged-in user profile.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('karyawan');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'role' => $user->karyawan->divisi ?? 'Karyawan',
                'karyawan' => $user->karyawan,
            ],
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}

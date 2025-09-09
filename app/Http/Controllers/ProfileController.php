<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Karyawan;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = Auth::user();
        $user = User::with('karyawan')->find($user->id);
        
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $user = Auth::user();
        $user = User::with('karyawan')->find($user->id);
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's account information
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9._]+$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic user info
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'username' => $request->username,
        ]);

        // Update password if provided
        if ($request->filled('new_password')) {
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Update the user's personal information (karyawan data)
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::user();
        $user = User::with('karyawan')->find($user->id);
        
        if (!$user->karyawan) {
            return back()->withErrors(['error' => 'Data karyawan tidak ditemukan.']);
        }

        $request->validate([
            'nik' => 'nullable|string|max:20|unique:karyawan,nik,' . $user->karyawan->id,
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'alamat_lengkap' => 'nullable|string|max:500',
            'tanggal_masuk' => 'nullable|date',
            'divisi' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'no_ketenagakerjaan' => 'nullable|string|max:50',
        ]);

        Karyawan::where('id', $user->karyawan->id)->update($request->only([
            'nik',
            'nama_lengkap', 
            'nama_panggilan',
            'email',
            'no_hp',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'agama',
            'status_perkawinan',
            'alamat_lengkap',
            'tanggal_masuk',
            'divisi',
            'pekerjaan',
            'no_ketenagakerjaan'
        ]));

        return back()->with('success', 'Data pribadi berhasil diperbarui.');
    }

    /**
     * Update profile picture (future implementation)
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Future implementation for avatar upload
        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Delete account (with confirmation)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'confirmation' => 'required|in:DELETE',
        ]);

        $user = Auth::user();
        
        // Logout user
        Auth::logout();
        
        // Delete user account
        User::where('id', $user->id)->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}

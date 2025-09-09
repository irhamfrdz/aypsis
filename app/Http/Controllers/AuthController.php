<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\KaryawanController;

class AuthController extends Controller
{
    /**
     * Menampilkan form Login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Custom query untuk autentikasi dengan username
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();

            // If the user has no related karyawan record, show the "create karyawan" form
            // so they can register their employee profile immediately.
            if (empty($user->karyawan)) {
                // Call the KaryawanController create method directly to render the form
                $kc = app()->make(KaryawanController::class);
                return $kc->create();
            }

            // REVISI: Redirect based on pekerjaan
            if ($user->karyawan && $user->karyawan->pekerjaan === 'Supir Truck') {
                return redirect()->route('supir.dashboard');
            }

            // Redirect default untuk admin/staff
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'login' => 'Username atau password salah. Silakan periksa kembali data login Anda.',
            'username' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }

    /**
     * Menangani proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Menampilkan form registrasi karyawan
     */
    public function showKaryawanRegisterForm()
    {
        return view('auth.register-karyawan');
    }

    /**
     * Menangani proses registrasi karyawan
     */
    public function registerKaryawan(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:100',
            'nik' => 'required|string|max:20|unique:karyawans',
            'no_ketenagakerjaan' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
            'pekerjaan' => 'required|string',
            'alasan_pendaftaran' => 'required|string|max:500',
        ]);

        // Simpan data karyawan dengan status pending
        \App\Models\Karyawan::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nama_panggilan' => $request->nama_panggilan,
            'nik' => $request->nik,
            'no_ketenagakerjaan' => $request->no_ketenagakerjaan,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'pekerjaan' => $request->pekerjaan,
            'status' => 'pending', // Status pending untuk review admin
            'keterangan' => 'Registrasi mandiri: ' . $request->alasan_pendaftaran,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi karyawan berhasil! Menunggu persetujuan administrator.');
    }

    /**
     * Menampilkan form registrasi user
     */
    public function showUserRegisterForm()
    {
        // Ambil daftar karyawan yang belum memiliki user account
        $karyawans = \App\Models\Karyawan::whereDoesntHave('user')->get();

        return view('auth.register-user', compact('karyawans'));
    }

    /**
     * Menangani proses registrasi user
     */
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'karyawan_id' => 'required|exists:karyawans,id',
            'alasan_pendaftaran' => 'required|string|max:500',
        ]);

        // Cek apakah karyawan sudah memiliki user account
        $karyawan = \App\Models\Karyawan::find($request->karyawan_id);
        if ($karyawan->user) {
            return back()->withErrors(['karyawan_id' => 'Karyawan ini sudah memiliki akun user.']);
        }

        // Simpan data user dengan status inactive
        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'karyawan_id' => $request->karyawan_id,
            'status' => 'inactive', // Status inactive untuk review admin
            'registration_reason' => $request->alasan_pendaftaran,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi user berhasil! Menunggu persetujuan administrator untuk aktivasi akun.');
    }
}

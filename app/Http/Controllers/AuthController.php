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
}

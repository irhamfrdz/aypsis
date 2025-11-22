<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Pekerjaan;
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

        // Get remember me checkbox value
        $remember = $request->filled('remember');

        // Custom query untuk autentikasi dengan username - with remember me
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();

            // Handle user status cases.
            // Rejected users are denied.
            if ($user->status === 'rejected') {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun Anda telah ditolak oleh administrator. Silakan hubungi admin untuk informasi lebih lanjut.']);
            }

            // Jika user belum punya karyawan, arahkan ke onboarding form lengkap
            if (empty($user->karyawan)) {
                $request->session()->regenerate();

                // Ambil data divisis dan pekerjaans untuk dropdown
                $divisis = Divisi::active()->orderBy('nama_divisi')->get();
                $pekerjaans = Pekerjaan::active()->orderBy('nama_pekerjaan')->get();
                $cabangs = \App\Models\Cabang::orderBy('nama_cabang')->get();
                $pajaks = \App\Models\Pajak::orderBy('nama_status')->get();
                $banks = \App\Models\Bank::orderBy('name')->get();

                // Group pekerjaan by divisi for JavaScript
                $pekerjaanByDivisi = [];
                foreach ($pekerjaans as $pekerjaan) {
                    $divisi = $pekerjaan->divisi ?? '';
                    if (!isset($pekerjaanByDivisi[$divisi])) {
                        $pekerjaanByDivisi[$divisi] = [];
                    }
                    $pekerjaanByDivisi[$divisi][] = $pekerjaan->nama_pekerjaan;
                }

                return view('karyawan.onboarding-full', compact('divisis', 'pekerjaans', 'cabangs', 'pajaks', 'banks', 'pekerjaanByDivisi'));
            }

            // Any other non-approved status should be blocked.
            if ($user->status !== 'approved') {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun Anda masih menunggu persetujuan administrator. Silakan hubungi admin untuk aktivasi akun.']);
            }

            $request->session()->regenerate();

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
        $validated = $request->validate([
            'nik' => 'required|string|regex:/^[0-9]+$/|unique:karyawans',
            'nama_panggilan' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:karyawans',
            'ktp' => 'nullable|string|regex:/^[0-9]{16}$/|unique:karyawans',
            'kk' => 'nullable|string|regex:/^[0-9]{16}$/|unique:karyawans',
            'alamat' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'jenis_kelamin' => ['nullable', \Illuminate\Validation\Rule::in(['L', 'P'])],
            'status_perkawinan' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date',
            'tanggal_masuk_sebelumnya' => 'nullable|date',
            'tanggal_berhenti_sebelumnya' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
            'status_pajak' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'bank_cabang' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
        ], [
            'nik.regex' => 'NIK harus berupa angka saja, tidak boleh ada huruf.',
            'ktp.regex' => 'Nomor KTP harus berupa 16 digit angka saja, tidak boleh ada huruf.',
            'kk.regex' => 'Nomor KK harus berupa 16 digit angka saja, tidak boleh ada huruf.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar dalam sistem.',
            'kk.unique' => 'Nomor KK sudah terdaftar dalam sistem.',
        ]);

        try {
            $user = \App\Models\User::find(Auth::id());
            $validated['user_id'] = $user ? $user->id : null;
            // Tambahkan status default dan catatan onboarding jika belum ada
            if (empty($validated['status'])) {
                $validated['status'] = 'pending';
            }
            if (!empty($request->alasan_pendaftaran)) {
                $validated['catatan'] = ($validated['catatan'] ?? '') . ' Registrasi mandiri: ' . $request->alasan_pendaftaran;
            }
            $karyawan = \App\Models\Karyawan::create($validated);
            // Link user to karyawan (pastikan instance Eloquent)
            if ($user) {
                $user->karyawan_id = $karyawan->id;
                // Update username jika dikirim dari form (dari auto-generate JS)
                if ($request->filled('username') && $request->username !== $user->username) {
                    $user->username = $request->username;
                }
                $user->save();
            }


            // Jika ABK, redirect ke checklist crew (route khusus onboarding)
            if (method_exists($karyawan, 'isAbk') && $karyawan->isAbk()) {
                return redirect()->route('karyawan.onboarding-crew-checklist', $karyawan->id)
                    ->with('success', 'Data karyawan berhasil ditambahkan. Silakan lengkapi checklist kelengkapan crew.');
            }

            // User status tetap pending, menunggu approval admin
            // (Dihapus: otomatis approve setelah onboarding)

            // Logout and redirect to login for non-ABK
            Auth::logout();
            return redirect()->route('login')->with('success', 'Data karyawan berhasil disimpan. Akun Anda menunggu persetujuan administrator untuk dapat digunakan.');
        } catch (\Exception $e) {
            Log::error('registerKaryawan failed', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.');
        }
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

        // Simpan data user dengan status pending untuk approval
        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'karyawan_id' => $request->karyawan_id,
            'status' => 'pending', // Status pending untuk review admin
            'registration_reason' => $request->alasan_pendaftaran,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi user berhasil! Akun Anda menunggu persetujuan administrator untuk dapat digunakan.');
    }
}

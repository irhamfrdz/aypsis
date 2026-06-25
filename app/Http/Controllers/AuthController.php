<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use App\Models\Pekerjaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            // Jika user belum punya karyawan, redirect ke halaman pendaftaran/onboarding (GET)
            if (empty($user->karyawan_id) && empty($user->karyawan_tidak_tetap_id)) {
                $request->session()->regenerate();

                return redirect()->route('karyawan.create');
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
        $tipeKaryawan = $request->input('tipe_karyawan', 'tetap');

        if ($tipeKaryawan === 'tidak_tetap') {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'nama_panggilan' => 'required|string|max:255',
                'alamat' => 'required|string|max:255',
                'no_telepon' => 'required|string|max:255',
                'pekerjaan' => 'nullable|string|max:255',
            ]);
        } else {
            $validated = $request->validate([
                'nik' => 'required|string|regex:/^[0-9]+$/|unique:karyawans',
                'nama_panggilan' => 'required|string|max:255|unique:karyawans',
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
        }

        try {
            $user = \App\Models\User::find(Auth::id());

            if ($tipeKaryawan === 'tidak_tetap') {
                $nik = $this->generateNextKaryawanTidakTetapNik();
                $karyawanTidakTetap = KaryawanTidakTetap::create([
                    'nik' => $nik,
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'nama_panggilan' => $validated['nama_panggilan'],
                    'alamat_lengkap' => $validated['alamat'],
                    'pekerjaan' => $validated['pekerjaan'] ?? null,
                    'divisi' => 'NON KARYAWAN',
                    'tanggal_masuk' => now()->toDateString(),
                ]);

                if ($user) {
                    $user->karyawan_tidak_tetap_id = $karyawanTidakTetap->id;
                    if ($request->filled('username') && $request->username !== $user->username) {
                        $user->username = $request->username;
                    }
                    $user->save();
                }

                Auth::logout();

                return redirect()->route('login')->with('success', 'Data karyawan tidak tetap berhasil disimpan. Akun Anda menunggu persetujuan administrator untuk dapat digunakan.');
            }

            $validated['user_id'] = $user ? $user->id : null;
            // Tambahkan status default dan catatan onboarding jika belum ada
            if (empty($validated['status'])) {
                $validated['status'] = 'pending';
            }
            if (! empty($request->alasan_pendaftaran)) {
                $validated['catatan'] = ($validated['catatan'] ?? '').' Registrasi mandiri: '.$request->alasan_pendaftaran;
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

            // Logout and redirect to login for non-ABK
            Auth::logout();

            return redirect()->route('login')->with('success', 'Data karyawan berhasil disimpan. Akun Anda menunggu persetujuan administrator untuk dapat digunakan.');
        } catch (\Exception $e) {
            Log::error('registerKaryawan failed', ['error' => $e->getMessage(), 'input' => $request->all()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Generate next NIK for Karyawan Tidak Tetap
     */
    private function generateNextKaryawanTidakTetapNik(): string
    {
        $last = KaryawanTidakTetap::where('nik', 'LIKE', 'P%')
            ->orderByRaw('CAST(SUBSTRING(nik, 2) AS UNSIGNED) DESC')
            ->value('nik');

        if ($last && preg_match('/^P(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'P'.str_pad($next, 4, '0', STR_PAD_LEFT);
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

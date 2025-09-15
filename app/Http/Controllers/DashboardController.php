<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data ringkasan.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is a driver (supir) - redirect to supir dashboard
        if ($user->isSupir()) {
            return redirect()->route('supir.dashboard');
        }

        // Check if user has any permissions
        $hasPermissions = $user->permissions->count() > 0;

        // If user has no permissions, show special dashboard
        if (!$hasPermissions) {
            return view('dashboard_no_permissions');
        }

        // Menghitung total data dari masing-masing model
        $totalPermohonan = Permohonan::count();
        $totalKaryawan = Karyawan::count();
        // Mengasumsikan kontainer yang tersedia adalah yang kondisinya 'Baik'
        // dan belum terikat pada permohonan yang aktif.
        $kontainerTersedia = Kontainer::where('kondisi_kontainer', 'Baik')->count();

        // Mengirim semua data ke view 'dashboard'
        return view('dashboard', compact(
            'totalPermohonan',
            'totalKaryawan',
            'kontainerTersedia'
        ));
    }
}

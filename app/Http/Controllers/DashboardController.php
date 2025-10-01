<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;
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

        // Check if user has any meaningful permissions (exclude basic auth permissions)
        $meaningfulPermissions = $user->permissions
            ->whereNotIn('name', ['login', 'logout']) // Exclude basic auth permissions
            ->count();

        // If user has no meaningful permissions, show special dashboard
        if ($meaningfulPermissions == 0) {
            return view('dashboard_no_permissions');
        }

        // Only check dashboard permission if user has meaningful permissions
        $this->authorize('dashboard');

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

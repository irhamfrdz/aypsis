<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\Kontainer;
use App\Models\Prospek;
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

        // Data prospek berdasarkan kombinasi tujuan dan ukuran kontainer
        $prospekData = [
            'Jakarta' => [
                '20ft' => $this->getProspekByTujuanUkuran('Jakarta', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Jakarta', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Jakarta', 'CARGO'),
            ],
            'Batam' => [
                '20ft' => $this->getProspekByTujuanUkuran('Batam', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Batam', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Batam', 'CARGO'),
            ],
            'Pinang' => [
                '20ft' => $this->getProspekByTujuanUkuran('Pinang', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Pinang', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Pinang', 'CARGO'),
            ],
        ];

        // Mengirim semua data ke view 'dashboard'
        return view('dashboard', compact('prospekData'));
    }

    /**
     * Menghitung jumlah prospek berdasarkan tujuan dan ukuran kontainer
     */
    private function getProspekByTujuanUkuran($tujuan, $ukuran)
    {
        return Prospek::where('tujuan_pengiriman', 'like', "%{$tujuan}%")
                     ->where('ukuran', $ukuran)
                     ->where(function($query) {
                         $query->whereNull('status')
                               ->orWhere('status', '')
                               ->orWhere('status', 'aktif');
                     })
                     ->count();
    }

    /**
     * Menghitung jumlah prospek berdasarkan tujuan dan tipe (untuk cargo)
     */
    private function getProspekByTujuanTipe($tujuan, $tipe)
    {
        return Prospek::where('tujuan_pengiriman', 'like', "%{$tujuan}%")
                     ->where('tipe', $tipe)
                     ->where(function($query) {
                         $query->whereNull('status')
                               ->orWhere('status', '')
                               ->orWhere('status', 'aktif');
                     })
                     ->count();
    }
}

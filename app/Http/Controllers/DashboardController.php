<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data ringkasan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
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

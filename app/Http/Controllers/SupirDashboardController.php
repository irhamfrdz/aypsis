<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Permohonan;



use Illuminate\Http\Request;

class SupirDashboardController extends Controller
{
    public function index()
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        $supirId = $user->karyawan->id;

        // Ambil permohonan yang sedang berjalan (misalnya status bukan 'Selesai' atau 'Dibatalkan')
        $permohonans = Permohonan::where('supir_id', $supirId)
                     ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                     ->latest()
                     ->get();

    // Build kegiatan map (kode => nama) so view can display human-friendly names
    $kegiatanRows = \App\Models\MasterKegiatan::all();
    $kegiatanMap = $kegiatanRows->pluck('nama_kegiatan', 'kode_kegiatan')->toArray();

    return view('supir.dashboard', compact('permohonans', 'kegiatanMap'));
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Permohonan;
use App\Models\SuratJalan;

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

        $supirId = $user->karyawan->id ?? null;
        $supirUsername = $user->username;
        $supirName = $user->name;  // This will use the accessor

        // Get the karyawan record to get nama_lengkap
        $supirNamaLengkap = $user->karyawan->nama_lengkap ?? $supirUsername;

        // Ambil permohonan yang sedang berjalan (menggunakan supir_id)
        $permohonans = Permohonan::where('supir_id', $supirId)
                     ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                     ->latest()
                     ->get();

        // Ambil surat jalan yang perlu checkpoint atau sedang berjalan (menggunakan nama_lengkap karyawan)
        // Try multiple matching strategies to ensure we find the records
        $suratJalans = SuratJalan::where(function($query) use ($supirNamaLengkap, $supirUsername, $supirName) {
                         $query->where('supir', $supirNamaLengkap)
                               ->orWhere('supir', $supirUsername)
                               ->orWhere('supir', $supirName);
                     })
                     ->whereIn('status', ['belum masuk checkpoint', 'checkpoint_completed'])
                     ->latest()
                     ->get();

        // Debug logging to help diagnose issues
        \Log::info('Supir Dashboard Debug', [
            'user_username' => $supirUsername,
            'user_name' => $supirName,
            'karyawan_nama_lengkap' => $supirNamaLengkap,
            'found_surat_jalans' => $suratJalans->count(),
            'surat_jalan_ids' => $suratJalans->pluck('id')->toArray()
        ]);

        // Build kegiatan map (kode => nama) so view can display human-friendly names
        $kegiatanRows = \App\Models\MasterKegiatan::all();
        $kegiatanMap = $kegiatanRows->pluck('nama_kegiatan', 'kode_kegiatan')->toArray();

        return view('supir.dashboard', compact('permohonans', 'suratJalans', 'kegiatanMap'));
    }
}

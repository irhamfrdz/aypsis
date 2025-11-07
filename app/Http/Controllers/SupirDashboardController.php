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

    public function obMuat()
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Ambil data kapal dari master kapal yang aktif
        $masterKapals = \App\Models\MasterKapal::where('status', 'aktif')
                                              ->orderBy('nama_kapal')
                                              ->get();

        // Ambil data voyage dari pergerakan kapal - lebih fleksibel untuk testing
        // Gunakan lebih banyak status untuk memastikan ada data
        $pergerakanKapals = \App\Models\PergerakanKapal::whereNotNull('voyage')
                                                       ->where('voyage', '!=', '')
                                                       ->orderBy('nama_kapal')
                                                       ->orderBy('voyage')
                                                       ->get();

        // Debug log untuk melihat data yang tersedia
        \Log::info('OB Muat Data Debug', [
            'master_kapals_count' => $masterKapals->count(),
            'pergerakan_kapals_count' => $pergerakanKapals->count(),
            'master_kapal_names' => $masterKapals->pluck('nama_kapal')->toArray(),
            'pergerakan_kapal_names' => $pergerakanKapals->pluck('nama_kapal')->unique()->toArray(),
            'sample_voyages' => $pergerakanKapals->take(5)->map(function($item) {
                return $item->nama_kapal . ' - ' . $item->voyage . ' (' . $item->status . ')';
            })->toArray(),
            'voyage_by_kapal' => $pergerakanKapals->groupBy('nama_kapal')->map(function($group) {
                return $group->pluck('voyage')->toArray();
            })->toArray()
        ]);
        
        // Check name matching between master and pergerakan
        $masterNames = $masterKapals->pluck('nama_kapal')->toArray();
        $pergerakanNames = $pergerakanKapals->pluck('nama_kapal')->unique()->toArray();
        $commonNames = array_intersect($masterNames, $pergerakanNames);
        
        \Log::info('Kapal Name Matching', [
            'common_names' => $commonNames,
            'master_only' => array_diff($masterNames, $pergerakanNames),
            'pergerakan_only' => array_diff($pergerakanNames, $masterNames)
        ]);

        return view('supir.ob-muat', compact('masterKapals', 'pergerakanKapals'));
    }

    public function obMuatStore(Request $request)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Validasi input
        $request->validate([
            'kapal' => 'required|string|max:255',
            'voyage' => 'required|string|max:255',
        ], [
            'kapal.required' => 'Kapal harus dipilih.',
            'voyage.required' => 'Voyage harus dipilih.',
        ]);

        // Verifikasi bahwa voyage exist untuk kapal yang dipilih
        $pergerakanKapal = \App\Models\PergerakanKapal::where('nama_kapal', $request->kapal)
                                                     ->where('voyage', $request->voyage)
                                                     ->first();

        if (!$pergerakanKapal) {
            return back()->withErrors(['voyage' => 'Data voyage tidak ditemukan untuk kapal yang dipilih.'])
                        ->withInput();
        }

        // Log activity untuk audit trail
        \Log::info('OB Muat Data Submitted', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'kapal' => $request->kapal,
            'voyage' => $request->voyage,
            'pergerakan_kapal_id' => $pergerakanKapal->id,
            'timestamp' => now()
        ]);

        // TODO: Implement actual OB Muat logic here
        // For now, just store the session data and redirect back with success
        session([
            'ob_muat_data' => [
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'pergerakan_kapal_id' => $pergerakanKapal->id,
                'submitted_at' => now(),
                'submitted_by' => $user->id
            ]
        ]);

        return back()->with('success', 'Data OB Muat berhasil disubmit untuk Kapal: ' . $request->kapal . ', Voyage: ' . $request->voyage);
    }

    public function obMuatIndex(Request $request)
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Validasi parameter yang required
        $request->validate([
            'kapal' => 'required|string',
            'voyage' => 'required|string',
        ]);

        $selectedKapal = $request->get('kapal');
        $selectedVoyage = $request->get('voyage');

        // Ambil data BL berdasarkan kapal dan voyage
        $bls = \App\Models\Bl::where('nama_kapal', $selectedKapal)
                             ->where('no_voyage', $selectedVoyage)
                             ->whereNotNull('nomor_kontainer')
                             ->where('nomor_kontainer', '!=', '')
                             ->orderBy('nomor_kontainer')
                             ->get();

        // Log untuk debugging
        \Log::info('OB Muat Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return view('supir.ob-muat-index', compact('bls', 'selectedKapal', 'selectedVoyage'));
    }
}

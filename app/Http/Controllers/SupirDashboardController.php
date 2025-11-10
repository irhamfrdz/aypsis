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

    public function obMuatStoreSelection(Request $request)
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

        // Redirect ke halaman index dengan parameter kapal dan voyage
        return redirect()->route('supir.ob-muat.index', [
            'kapal' => $request->kapal,
            'voyage' => $request->voyage
        ])->with('success', 'Data OB Muat berhasil disubmit untuk Kapal: ' . $request->kapal . ', Voyage: ' . $request->voyage);
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

        // Ambil data tagihan OB yang sudah ada untuk kapal dan voyage ini
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->pluck('nomor_kontainer')
                                                  ->toArray();

        // Tambahkan status OB ke setiap BL
        $bls->each(function ($bl) use ($existingTagihanOb) {
            $bl->sudah_ob = in_array($bl->nomor_kontainer, $existingTagihanOb);
        });

        // Log untuk debugging
        \Log::info('OB Muat Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
            'existing_tagihan_ob' => $existingTagihanOb,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return view('supir.ob-muat-index', compact('bls', 'selectedKapal', 'selectedVoyage'));
    }

    public function obMuatProcess(Request $request)
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
            'bl_id' => 'required|exists:bls,id',
        ]);

        $selectedKapal = $request->get('kapal');
        $selectedVoyage = $request->get('voyage');
        $blId = $request->get('bl_id');

        // Ambil data BL
        $bl = \App\Models\Bl::findOrFail($blId);
        $nomorKontainer = $bl->nomor_kontainer;

        // Cek apakah sudah ada tagihan OB untuk kontainer ini
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->where('nomor_kontainer', $nomorKontainer)
                                                  ->first();

        if ($existingTagihanOb) {
            return back()->with('error', 'Tagihan OB untuk kontainer ' . $nomorKontainer . ' sudah ada.');
        }

        // Cari surat jalan terkait untuk mendapatkan data kegiatan/aktifitas
        $suratJalan = \App\Models\SuratJalan::where('no_kontainer', $nomorKontainer)
                                           ->where('supir', $user->name)
                                           ->where(function($query) {
                                               $query->whereNotNull('aktifitas')
                                                     ->orWhereNotNull('kegiatan');
                                           })
                                           ->first();

        // Tentukan status kontainer berdasarkan kegiatan pada surat jalan
        $statusKontainer = 'empty'; // default
        if ($suratJalan) {
            // Prioritas aktifitas, fallback ke kegiatan
            $kegiatan = $suratJalan->aktifitas ?: $suratJalan->kegiatan;
            if ($kegiatan) {
                $statusKontainer = \App\Models\TagihanOb::getStatusKontainerFromKegiatan($kegiatan);
            }
        }

        try {
            // Buat TagihanOb baru secara otomatis
            $tagihanOb = new \App\Models\TagihanOb();
            $tagihanOb->kapal = $selectedKapal;
            $tagihanOb->voyage = $selectedVoyage;
            $tagihanOb->nomor_kontainer = $nomorKontainer;
            $tagihanOb->nama_supir = $user->name;
            $tagihanOb->barang = $bl->nama_barang ?? 'General Cargo';
            $tagihanOb->status_kontainer = $statusKontainer;
            $tagihanOb->bl_id = $bl->id;
            $tagihanOb->created_by = $user->id;
            
            // Hitung biaya otomatis dari pricelist
            $sizeKontainer = $bl->tipe_kontainer ?? '20ft'; // Default 20ft jika tidak ada
            $biaya = \App\Models\TagihanOb::calculateBiayaFromPricelist($sizeKontainer, $statusKontainer);
            $tagihanOb->biaya = $biaya;
            
            $tagihanOb->save();

            // Log activity untuk audit trail
            \Log::info('TagihanOb Created via OB Muat Process', [
                'tagihan_ob_id' => $tagihanOb->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'nomor_kontainer' => $nomorKontainer,
                'kegiatan_surat_jalan' => $kegiatan ?? null,
                'status_kontainer' => $statusKontainer,
                'size_kontainer' => $sizeKontainer,
                'biaya' => $biaya,
                'timestamp' => now()
            ]);

            return redirect()->route('supir.ob-muat.index', [
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ])->with('success', 'OB Muat berhasil diproses! Tagihan OB untuk kontainer ' . $nomorKontainer . ' telah dibuat dengan status: ' . ucfirst($statusKontainer) . ', Biaya: Rp ' . number_format($biaya, 0, ',', '.'));

        } catch (\Exception $e) {
            \Log::error('Error processing OB Muat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'bl_id' => $blId,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses OB Muat. Silakan coba lagi.');
        }
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
            'nomor_kontainer' => 'required|string|max:255',
            'barang' => 'required|string|max:255',
            'status_kontainer' => 'required|in:full,empty',
            'size_kontainer' => 'required|string',
            'biaya' => 'required|numeric|min:0',
        ], [
            'kapal.required' => 'Kapal harus diisi.',
            'voyage.required' => 'Voyage harus diisi.',
            'nomor_kontainer.required' => 'Nomor kontainer harus diisi.',
            'barang.required' => 'Jenis barang harus diisi.',
            'status_kontainer.required' => 'Status kontainer harus dipilih.',
            'size_kontainer.required' => 'Size kontainer harus diisi.',
            'biaya.required' => 'Biaya harus diisi.',
            'biaya.numeric' => 'Biaya harus berupa angka.',
            'biaya.min' => 'Biaya tidak boleh kurang dari 0.',
        ]);

        // Cek apakah sudah ada tagihan OB untuk kontainer ini
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $request->kapal)
                                                  ->where('voyage', $request->voyage)
                                                  ->where('nomor_kontainer', $request->nomor_kontainer)
                                                  ->first();

        if ($existingTagihanOb) {
            return back()->withErrors(['nomor_kontainer' => 'Tagihan OB untuk kontainer ini sudah ada.'])
                        ->withInput();
        }

        // Ambil data BL untuk referensi
        $bl = \App\Models\Bl::where('nama_kapal', $request->kapal)
                           ->where('no_voyage', $request->voyage)
                           ->where('nomor_kontainer', $request->nomor_kontainer)
                           ->first();

        try {
            // Simpan tagihan OB
            $tagihanOb = \App\Models\TagihanOb::create([
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'nomor_kontainer' => $request->nomor_kontainer,
                'nama_supir' => $user->name,
                'barang' => $request->barang,
                'status_kontainer' => $request->status_kontainer,
                'size_kontainer' => $request->size_kontainer,
                'biaya' => $request->biaya,
                'bl_id' => $bl ? $bl->id : null,
                'created_by' => $user->id,
                'keterangan' => $request->keterangan,
            ]);

            // Log activity untuk audit trail
            \Log::info('TagihanOb Created', [
                'tagihan_ob_id' => $tagihanOb->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'nomor_kontainer' => $request->nomor_kontainer,
                'status_kontainer' => $request->status_kontainer,
                'biaya' => $request->biaya,
                'timestamp' => now()
            ]);

            return redirect()->route('supir.ob-muat.index', [
                'kapal' => $request->kapal,
                'voyage' => $request->voyage
            ])->with('success', 'Data OB Muat berhasil disimpan ke dalam tagihan OB. Kontainer: ' . $request->nomor_kontainer . ', Status: ' . ucfirst($request->status_kontainer) . ', Biaya: Rp ' . number_format($request->biaya, 0, ',', '.'));

        } catch (\Exception $e) {
            \Log::error('Error creating TagihanOb', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'])
                        ->withInput();
        }
    }
}

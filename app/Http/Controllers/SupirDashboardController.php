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
        // Hanya tampilkan surat jalan yang sudah ada pembayaran pranota uang jalan
        $suratJalans = SuratJalan::where(function($query) use ($supirNamaLengkap, $supirUsername, $supirName) {
                         $query->where('supir', $supirNamaLengkap)
                               ->orWhere('supir', $supirUsername)
                               ->orWhere('supir', $supirName);
                     })
                     ->whereIn('status', ['belum masuk checkpoint', 'checkpoint_completed'])
                     ->where('status_pembayaran_uang_jalan', 'dibayar') // Hanya yang sudah dibayar
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

        // Ambil data voyage dari naik_kapal - lebih akurat karena data real
        // Gunakan distinct untuk menghindari duplikasi dan group by untuk unique voyage
        $naikKapals = \App\Models\NaikKapal::whereNotNull('no_voyage')
                                           ->whereNotNull('nama_kapal')
                                           ->where('no_voyage', '!=', '')
                                           ->where('nama_kapal', '!=', '')
                                           ->select('nama_kapal', 'no_voyage', 
                                                   \DB::raw('MAX(tanggal_muat) as tanggal_muat'),
                                                   \DB::raw('MAX(pelabuhan_tujuan) as pelabuhan_tujuan'))
                                           ->groupBy('nama_kapal', 'no_voyage')
                                           ->orderBy('nama_kapal')
                                           ->orderBy('no_voyage')
                                           ->get();

        // Debug log untuk melihat data yang tersedia
        \Log::info('OB Muat Data Debug (Naik Kapal)', [
            'master_kapals_count' => $masterKapals->count(),
            'naik_kapals_count' => $naikKapals->count(),
            'master_kapal_names' => $masterKapals->pluck('nama_kapal')->toArray(),
            'naik_kapal_names' => $naikKapals->pluck('nama_kapal')->unique()->toArray(),
            'sample_voyages' => $naikKapals->take(5)->map(function($item) {
                return $item->nama_kapal . ' - ' . $item->no_voyage . ' (' . $item->tanggal_muat . ')';
            })->toArray(),
            'voyage_by_kapal' => $naikKapals->groupBy('nama_kapal')->map(function($group) {
                return $group->pluck('no_voyage')->unique()->toArray();
            })->toArray()
        ]);
        
        // Check name matching between master and naik_kapal
        $masterNames = $masterKapals->pluck('nama_kapal')->toArray();
        $naikKapalNames = $naikKapals->pluck('nama_kapal')->unique()->toArray();
        $commonNames = array_intersect($masterNames, $naikKapalNames);
        
        \Log::info('Kapal Name Matching (Naik Kapal)', [
            'common_names' => $commonNames,
            'master_only' => array_diff($masterNames, $naikKapalNames),
            'naik_kapal_only' => array_diff($naikKapalNames, $masterNames)
        ]);

        return view('supir.ob-muat', compact('masterKapals', 'naikKapals'));
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

        // Ambil data dari tabel naik_kapal berdasarkan kapal dan voyage
        $bls = \App\Models\NaikKapal::where('nama_kapal', $selectedKapal)
                             ->where('no_voyage', $selectedVoyage)
                             ->whereNotNull('nomor_kontainer')
                             ->where('nomor_kontainer', '!=', '')
                             ->orderBy('nomor_kontainer')
                             ->get();

        // Ambil data tagihan OB yang sudah ada untuk kapal dan voyage ini (untuk OB Muat)
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->where('kegiatan', 'muat')
                                                  ->pluck('nomor_kontainer')
                                                  ->toArray();

        // Tambahkan status OB ke setiap naik_kapal
        $bls->each(function ($naikKapal) use ($existingTagihanOb) {
            // Field sudah sesuai dengan view (nomor_kontainer sudah ada)
            $naikKapal->nama_barang = $naikKapal->jenis_barang ?? '-';
            
            // Status OB dianggap TRUE jika:
            // 1. Field sudah_ob di database naik_kapal = true, ATAU
            // 2. Sudah ada tagihan OB untuk kontainer ini
            $naikKapal->sudah_ob = ($naikKapal->sudah_ob == true) || in_array($naikKapal->nomor_kontainer, $existingTagihanOb);
        });

        // Log untuk debugging
        \Log::info('OB Muat Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
            'existing_tagihan_ob' => $existingTagihanOb,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'source_table' => 'naik_kapal'
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
            'naik_kapal_id' => 'required|exists:naik_kapal,id',
        ]);

        $selectedKapal = $request->get('kapal');
        $selectedVoyage = $request->get('voyage');
        $naikKapalId = $request->get('naik_kapal_id');

        // Ambil data NaikKapal
        $naikKapal = \App\Models\NaikKapal::findOrFail($naikKapalId);
        $nomorKontainer = $naikKapal->nomor_kontainer;

        // Cek apakah sudah ada tagihan OB untuk kontainer ini (OB Muat)
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->where('nomor_kontainer', $nomorKontainer)
                                                  ->where('kegiatan', 'muat')
                                                  ->first();

        if ($existingTagihanOb) {
            return back()->with('error', 'Tagihan OB Muat untuk kontainer ' . $nomorKontainer . ' sudah ada.');
        }

        // Cari surat jalan terkait untuk mendapatkan data kegiatan/aktifitas
        \Log::info('Searching SuratJalan', [
            'no_kontainer' => $nomorKontainer,
            'supir' => $user->name,
        ]);
        
        $suratJalan = \App\Models\SuratJalan::where('no_kontainer', $nomorKontainer)
                                           ->where('supir', $user->name)
                                           ->where(function($query) {
                                               $query->whereNotNull('aktifitas')
                                                     ->orWhereNotNull('kegiatan');
                                           })
                                           ->first();
                                           
        \Log::info('SuratJalan found', [
            'found' => $suratJalan ? true : false,
            'kegiatan' => $suratJalan->kegiatan ?? null,
            'size' => $suratJalan->size ?? null,
        ]);

        // Tentukan status kontainer berdasarkan kegiatan pada surat jalan
        $statusKontainer = 'empty'; // default
        $kegiatan = null;
        
        if ($suratJalan) {
            // Prioritas aktifitas, fallback ke kegiatan
            $kegiatan = $suratJalan->aktifitas ?: $suratJalan->kegiatan;
            if ($kegiatan) {
                $statusKontainer = \App\Models\TagihanOb::getStatusKontainerFromKegiatan($kegiatan);
            }
        } else {
            // Jika tidak ditemukan dengan supir yang sama, coba cari tanpa filter supir
            $suratJalanAlt = \App\Models\SuratJalan::where('no_kontainer', $nomorKontainer)
                                                   ->whereNotNull('kegiatan')
                                                   ->first();
            \Log::info('Alternative SuratJalan search', [
                'found' => $suratJalanAlt ? true : false,
                'kegiatan' => $suratJalanAlt->kegiatan ?? null,
                'supir' => $suratJalanAlt->supir ?? null,
            ]);
            
            if ($suratJalanAlt) {
                $suratJalan = $suratJalanAlt; // Use alternative surat jalan
                $kegiatan = $suratJalanAlt->kegiatan;
                $statusKontainer = \App\Models\TagihanOb::getStatusKontainerFromKegiatan($kegiatan);
            }
        }

        try {
            // Buat TagihanOb baru secara otomatis
            $tagihanOb = new \App\Models\TagihanOb();
            $tagihanOb->kapal = $selectedKapal;
            $tagihanOb->voyage = $selectedVoyage;
            $tagihanOb->kegiatan = 'muat';
            $tagihanOb->nomor_kontainer = $nomorKontainer;
            $tagihanOb->nama_supir = $user->name;
            $tagihanOb->barang = $naikKapal->jenis_barang ?? 'General Cargo';
            $tagihanOb->status_kontainer = $statusKontainer;
            $tagihanOb->naik_kapal_id = $naikKapal->id;
            $tagihanOb->created_by = $user->id;
            
            // Ambil size kontainer dari naik_kapal, fallback ke surat jalan
            $sizeKontainer = '20ft'; // default
            if ($naikKapal->size_kontainer && in_array($naikKapal->size_kontainer, ['20ft', '40ft'])) {
                $sizeKontainer = $naikKapal->size_kontainer;
            } elseif ($suratJalan && $suratJalan->size) {
                // Convert size dari surat jalan (20, 40) ke format pricelist (20ft, 40ft)
                $sizeKontainer = $suratJalan->size . 'ft';
            }
            
            // Hitung biaya otomatis dari pricelist
            $biaya = \App\Models\TagihanOb::calculateBiayaFromPricelist($sizeKontainer, $statusKontainer);
            $tagihanOb->biaya = $biaya;
            
            $tagihanOb->save();

            // Update status OB pada tabel naik_kapal
            $naikKapal->sudah_ob = true;
            $naikKapal->save();

            // Log activity untuk audit trail
            \Log::info('TagihanOb Created via OB Muat Process', [
                'tagihan_ob_id' => $tagihanOb->id,
                'naik_kapal_id' => $naikKapal->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'nomor_kontainer' => $nomorKontainer,
                'kegiatan' => 'muat',
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
                'naik_kapal_id' => $naikKapalId,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses OB Muat: ' . $e->getMessage());
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

    /**
     * Display OB Bongkar form (Kapal & Voyage selection)
     */
    public function obBongkar()
    {
        // Pastikan user yang login adalah karyawan dengan divisi supir
        $user = Auth::user();
        if (!$user->isSupir()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Ambil data master kapal untuk dropdown
        $masterKapals = \App\Models\MasterKapal::orderBy('nama_kapal')->get();
        
        // Ambil data dari BL untuk bongkar kontainer (dikelompokkan berdasarkan kapal dan voyage)
        // Hanya ambil kolom yang ada di tabel bls
        $blsData = \App\Models\Bl::select('nama_kapal', 'no_voyage')
                                  ->whereNotNull('nama_kapal')
                                  ->whereNotNull('no_voyage')
                                  ->where('nama_kapal', '!=', '')
                                  ->where('no_voyage', '!=', '')
                                  ->groupBy('nama_kapal', 'no_voyage')
                                  ->orderBy('nama_kapal')
                                  ->orderBy('no_voyage')
                                  ->get();

        \Log::info('OB Bongkar Page Accessed', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'master_kapals_count' => $masterKapals->count(),
            'bls_data_count' => $blsData->count()
        ]);

        return view('supir.ob-bongkar', compact('masterKapals'));
    }

    /**
     * Store OB Bongkar selection and redirect to index
     */
    public function obBongkarStore(Request $request)
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

        // Verifikasi bahwa voyage exist untuk kapal yang dipilih di BL
        $blExists = \App\Models\Bl::where('nama_kapal', $request->kapal)
                                  ->where('no_voyage', $request->voyage)
                                  ->exists();

        if (!$blExists) {
            return back()->withErrors(['voyage' => 'Data voyage tidak ditemukan untuk kapal yang dipilih.'])
                        ->withInput();
        }

        // Log activity untuk audit trail
        \Log::info('OB Bongkar Data Submitted', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'kapal' => $request->kapal,
            'voyage' => $request->voyage,
            'timestamp' => now()
        ]);

        // Redirect ke halaman index dengan parameter kapal dan voyage
        return redirect()->route('supir.ob-bongkar.index', [
            'kapal' => $request->kapal,
            'voyage' => $request->voyage
        ])->with('success', 'Data OB Bongkar berhasil disubmit untuk Kapal: ' . $request->kapal . ', Voyage: ' . $request->voyage);
    }

    /**
     * Display OB Bongkar index with BL list
     */
    public function obBongkarIndex(Request $request)
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

        // Ambil data BL berdasarkan kapal dan voyage untuk bongkar
        $bls = \App\Models\Bl::where('nama_kapal', $selectedKapal)
                             ->where('no_voyage', $selectedVoyage)
                             ->whereNotNull('nomor_kontainer')
                             ->where('nomor_kontainer', '!=', '')
                             ->orderBy('nomor_kontainer')
                             ->get();

        // Ambil data tagihan OB yang sudah ada untuk kapal dan voyage ini
        // Note: Tabel tagihan_ob tidak memiliki kolom 'kegiatan', jadi kita cek berdasarkan kapal dan voyage saja
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->pluck('nomor_kontainer')
                                                  ->toArray();

        // Tambahkan status OB ke setiap BL
        $bls->each(function ($bl) use ($existingTagihanOb) {
            $bl->sudah_ob = in_array($bl->nomor_kontainer, $existingTagihanOb);
        });

        // Log untuk debugging
        \Log::info('OB Bongkar Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
            'existing_tagihan_ob' => $existingTagihanOb,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return view('supir.ob-bongkar-index', compact('bls', 'selectedKapal', 'selectedVoyage'));
    }

    /**
     * Process OB Bongkar data and create TagihanOb
     */
    public function obBongkarProcess(Request $request)
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

        // Cek apakah sudah ada tagihan OB untuk kontainer ini (OB Bongkar)
        $existingTagihanOb = \App\Models\TagihanOb::where('kapal', $selectedKapal)
                                                  ->where('voyage', $selectedVoyage)
                                                  ->where('nomor_kontainer', $nomorKontainer)
                                                  ->where('kegiatan', 'bongkar')
                                                  ->first();

        if ($existingTagihanOb) {
            return back()->with('error', 'Tagihan OB Bongkar untuk kontainer ' . $nomorKontainer . ' sudah ada.');
        }

        try {
            // Ambil size kontainer dari BL
            $sizeKontainer = '20ft'; // default
            if ($bl->tipe_kontainer && in_array($bl->tipe_kontainer, ['20ft', '40ft'])) {
                $sizeKontainer = $bl->tipe_kontainer;
            } elseif ($bl->size) {
                // Convert size dari BL (20, 40) ke format pricelist (20ft, 40ft)
                $sizeKontainer = $bl->size . 'ft';
            }
            
            // Default status kontainer untuk bongkar adalah 'full'
            $statusKontainer = 'full';
            
            // Hitung biaya otomatis dari pricelist
            $biaya = \App\Models\TagihanOb::calculateBiayaFromPricelist($sizeKontainer, $statusKontainer);
            
            // Buat tagihan OB baru untuk bongkar
            $tagihanOb = new \App\Models\TagihanOb();
            $tagihanOb->kapal = $selectedKapal;
            $tagihanOb->voyage = $selectedVoyage;
            $tagihanOb->kegiatan = 'bongkar';
            $tagihanOb->nomor_kontainer = $nomorKontainer;
            $tagihanOb->nama_supir = $user->name;
            $tagihanOb->bl_id = $blId;
            $tagihanOb->created_by = $user->id;
            $tagihanOb->size_kontainer = $sizeKontainer;
            $tagihanOb->barang = $bl->nama_barang ?? 'General Cargo';
            $tagihanOb->status_kontainer = $statusKontainer;
            $tagihanOb->biaya = $biaya;
            $tagihanOb->keterangan = 'OB Bongkar - dibuat oleh supir';
            
            $tagihanOb->save();

            // Update status OB pada tabel bls
            $bl->sudah_ob = true;
            $bl->save();

            \Log::info('TagihanOb Bongkar Created', [
                'tagihan_ob_id' => $tagihanOb->id,
                'bl_id' => $bl->id,
                'nomor_kontainer' => $nomorKontainer,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'kegiatan' => 'bongkar',
                'supir' => $user->name,
                'size_kontainer' => $sizeKontainer,
                'status_kontainer' => $statusKontainer,
                'biaya' => $biaya
            ]);

            return redirect()->route('supir.ob-bongkar.index', [
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ])->with('success', 'OB Bongkar berhasil diproses! Tagihan OB untuk kontainer ' . $nomorKontainer . ' telah dibuat dengan status: ' . ucfirst($statusKontainer) . ', Biaya: Rp ' . number_format($biaya, 0, ',', '.'));

        } catch (\Exception $e) {
            \Log::error('Error creating TagihanOb Bongkar', [
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

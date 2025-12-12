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

        // Prepare voyage data as a plain PHP array for JS injection
        $voyageData = $naikKapals->groupBy('nama_kapal')->map(function($voyages) {
            return $voyages->map(function($v) {
                return [
                    'voyage' => $v->no_voyage,
                    'tanggal_muat' => $v->tanggal_muat ? $v->tanggal_muat->format('d/m/Y') : '-',
                    'pelabuhan_tujuan' => $v->pelabuhan_tujuan ?? '-',
                    'jenis_barang' => $v->jenis_barang ?? '-',
                ];
            })->values()->toArray();
        })->toArray();

        return view('supir.ob-muat', compact('masterKapals', 'naikKapals', 'voyageData'));
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
        // Status sudah_ob langsung dibaca dari database
        $bls = \App\Models\NaikKapal::where('nama_kapal', $selectedKapal)
                             ->where('no_voyage', $selectedVoyage)
                             ->whereNotNull('nomor_kontainer')
                             ->where('nomor_kontainer', '!=', '')
                             ->orderBy('nomor_kontainer')
                             ->get();

        // Tambahkan field nama_barang untuk view
        $bls->each(function ($naikKapal) {
            $naikKapal->nama_barang = $naikKapal->jenis_barang ?? '-';
        });

        // Log untuk debugging dengan detail status OB setiap kontainer
        \Log::info('OB Muat Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
            'sudah_ob_status' => $bls->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kontainer' => $item->nomor_kontainer,
                    'sudah_ob' => $item->sudah_ob,
                    'supir_id' => $item->supir_id,
                    'tanggal_ob' => $item->tanggal_ob
                ];
            })->toArray(),
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

        // Validasi apakah user memiliki karyawan_id
        if (!$user->karyawan_id) {
            \Log::error('OB Muat Process - No Karyawan ID', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'karyawan_id' => null
            ]);
            return back()->with('error', '❌ Akun Anda belum terhubung dengan data karyawan. Silakan hubungi administrator.');
        }

        // Validasi apakah karyawan dengan ID tersebut ada
        $karyawan = \App\Models\Karyawan::find($user->karyawan_id);
        if (!$karyawan) {
            \Log::error('OB Muat Process - Karyawan Not Found', [
                'user_id' => $user->id,
                'karyawan_id' => $user->karyawan_id
            ]);
            return back()->with('error', '❌ Data karyawan tidak ditemukan. Silakan hubungi administrator.');
        }

        // Ambil data NaikKapal
        $naikKapal = \App\Models\NaikKapal::findOrFail($naikKapalId);
        $nomorKontainer = $naikKapal->nomor_kontainer;

        // Cek apakah kontainer sudah di-OB sebelumnya
        if ($naikKapal->sudah_ob) {
            return back()->with('error', 'Kontainer ' . $nomorKontainer . ' sudah pernah di-OB sebelumnya.');
        }

        try {
            \DB::beginTransaction();
            
            // Log status sebelum update
            \Log::info('OB Muat Process - Before Update', [
                'naik_kapal_id' => $naikKapal->id,
                'sudah_ob_before' => $naikKapal->sudah_ob,
                'nomor_kontainer' => $naikKapal->nomor_kontainer
            ]);
            
            // Update status OB pada tabel naik_kapal dan mark driver & timestamp
            $naikKapal->sudah_ob = true;
            $naikKapal->supir_id = $user->karyawan_id; // Gunakan karyawan_id dari user
            $naikKapal->tanggal_ob = now();
            $naikKapal->catatan_ob = 'OB Muat - diproses oleh ' . $user->name . ' (Karyawan ID: ' . $user->karyawan_id . ')';
            $naikKapal->updated_by = $user->id;
            $saved = $naikKapal->save();
            
            // Verifikasi save berhasil
            if (!$saved) {
                throw new \Exception('Gagal menyimpan status OB pada naik_kapal');
            }
            
            // Refresh dari database untuk memastikan data tersimpan
            $naikKapal->refresh();
            
            // Log dan verifikasi status setelah save
            \Log::info('OB Muat Process - After Update', [
                'naik_kapal_id' => $naikKapal->id,
                'sudah_ob_after' => $naikKapal->sudah_ob,
                'save_result' => $saved,
                'nomor_kontainer' => $naikKapal->nomor_kontainer
            ]);
            
            // Double check - jika masih false, throw error
            if (!$naikKapal->sudah_ob) {
                throw new \Exception('Status sudah_ob gagal tersimpan - nilai masih false setelah refresh dari database');
            }

            // Ambil data prospek dengan relasi tanda terima dan surat jalan
            $prospek = null;
            $tandaTerima = null;
            $suratJalan = null;
            
            if ($naikKapal->prospek_id) {
                $prospek = \App\Models\Prospek::with(['tandaTerima', 'suratJalan'])->find($naikKapal->prospek_id);
                
                if ($prospek) {
                    // Update status prospek menjadi sudah_muat
                    $prospek->status = 'sudah_muat';
                    $prospek->updated_by = $user->id;
                    $prospek->save();
                    
                    // Ambil data tanda terima jika ada
                    $tandaTerima = $prospek->tandaTerima;
                    $suratJalan = $prospek->suratJalan;
                    
                    \Log::info('OB Muat Process - Prospek Status Updated', [
                        'prospek_id' => $prospek->id,
                        'old_status' => 'aktif',
                        'new_status' => 'sudah_muat',
                        'has_tanda_terima' => !is_null($tandaTerima),
                        'has_surat_jalan' => !is_null($suratJalan)
                    ]);
                }
            }

            // Buat record di tabel bls dengan data lengkap dari prospek dan tanda terima
            $blData = [
                'naik_kapal_id' => $naikKapal->id,
                'prospek_id' => $naikKapal->prospek_id,
                'nomor_kontainer' => $naikKapal->nomor_kontainer,
                'no_seal' => $naikKapal->no_seal ?? ($prospek ? $prospek->no_seal : null),
                'tipe_kontainer' => $naikKapal->tipe_kontainer ?? ($prospek ? $prospek->tipe : null),
                'size_kontainer' => $naikKapal->ukuran_kontainer ?? ($prospek ? $prospek->ukuran : null),
                'nama_kapal' => $naikKapal->nama_kapal,
                'no_voyage' => $naikKapal->no_voyage,
                'pelabuhan_asal' => $naikKapal->pelabuhan_asal,
                'pelabuhan_tujuan' => $naikKapal->pelabuhan_tujuan,
                'nama_barang' => $naikKapal->jenis_barang ?? ($prospek ? $prospek->barang : null),
                'volume' => $naikKapal->total_volume ?? ($prospek ? $prospek->total_volume : null),
                'tonnage' => $naikKapal->total_tonase ?? ($prospek ? $prospek->total_ton : null),
                'kuantitas' => $naikKapal->kuantitas ?? ($prospek ? $prospek->kuantitas : null),
                'supir_id' => $user->karyawan_id,
                'supir_ob' => $prospek ? $prospek->nama_supir : null,
                'tanggal_ob' => null,  // Belum di-OB, akan diisi saat bongkar
                'sudah_ob' => false,  // BL baru dibuat, statusnya belum OB (akan di-mark saat bongkar)
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];
            
            // Tambahkan data dari tanda terima jika ada
            if ($tandaTerima) {
                $blData['pengirim'] = $tandaTerima->pengirim ?? ($prospek ? $prospek->pt_pengirim : null);
                $blData['penerima'] = $tandaTerima->penerima;
                $blData['alamat_pengiriman'] = $tandaTerima->alamat_penerima;
                $blData['contact_person'] = $tandaTerima->contact_person;
                $blData['term'] = $tandaTerima->term;
                $blData['satuan'] = $tandaTerima->satuan;
            } else if ($prospek) {
                // Jika tidak ada tanda terima, ambil dari prospek
                $blData['pengirim'] = $prospek->pt_pengirim;
            }
            
            $bl = \App\Models\Bl::create($blData);

            \DB::commit();
            
            // Verifikasi final setelah commit - query langsung dari database
            $verifikasi = \App\Models\NaikKapal::find($naikKapal->id);
            
            // Log activity untuk debugging
            \Log::info('OB Muat Process - Completed', [
                'naik_kapal_id' => $naikKapal->id,
                'nomor_kontainer' => $nomorKontainer,
                'bl_id' => $bl->id,
                'prospek_updated' => isset($prospek),
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'supir' => $user->name,
                'sudah_ob_verified' => $verifikasi->sudah_ob,
                'supir_id_verified' => $verifikasi->supir_id,
                'tanggal_ob_verified' => $verifikasi->tanggal_ob,
                'timestamp' => now()
            ]);

            return redirect()->route('supir.ob-muat.index', [
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ])->with('success', 'OB Muat berhasil diproses untuk kontainer ' . $nomorKontainer . '! Data BL telah dibuat.');

        } catch (\Exception $e) {
            \DB::rollBack();
            
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
        // Status sudah_ob langsung dibaca dari database
        $bls = \App\Models\Bl::where('nama_kapal', $selectedKapal)
                             ->where('no_voyage', $selectedVoyage)
                             ->whereNotNull('nomor_kontainer')
                             ->where('nomor_kontainer', '!=', '')
                             ->orderBy('nomor_kontainer')
                             ->get();

        // Log untuk debugging
        \Log::info('OB Bongkar Index Data', [
            'selected_kapal' => $selectedKapal,
            'selected_voyage' => $selectedVoyage,
            'found_containers' => $bls->count(),
            'container_numbers' => $bls->pluck('nomor_kontainer')->toArray(),
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
            \Log::warning('OB Bongkar Process - Unauthorized Access', [
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? null,
                'is_supir' => false
            ]);
            abort(403, 'Akses ditolak. Halaman ini hanya untuk supir.');
        }

        // Validasi parameter yang required
        try {
            $request->validate([
                'kapal' => 'required|string',
                'voyage' => 'required|string',
                'bl_id' => 'required|exists:bls,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('OB Bongkar Process - Validation Failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_id' => $user->id
            ]);
            return back()->with('error', 'Data tidak lengkap. Pastikan kapal, voyage, dan kontainer sudah dipilih.');
        }

        $selectedKapal = $request->get('kapal');
        $selectedVoyage = $request->get('voyage');
        $blId = $request->get('bl_id');

        // Validasi apakah user memiliki karyawan_id
        if (!$user->karyawan_id) {
            \Log::error('OB Bongkar Process - No Karyawan ID', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'karyawan_id' => null,
                'error' => 'User tidak memiliki karyawan_id',
                'solution' => 'Set karyawan_id di tabel users untuk user ini'
            ]);
            return back()->with('error', '❌ Akun Anda (User ID: ' . $user->id . ' - ' . $user->name . ') belum terhubung dengan data karyawan. Silakan hubungi administrator untuk mengatur karyawan_id di akun Anda.');
        }

        // Validasi apakah karyawan dengan ID tersebut ada
        $karyawan = \App\Models\Karyawan::find($user->karyawan_id);
        if (!$karyawan) {
            \Log::error('OB Bongkar Process - Karyawan Not Found', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'karyawan_id' => $user->karyawan_id,
                'error' => 'Karyawan dengan ID ' . $user->karyawan_id . ' tidak ditemukan di tabel karyawans'
            ]);
            return back()->with('error', '❌ Data karyawan (ID: ' . $user->karyawan_id . ') tidak ditemukan di sistem. Silakan hubungi administrator.');
        }

        // Log request awal
        \Log::info('OB Bongkar Process - Started', [
            'bl_id' => $blId,
            'kapal' => $selectedKapal,
            'voyage' => $selectedVoyage,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'karyawan_id' => $karyawan->id,
            'karyawan_nama' => $karyawan->nama_lengkap ?? $karyawan->nama_panggilan
        ]);

        // Ambil data BL dengan detailed logging
        try {
            $bl = \App\Models\Bl::findOrFail($blId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('OB Bongkar Process - BL Not Found', [
                'bl_id' => $blId,
                'error' => 'Data kontainer tidak ditemukan di database'
            ]);
            return back()->with('error', 'Data kontainer tidak ditemukan. Silakan refresh halaman dan coba lagi.');
        }

        $nomorKontainer = $bl->nomor_kontainer;

        // Log status BL sebelum proses
        \Log::info('OB Bongkar Process - BL Status Before', [
            'bl_id' => $bl->id,
            'nomor_kontainer' => $nomorKontainer,
            'sudah_ob_before' => $bl->sudah_ob,
            'supir_id_before' => $bl->supir_id,
            'tanggal_ob_before' => $bl->tanggal_ob,
            'updated_at_before' => $bl->updated_at
        ]);

        // Cek apakah kontainer sudah di-OB sebelumnya
        if ($bl->sudah_ob) {
            $supirName = $bl->supir ? $bl->supir->nama_panggilan : 'Unknown';
            $tanggalOb = $bl->tanggal_ob ? $bl->tanggal_ob->format('d-m-Y H:i') : 'Unknown';
            
            \Log::warning('OB Bongkar Process - Already OB', [
                'bl_id' => $bl->id,
                'nomor_kontainer' => $nomorKontainer,
                'sudah_ob' => true,
                'supir_sebelumnya' => $supirName,
                'tanggal_ob_sebelumnya' => $tanggalOb
            ]);
            
            return back()->with('error', 'Kontainer ' . $nomorKontainer . ' sudah pernah di-OB oleh ' . $supirName . ' pada ' . $tanggalOb . '. Tidak dapat OB ulang.');
        }

        try {
            // Update status OB pada tabel bls dan mark driver & timestamp
            $bl->sudah_ob = true;
            $bl->supir_id = $user->karyawan_id; // Gunakan karyawan_id dari user
            $bl->tanggal_ob = now();
            $bl->catatan_ob = 'OB Bongkar - diproses oleh ' . $user->name . ' (Karyawan ID: ' . $user->karyawan_id . ')';
            $bl->updated_by = $user->id;
            
            // Attempt to save with detailed error handling
            $saved = $bl->save();

            // Verify save was successful
            if (!$saved) {
                throw new \Exception('Model save() returned false - data gagal tersimpan ke database');
            }

            // Refresh from database to confirm
            $bl->refresh();

            // Verify data was actually updated in database
            if (!$bl->sudah_ob) {
                throw new \Exception('Status sudah_ob gagal tersimpan - nilai masih false setelah refresh dari database');
            }

            // Log success dengan detail lengkap
            \Log::info('OB Bongkar Process - SUCCESS', [
                'bl_id' => $bl->id,
                'nomor_kontainer' => $nomorKontainer,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'supir' => $user->name,
                'supir_id' => $user->id,
                'tanggal_ob' => $bl->tanggal_ob,
                'sudah_ob_after' => $bl->sudah_ob,
                'updated_at_after' => $bl->updated_at,
                'save_result' => $saved,
                'verified' => 'Data berhasil tersimpan dan terverifikasi',
                'timestamp' => now()
            ]);

            return redirect()->route('supir.ob-bongkar.index', [
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage
            ])->with('success', '✓ OB Bongkar berhasil! Kontainer ' . $nomorKontainer . ' telah diproses oleh ' . $user->name . ' pada ' . now()->format('d-m-Y H:i'));

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('OB Bongkar Process - Database Error', [
                'error_type' => 'QueryException',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'sql_state' => $e->errorInfo[0] ?? null,
                'bl_id' => $blId,
                'nomor_kontainer' => $nomorKontainer,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', '❌ Gagal menyimpan ke database. Error: ' . $e->getMessage() . '. Silakan hubungi administrator atau coba lagi.');

        } catch (\Exception $e) {
            \Log::error('OB Bongkar Process - General Error', [
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'bl_id' => $blId,
                'nomor_kontainer' => $nomorKontainer,
                'kapal' => $selectedKapal,
                'voyage' => $selectedVoyage,
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage() . '. Silakan screenshot error ini dan hubungi administrator.');
        }
    }
}

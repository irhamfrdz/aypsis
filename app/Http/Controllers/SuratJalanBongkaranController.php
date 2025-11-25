<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaran;
use App\Models\Order;
use App\Models\MasterKapal;
use App\Models\Bl;
use App\Models\User;
use App\Models\TujuanKegiatanUtama;
use App\Models\MasterKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratJalanBongkaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:surat-jalan-bongkaran-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:surat-jalan-bongkaran-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:surat-jalan-bongkaran-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:surat-jalan-bongkaran-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalanBongkaran::with(['kapal', 'user']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        // Filter berdasarkan kapal
        if ($request->filled('kapal_id')) {
            $query->where('kapal_id', $request->kapal_id);
        }

        // Filter berdasarkan order
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('nomor_container', 'like', "%{$search}%")
                  ->orWhere('nomor_seal', 'like', "%{$search}%")
                  ->orWhere('nama_pengirim', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%");
            });
        }

        $suratJalanBongkarans = $query->orderBy('created_at', 'desc')->paginate(25);

        // Data untuk filter dropdown
        $kapals = Bl::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->distinct()
                    ->orderBy('nama_kapal')
                    ->get()
                    ->map(function($bl, $index) {
                        return (object)[
                            'id' => $index + 1,
                            'nama_kapal' => $bl->nama_kapal
                        ];
                    });
        $orders = Order::orderBy('nomor_order')->get();

        return view('surat-jalan-bongkaran.index', compact('suratJalanBongkarans', 'kapals', 'orders'));
    }

    /**
     * Show the form for selecting kapal and voyage before creating.
     */
    public function selectKapal(Request $request)
    {
        // Get unique kapal names from BLs table
        $kapals = Bl::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->distinct()
                    ->orderBy('nama_kapal')
                    ->get()
                    ->map(function($bl, $index) {
                        return (object)[
                            'id' => $index + 1, // Use incremental ID since we don't have master_kapal id
                            'nama_kapal' => $bl->nama_kapal
                        ];
                    });
        
        // Get BL data based on selected kapal
        $bls = collect();
        if ($request->filled('kapal_id')) {
            // Find kapal name by the incremental ID
            $selectedKapalName = $kapals->where('id', $request->kapal_id)->first()?->nama_kapal;
            
            if ($selectedKapalName) {
                $bls = Bl::where('nama_kapal', $selectedKapalName)
                        ->distinct()
                        ->get(['no_voyage', 'nomor_bl'])
                        ->groupBy('no_voyage');
            }
        }
        
        return view('surat-jalan-bongkaran.select-kapal', compact('kapals', 'bls'));
    }

    /**
     * Get BL data based on kapal selection (AJAX endpoint)
     */
    public function getBlData(Request $request)
    {
        if (!$request->filled('kapal_id')) {
            return response()->json(['voyages' => [], 'bls' => []]);
        }

        // Get kapal name from BLs table using incremental ID
        $kapals = Bl::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->distinct()
                    ->orderBy('nama_kapal')
                    ->get()
                    ->map(function($bl, $index) {
                        return (object)[
                            'id' => $index + 1,
                            'nama_kapal' => $bl->nama_kapal
                        ];
                    });
        
        $selectedKapal = $kapals->where('id', $request->kapal_id)->first();
        if (!$selectedKapal) {
            return response()->json(['voyages' => [], 'bls' => []]);
        }

        // Get BL data for this kapal with container information
        $bls = Bl::where('nama_kapal', $selectedKapal->nama_kapal)
              ->whereNotNull('no_voyage')
              ->whereNotNull('nomor_kontainer')
              ->get(['no_voyage', 'nomor_bl', 'nomor_kontainer', 'tipe_kontainer', 'size_kontainer', 'no_seal']);

        // Group by voyage and get unique voyages
        $voyages = $bls->pluck('no_voyage')->unique()->values();
        
        // Get container data grouped by voyage
        $blsByVoyage = $bls->groupBy('no_voyage')->map(function($items) {
            return $items->map(function($item) {
                // Format: "nomor_bl - nomor_kontainer (tipe_kontainer)"
                $display = '';
                if ($item->nomor_bl) {
                    $display = $item->nomor_bl . ' - ';
                }
                $display .= $item->nomor_kontainer;
                if ($item->size_kontainer) {
                    $display .= ' (' . strtoupper($item->size_kontainer) . ')';
                } elseif ($item->tipe_kontainer) {
                    $display .= ' (' . strtoupper($item->tipe_kontainer) . ')';
                }
                return [
                    'value' => $item->nomor_kontainer,
                    'text' => $display,
                    'nomor_bl' => $item->nomor_bl,
                    'nomor_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer ?: $item->tipe_kontainer
                ];
            })->values();
        });

        return response()->json([
            'voyages' => $voyages,
            'bls' => $blsByVoyage
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validate that kapal and voyage are provided
        $request->validate([
            'kapal_id' => 'required|integer',
            'no_voyage' => 'required|string',
        ]);

        // Get unique kapal names from BLs table
        $kapals = Bl::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->distinct()
                    ->orderBy('nama_kapal')
                    ->get()
                    ->map(function($bl, $index) {
                        return (object)[
                            'id' => $index + 1,
                            'nama_kapal' => $bl->nama_kapal
                        ];
                    });
        
        $users = User::orderBy('username')->get();
        
        // Get karyawan dengan divisi supir untuk dropdown supir
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')
                                                ->whereNull('tanggal_berhenti')
                                                ->orderBy('nama_panggilan')
                                                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);
        
        // Get tujuan kegiatan utama untuk dropdown tujuan pengambilan
        $tujuanKegiatanUtamas = \App\Models\TujuanKegiatanUtama::select('ke')
                                                               ->distinct()
                                                               ->whereNotNull('ke')
                                                               ->orderBy('ke')
                                                               ->get();
        
        // Get master kegiatans dengan type kegiatan surat jalan untuk dropdown aktifitas
        $masterKegiatans = MasterKegiatan::where('type', 'kegiatan surat jalan')
                                         ->where('status', 'aktif')
                                         ->orderBy('nama_kegiatan')
                                         ->get();
        
        // Find selected kapal by ID
        $selectedKapal = $kapals->where('id', $request->kapal_id)->first();
        $noVoyage = $request->no_voyage;
        $selectedContainer = null;

        // If no_bl (container) is selected, get the container details
        if ($request->filled('no_bl') && $selectedKapal) {
            $selectedContainer = Bl::where('nama_kapal', $selectedKapal->nama_kapal)
                                   ->where('no_voyage', $noVoyage)
                                   ->where('nomor_kontainer', $request->no_bl)
                                   ->first(['nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'penerima', 'alamat_pengiriman']);
                                   
            // Debug: log the container data
            if ($selectedContainer) {
                \Log::info('Selected Container Data:', [
                    'nomor_kontainer' => $selectedContainer->nomor_kontainer,
                    'no_seal' => $selectedContainer->no_seal,
                    'tipe_kontainer' => $selectedContainer->tipe_kontainer,
                    'size_kontainer' => $selectedContainer->size_kontainer,
                    'pengirim' => $selectedContainer->pengirim,
                    'penerima' => $selectedContainer->penerima,
                    'alamat_pengiriman' => $selectedContainer->alamat_pengiriman,
                ]);
            }
        }
        
        // Also check for container details passed via URL parameters
        if (!$selectedContainer && ($request->filled('container_seal') || $request->filled('container_size'))) {
            $selectedContainer = (object) [
                'nomor_kontainer' => $request->no_bl ?? '',
                'no_seal' => $request->container_seal ?? '',
                'size_kontainer' => $request->container_size ?? '',
                'tipe_kontainer' => $request->container_size ?? '',
                'pengirim' => $request->pengirim ?? '',
                'penerima' => $request->pengirim ?? '',
                'alamat_pengiriman' => $request->alamat_pengiriman ?? ''
            ];
        }

        return view('surat-jalan-bongkaran.create', compact('kapals', 'users', 'selectedKapal', 'noVoyage', 'selectedContainer', 'karyawanSupirs', 'tujuanKegiatanUtamas', 'masterKegiatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkarans',
            'tanggal_surat_jalan' => 'required|date',
            'term' => 'nullable|string|max:255',
            'aktifitas' => 'nullable|string',
            'pengirim' => 'nullable|string|max:255',
            'tujuan_alamat' => 'nullable|string|max:255',
            'tujuan_pengambilan' => 'nullable|string|max:255',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'jenis_pengiriman' => 'nullable|string|max:100',
            'tanggal_ambil_barang' => 'nullable|date',
            'supir' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:50',
            'kenek' => 'nullable|string|max:255',
            'krani' => 'nullable|string|max:255',
            'no_kontainer' => 'nullable|string|max:100',
            'no_seal' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'karton' => 'nullable|string|in:ya,tidak',
            'plastik' => 'nullable|string|in:ya,tidak',
            'terpal' => 'nullable|string|in:ya,tidak',
            'rit' => 'nullable|string|in:menggunakan_rit,tidak_menggunakan_rit',
            'uang_jalan_type' => 'nullable|string|in:full,setengah',
            'tagihan_ayp' => 'nullable|boolean',
            'tagihan_atb' => 'nullable|boolean',
            'tagihan_pb' => 'nullable|boolean',
            'nama_supir' => 'nullable|string|max:255',
            'nomor_sim' => 'nullable|string|max:50',
            'telepon_supir' => 'nullable|string|max:50',
            'nama_kenek' => 'nullable|string|max:255',
            'telepon_kenek' => 'nullable|string|max:50',
            'nomor_polisi_kendaraan' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'nomor_container' => 'nullable|string|max:100',
            'ukuran_container' => 'nullable|string|max:50',
            'jenis_container' => 'nullable|string|max:100',
            'nomor_seal' => 'nullable|string|max:100',
            'kondisi_container' => 'nullable|string|max:100',
            'biaya_bongkar' => 'nullable|numeric',
            'biaya_tambahan' => 'nullable|numeric',
            'total_biaya' => 'nullable|numeric',
            'metode_pembayaran' => 'nullable|string|max:100',
            'status_pembayaran' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
            'dokumentasi' => 'nullable|string',
        ]);

        $validatedData['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            $suratJalanBongkaran = SuratJalanBongkaran::create($validatedData);

            DB::commit();

            return redirect()->route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                           ->with('success', 'Surat Jalan Bongkaran berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['order', 'kapal', 'user']);
        
        return view('surat-jalan-bongkaran.show', compact('suratJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $users = User::orderBy('username')->get();

        return view('surat-jalan-bongkaran.edit', compact('suratJalanBongkaran', 'kapals', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratJalanBongkaran $suratJalanBongkaran)
    {
        $validatedData = $request->validate([
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkarans,nomor_surat_jalan,' . $suratJalanBongkaran->id,
            'tanggal_surat_jalan' => 'required|date',
            'term' => 'nullable|string|max:255',
            'aktifitas' => 'nullable|string',
            'pengirim' => 'nullable|string|max:255',
            'tujuan_alamat' => 'nullable|string|max:255',
            'tujuan_pengambilan' => 'nullable|string|max:255',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'jenis_pengiriman' => 'nullable|string|max:100',
            'tanggal_ambil_barang' => 'nullable|date',
            'supir' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:50',
            'kenek' => 'nullable|string|max:255',
            'krani' => 'nullable|string|max:255',
            'no_kontainer' => 'nullable|string|max:100',
            'no_seal' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'karton' => 'nullable|string|in:ya,tidak',
            'plastik' => 'nullable|string|in:ya,tidak',
            'terpal' => 'nullable|string|in:ya,tidak',
            'rit' => 'nullable|string|in:menggunakan_rit,tidak_menggunakan_rit',
            'uang_jalan_type' => 'nullable|string|in:full,setengah',
            'tagihan_ayp' => 'nullable|boolean',
            'tagihan_atb' => 'nullable|boolean',
            'tagihan_pb' => 'nullable|boolean',
            'nama_supir' => 'nullable|string|max:255',
            'nomor_sim' => 'nullable|string|max:50',
            'telepon_supir' => 'nullable|string|max:50',
            'nama_kenek' => 'nullable|string|max:255',
            'telepon_kenek' => 'nullable|string|max:50',
            'nomor_polisi_kendaraan' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'nomor_container' => 'nullable|string|max:100',
            'ukuran_container' => 'nullable|string|max:50',
            'jenis_container' => 'nullable|string|max:100',
            'nomor_seal' => 'nullable|string|max:100',
            'kondisi_container' => 'nullable|string|max:100',
            'biaya_bongkar' => 'nullable|numeric',
            'biaya_tambahan' => 'nullable|numeric',
            'total_biaya' => 'nullable|numeric',
            'metode_pembayaran' => 'nullable|string|max:100',
            'status_pembayaran' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
            'dokumentasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $suratJalanBongkaran->update($validatedData);

            DB::commit();

            return redirect()->route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                           ->with('success', 'Surat Jalan Bongkaran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratJalanBongkaran $suratJalanBongkaran)
    {
        try {
            $suratJalanBongkaran->delete();
            
            return redirect()->route('surat-jalan-bongkaran.index')
                           ->with('success', 'Surat Jalan Bongkaran berhasil dihapus.');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

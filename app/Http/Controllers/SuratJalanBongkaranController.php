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
use Illuminate\Support\Facades\Schema;

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
     * Show the form for selecting kapal and voyage.
     */
    public function selectShip(Request $request)
    {
        // Get unique kapal names from BL table
        $kapals = Bl::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->distinct()
                    ->orderBy('nama_kapal')
                    ->get()
                    ->pluck('nama_kapal');

        // Get voyages for selected kapal
        $voyages = collect();
        if ($request->filled('nama_kapal')) {
            $voyages = Bl::where('nama_kapal', $request->nama_kapal)
                        ->select('no_voyage')
                        ->whereNotNull('no_voyage')
                        ->distinct()
                        ->orderBy('no_voyage')
                        ->get()
                        ->pluck('no_voyage');
        }

        return view('surat-jalan-bongkaran.select-ship', compact('kapals', 'voyages'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Redirect to select ship if no kapal and voyage selected
        if (!$request->filled('nama_kapal') || !$request->filled('no_voyage')) {
            return redirect()->route('surat-jalan-bongkaran.select-ship');
        }

        $query = Bl::query();

        // Filter berdasarkan kapal (required)
        $query->where('nama_kapal', $request->nama_kapal);

        // Filter berdasarkan voyage (required)
        $query->where('no_voyage', $request->no_voyage);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        $bls = $query->orderBy('created_at', 'desc')->paginate(25);

        $selectedKapal = $request->nama_kapal;
        $selectedVoyage = $request->no_voyage;

        // Get data for modal form
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')
                                                ->whereNull('tanggal_berhenti')
                                                ->orderBy('nama_panggilan')
                                                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);
        
        $karyawanKranis = \App\Models\Karyawan::where('divisi', 'krani')
                                              ->whereNull('tanggal_berhenti')
                                              ->orderBy('nama_panggilan')
                                              ->get(['id', 'nama_lengkap', 'nama_panggilan']);
        
        $tujuanKegiatanUtamas = \App\Models\TujuanKegiatanUtama::whereNotNull('ke')
                                                               ->orderBy('ke')
                                                               ->get();
        
        $masterKegiatans = MasterKegiatan::where('type', 'kegiatan surat jalan')
                                         ->where('status', 'aktif')
                                         ->orderBy('nama_kegiatan')
                                         ->get();

        $terms = \App\Models\Term::orderBy('kode')->get();

        return view('surat-jalan-bongkaran.index', compact('bls', 'selectedKapal', 'selectedVoyage', 'karyawanSupirs', 'karyawanKranis', 'tujuanKegiatanUtamas', 'masterKegiatans', 'terms'));
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
                            'id' => $index + 1, // Use incremental ID
                            'nama_kapal' => $bl->nama_kapal
                        ];
                    });
        
        // Get BL data based on selected kapal
        $bls = collect();
        if ($request->filled('nama_kapal')) {
            $bls = Bl::where('nama_kapal', $request->nama_kapal)
                    ->distinct()
                    ->get(['no_voyage', 'nomor_bl'])
                    ->groupBy('no_voyage');
        }
        
        return view('surat-jalan-bongkaran.select-kapal', compact('kapals', 'bls'));
    }

    /**
     * Get BL data based on kapal selection (AJAX endpoint)
     */
    public function getBlData(Request $request)
    {
        if (!$request->filled('nama_kapal')) {
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
        
        // Find selected kapal by nama_kapal
        $selectedKapalName = $request->nama_kapal;
        if (!$selectedKapalName) {
            return response()->json(['voyages' => [], 'bls' => []]);
        }

        // Get BL data for this kapal with container information
        $bls = Bl::where('nama_kapal', $selectedKapalName)
              ->whereNotNull('no_voyage')
              ->whereNotNull('nomor_kontainer')
              ->get(['no_voyage', 'nomor_bl', 'nomor_kontainer', 'tipe_kontainer', 'size_kontainer', 'no_seal', 'nama_barang']);

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
                    'id' => $item->id,
                    'value' => $item->nomor_kontainer,
                    'text' => $display,
                    'nomor_bl' => $item->nomor_bl,
                    'nomor_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer ?: $item->tipe_kontainer,
                    'nama_barang' => $item->nama_barang
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
        // Debug: log all request parameters
        \Log::info('SJB Create Request Parameters:', $request->all());
        
        // Validate that kapal and voyage are provided
        $request->validate([
            'nama_kapal' => 'required|string',
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
        
        // Get karyawan dengan divisi supir untuk dropdown supir
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')
                                                ->whereNull('tanggal_berhenti')
                                                ->orderBy('nama_panggilan')
                                                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);
        
        // Get karyawan dengan divisi krani untuk dropdown kenek
        $karyawanKranis = \App\Models\Karyawan::where('divisi', 'krani')
                                              ->whereNull('tanggal_berhenti')
                                              ->orderBy('nama_panggilan')
                                              ->get(['id', 'nama_lengkap', 'nama_panggilan']);
        
        // Get tujuan kegiatan utama untuk dropdown tujuan pengambilan
        $tujuanKegiatanUtamas = \App\Models\TujuanKegiatanUtama::whereNotNull('ke')
                                                               ->orderBy('ke')
                                                               ->get();
        
        // Get master kegiatans dengan type kegiatan surat jalan untuk dropdown aktifitas
        $masterKegiatans = MasterKegiatan::where('type', 'kegiatan surat jalan')
                                         ->where('status', 'aktif')
                                         ->orderBy('nama_kegiatan')
                                         ->get();

        // Get terms untuk dropdown term pembayaran  
        $terms = \App\Models\Term::orderBy('kode')->get();
        
        // Get selected kapal name and voyage from request
        $selectedKapalName = $request->nama_kapal;
        $noVoyage = $request->no_voyage;
        $selectedContainer = null;
        $selectedBl = null;

        // Find kapal object for backward compatibility with view
        $selectedKapal = null;
        $kapalId = null;
        if ($selectedKapalName) {
            $selectedKapal = (object)['nama_kapal' => $selectedKapalName];
            // Find kapal_id from master_kapals table
            $masterKapal = MasterKapal::where('nama_kapal', $selectedKapalName)->first();
            if ($masterKapal) {
                $kapalId = $masterKapal->id;
            }
        }

        // If bl_id is passed, use it directly to fetch selectedBl and container data
        if ($request->filled('bl_id') && $selectedKapalName) {
            $selectedBl = Bl::where('nama_kapal', $selectedKapalName)
                              ->where('no_voyage', $noVoyage)
                              ->where('id', $request->bl_id)
                              ->first(['id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'penerima', 'alamat_pengiriman', 'pelabuhan_tujuan', 'nama_barang']);
            if ($selectedBl) {
                $selectedContainer = (object) [
                    'id' => $selectedBl->id,
                    'nomor_kontainer' => $selectedBl->nomor_kontainer ?? null,
                    'no_seal' => $selectedBl->no_seal ?? null,
                    'tipe_kontainer' => $selectedBl->tipe_kontainer ?? null,
                    'size_kontainer' => $selectedBl->size_kontainer ?? null,
                    'pengirim' => $selectedBl->pengirim ?? null,
                    'penerima' => $selectedBl->penerima ?? null,
                    'alamat_pengiriman' => $selectedBl->alamat_pengiriman ?? null,
                    'pelabuhan_tujuan' => $selectedBl->pelabuhan_tujuan ?? null,
                    'nama_barang' => $selectedBl->nama_barang ?? null,
                    'nomor_bl' => $selectedBl->nomor_bl ?? null,
                ];
            }
        }

        // If no_bl is passed, it might be either a container number (nomor_kontainer) or the BL number (nomor_bl) itself.
        if ($request->filled('no_bl') && $selectedKapalName) {
            // If the form includes both no_bl (BL) and no_kontainer (container) in query params, prefer the container
            // number for locating the container row; otherwise, use the provided no_bl value.
            $rawNoBlInput = $request->filled('no_kontainer') ? $request->no_kontainer : $request->no_bl;

            // 1) Try to find by container number first
            $selectedContainer = Bl::where('nama_kapal', $selectedKapalName)
                                   ->where('no_voyage', $noVoyage)
                                   ->where('nomor_kontainer', $rawNoBlInput)
                                   ->first(['id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'penerima', 'alamat_pengiriman', 'pelabuhan_tujuan', 'nama_barang']);

            if ($selectedContainer) {
                \Log::info('Selected Container Data:', [
                    'nomor_kontainer' => $selectedContainer->nomor_kontainer,
                    'no_seal' => $selectedContainer->no_seal,
                    'tipe_kontainer' => $selectedContainer->tipe_kontainer,
                    'size_kontainer' => $selectedContainer->size_kontainer,
                    'pengirim' => $selectedContainer->pengirim,
                    'penerima' => $selectedContainer->penerima,
                    'alamat_pengiriman' => $selectedContainer->alamat_pengiriman,
                    'pelabuhan_tujuan' => $selectedContainer->pelabuhan_tujuan,
                    'nama_barang' => $selectedContainer->nama_barang,
                ]);

                // Fill selectedBl from the container row
                $selectedBl = Bl::where('nama_kapal', $selectedKapalName)
                                 ->where('no_voyage', $noVoyage)
                                 ->where('nomor_kontainer', $rawNoBlInput)
                                 ->first(['id', 'nomor_bl']);
            } else {
                // 2) Fallback: try to find by BL number (nomor_bl)
                $selectedBl = Bl::where('nama_kapal', $selectedKapalName)
                                 ->where('no_voyage', $noVoyage)
                                 ->where('nomor_bl', $rawNoBlInput)
                                 ->first(['id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'penerima', 'alamat_pengiriman', 'pelabuhan_tujuan', 'nama_barang']);

                if ($selectedBl) {
                    // Create a container-like object to keep the rest of the view working
                    $selectedContainer = (object) [
                        'id' => $selectedBl->id,
                        'nomor_kontainer' => $selectedBl->nomor_kontainer ?? null,
                        'no_seal' => $selectedBl->no_seal ?? null,
                        'tipe_kontainer' => $selectedBl->tipe_kontainer ?? null,
                        'size_kontainer' => $selectedBl->size_kontainer ?? null,
                        'pengirim' => $selectedBl->pengirim ?? null,
                        'penerima' => $selectedBl->penerima ?? null,
                        'alamat_pengiriman' => $selectedBl->alamat_pengiriman ?? null,
                        'pelabuhan_tujuan' => $selectedBl->pelabuhan_tujuan ?? null,
                        'nama_barang' => $selectedBl->nama_barang ?? null,
                        'nomor_bl' => $selectedBl->nomor_bl ?? null,
                    ];

                    \Log::info('Selected BL Data (no_bl input used as nomor_bl):', [
                        'id' => $selectedBl->id,
                        'nomor_bl' => $selectedBl->nomor_bl,
                        'nomor_kontainer' => $selectedBl->nomor_kontainer ?? null,
                    ]);
                }
            }
        }
        
        // Also check for container details passed via URL parameters
        if (!$selectedContainer && ($request->filled('container_seal') || $request->filled('container_size') || $request->filled('no_bl'))) {
            $selectedContainer = (object) [
                'nomor_kontainer' => $request->no_bl ?? '',
                'no_seal' => $request->container_seal ?? '',
                'size_kontainer' => $request->container_size ?? '',
                'tipe_kontainer' => $request->container_size ?? '',
                'pengirim' => $request->pengirim ?? '',
                'penerima' => $request->pengirim ?? '',
                'alamat_pengiriman' => $request->alamat_pengiriman ?? '',
                'nama_barang' => $request->jenis_barang ?? ''
            ];
            // If we only have URL params and a no_bl in the params, attempt to load BL record as selectedBl
            if ($request->filled('no_bl') && $selectedKapalName) {
                $selectedBl = Bl::where('nama_kapal', $selectedKapalName)
                                 ->where('no_voyage', $noVoyage)
                                 ->where('nomor_kontainer', $request->no_bl)
                                 ->first(['id', 'nomor_bl']);
            }
        }

        return view('surat-jalan-bongkaran.create', compact(
            'kapals', 'selectedKapal', 'noVoyage', 'selectedContainer', 'selectedBl', 'karyawanSupirs', 'karyawanKranis', 'tujuanKegiatanUtamas', 'masterKegiatans', 'terms', 'kapalId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: log all request data
        \Log::info('SJB Store Request Data:', $request->all());
        
        try {
            $validatedData = $request->validate([
            'kapal_id' => 'nullable|integer',
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'no_bl' => 'nullable|string|max:255',
            'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkarans',
            'tanggal_surat_jalan' => 'required|date',
            'term' => 'nullable|string|max:255',
            'aktifitas' => 'nullable|string',
            'pengirim' => 'nullable|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
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
            'bl_id' => 'nullable|integer|exists:bls,id',
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
            'uang_jalan_nominal' => 'nullable|numeric|min:0',
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if request is AJAX (from modal)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Silakan periksa kembali data yang diinput.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // If bl_id is present, ensure we store the actual BL number (nomor_bl) instead of container number
        if ($request->filled('bl_id')) {
            $bl = Bl::find($request->bl_id);
            if ($bl && isset($bl->nomor_bl)) {
                $validatedData['no_bl'] = $bl->nomor_bl;
                if (Schema::hasColumn('surat_jalan_bongkarans', 'bl_id')) {
                    $validatedData['bl_id'] = $request->bl_id;
                }
            }
        } elseif ($request->filled('no_kontainer') && (!isset($validatedData['no_bl']) || empty($validatedData['no_bl']))) {
            // If only container number is provided and no_bl missing, try to look up BL number by container
            $blByContainer = Bl::where('nomor_kontainer', $request->no_kontainer)->first(['nomor_bl']);
            if ($blByContainer && isset($blByContainer->nomor_bl)) {
                $validatedData['no_bl'] = $blByContainer->nomor_bl;
                // If found by container, try to set bl_id as well
                $blRecord = Bl::where('nomor_kontainer', $request->no_kontainer)->first(['id']);
                if ($blRecord) {
                    if (Schema::hasColumn('surat_jalan_bongkarans', 'bl_id')) {
                        $validatedData['bl_id'] = $blRecord->id;
                    }
                }
            }
        }
        $validatedData['input_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $suratJalanBongkaran = SuratJalanBongkaran::create($validatedData);

            DB::commit();

            // Check if request is AJAX (from modal)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Jalan Bongkaran berhasil dibuat.',
                    'redirect' => route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                ]);
            }

            return redirect()->route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                           ->with('success', 'Surat Jalan Bongkaran berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Check if request is AJAX (from modal)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['inputBy', 'bl']);
        
        return view('surat-jalan-bongkaran.show', compact('suratJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        
        // Get terms untuk dropdown term pembayaran  
        $terms = \App\Models\Term::orderBy('kode')->get();

        return view('surat-jalan-bongkaran.edit', compact('suratJalanBongkaran', 'kapals', 'terms'));
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
            'jenis_barang' => 'nullable|string|max:255',
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
            'bl_id' => 'nullable|integer|exists:bls,id',
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

        // If bl_id is present, ensure we store the actual BL number (nomor_bl) and bl_id
        if ($request->filled('bl_id')) {
            $bl = Bl::find($request->bl_id);
            if ($bl && isset($bl->nomor_bl)) {
                $validatedData['no_bl'] = $bl->nomor_bl;
                if (Schema::hasColumn('surat_jalan_bongkarans', 'bl_id')) {
                    $validatedData['bl_id'] = $bl->id;
                }
            }
        } elseif ($request->filled('no_kontainer') && (!isset($validatedData['no_bl']) || empty($validatedData['no_bl']))) {
            $blByContainer = Bl::where('nomor_kontainer', $request->no_kontainer)->first(['id', 'nomor_bl']);
            if ($blByContainer && isset($blByContainer->nomor_bl)) {
                $validatedData['no_bl'] = $blByContainer->nomor_bl;
                if (Schema::hasColumn('surat_jalan_bongkarans', 'bl_id')) {
                    $validatedData['bl_id'] = $blByContainer->id;
                }
            }
        }

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

    /**
     * Print a surat jalan bongkaran (render a printable view)
     */
    public function print(SuratJalanBongkaran $suratJalanBongkaran)
    {
        // Eager load relations useful for the print view
        $suratJalanBongkaran->load(['inputBy', 'bl']);

        return view('surat-jalan-bongkaran.print', compact('suratJalanBongkaran'));
    }

    /**
     * Get BL data by ID (API endpoint for modal)
     */
    public function getBlById($id)
    {
        $bl = Bl::find($id);
        
        if (!$bl) {
            return response()->json(['error' => 'BL not found'], 404);
        }

        return response()->json([
            'id' => $bl->id,
            'nomor_bl' => $bl->nomor_bl,
            'nama_kapal' => $bl->nama_kapal,
            'no_voyage' => $bl->no_voyage,
            'nomor_kontainer' => $bl->nomor_kontainer,
            'no_seal' => $bl->no_seal,
            'tipe_kontainer' => $bl->tipe_kontainer,
            'size_kontainer' => $bl->size_kontainer,
            'nama_barang' => $bl->nama_barang,
            'tonnage' => $bl->tonnage,
            'volume' => $bl->volume,
            'status_bongkar' => $bl->status_bongkar,
            'pengirim' => $bl->pengirim ?? '',
            'penerima' => $bl->penerima ?? '',
            'alamat_pengiriman' => $bl->alamat_pengiriman ?? '',
            'pelabuhan_tujuan' => $bl->pelabuhan_tujuan ?? '',
            'jenis_pengiriman' => $bl->jenis_pengiriman ?? '',
        ]);
    }
}

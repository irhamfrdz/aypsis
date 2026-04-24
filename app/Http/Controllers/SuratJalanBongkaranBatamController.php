<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaranBatam;
use App\Models\Manifest;
use App\Models\MasterKapal;
use App\Models\User;
use App\Models\TujuanKegiatanUtama;
use App\Models\MasterKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\SuratJalanBongkaranTableExport;
use Maatwebsite\Excel\Facades\Excel;

class SuratJalanBongkaranBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:surat-jalan-bongkaran-batam-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:surat-jalan-bongkaran-batam-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:surat-jalan-bongkaran-batam-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:surat-jalan-bongkaran-batam-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the form for selecting kapal and voyage.
     */
    public function selectShip(Request $request)
    {
        // Get unique kapal names from Manifest table with normalization
        $kapals = Manifest::select('nama_kapal')
                    ->whereNotNull('nama_kapal')
                    ->where('nama_kapal', '!=', '')
                    ->get()
                    ->map(function($item) {
                        // Normalize: remove dots after KM/KMP, trim spaces, uppercase
                        return trim(str_replace(['KM.', 'KMP.'], ['KM', 'KMP'], strtoupper($item->nama_kapal)));
                    })
                    ->unique()
                    ->sort()
                    ->values();

        // Get voyages for selected kapal
        $voyages = collect();
        if ($request->filled('nama_kapal')) {
            $voyages = Manifest::where('nama_kapal', 'LIKE', '%' . str_replace(['KM ', 'KMP '], ['%', '%'], $request->nama_kapal) . '%')
                        ->select('no_voyage')
                        ->whereNotNull('no_voyage')
                        ->distinct()
                        ->orderBy('no_voyage')
                        ->get()
                        ->pluck('no_voyage');
        }

        return view('surat-jalan-bongkaran-batam.select-ship', compact('kapals', 'voyages'));
    }

    /**
     * Get voyages for a specific kapal via AJAX.
     */
    public function getVoyages(Request $request)
    {
        $nama_kapal = $request->query('nama_kapal');

        if (!$nama_kapal) {
            return response()->json(['success' => false, 'message' => 'Nama kapal is required'], 400);
        }

        // Normalize the search term for flexible matching
        $kapalClean = strtolower(str_replace('.', '', $nama_kapal));

        $voyages = Manifest::select('no_voyage')
                    ->where(function($q) use ($nama_kapal, $kapalClean) {
                        $q->where('nama_kapal', $nama_kapal)
                          ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                    })
                    ->whereNotNull('no_voyage')
                    ->distinct()
                    ->orderBy('no_voyage', 'desc')
                    ->pluck('no_voyage');

        return response()->json([
            'success' => true,
            'voyages' => $voyages
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get selected kapal and voyage from request for filter functionality
        $selectedKapal = $request->nama_kapal;
        $selectedVoyage = $request->no_voyage;

        // Check mode: 'surat_jalan' or 'bl' (default)
        $mode = $request->get('mode', 'bl');

        if ($mode === 'surat_jalan') {
            // Show Surat Jalan Bongkaran data
            $query = SuratJalanBongkaranBatam::query()
                ->where('lokasi', 'batam');

            // Filter by selected kapal and voyage if provided
            if ($selectedKapal) {
                $kapalClean = strtolower(str_replace('.', '', $selectedKapal));
                $query->where(function($q) use ($selectedKapal, $kapalClean) {
                    $q->where('nama_kapal', $selectedKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                });
            }
            if ($selectedVoyage) {
                $query->where('no_voyage', $selectedVoyage);
            }

            // Filter by types (FCL, LCL, Cargo)
            if ($request->filled('types')) {
                $types = (array) $request->types;
                $query->where(function($q) use ($types) {
                    $q->whereIn('jenis_pengiriman', $types)
                      ->orWhereIn('tipe_kontainer', $types);
                });
            }

            // Search in surat jalan bongkaran (ignore punctuation)
            if ($request->filled('search')) {
                $search = $request->search;
                // Remove all punctuation from search term
                $searchClean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $search);
                
                $query->where(function($q) use ($search, $searchClean) {
                    // Normal search (with punctuation)
                    $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('term', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%")
                      ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                      ->orWhere('jenis_pengiriman', 'like', "%{$search}%")
                      // Search without punctuation
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(nomor_surat_jalan, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(no_kontainer, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(no_seal, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(no_plat, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"]);
                });
            }

            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(25);
            $manifests = new LengthAwarePaginator([], 0, 25); // Empty paginated collection for Manifest mode
        } else {
            // Show Manifest data - default mode
            $query = Manifest::query();
            // Join with terms table to fetch human friendly term name
            $query->leftJoin('terms as t', 'manifests.term', '=', 't.kode')
                ->select('manifests.*', 't.nama_status as term_nama')
                ->with('suratJalanBongkaranBatam');

            // Filter by selected kapal and voyage if provided
            if ($selectedKapal) {
                $kapalClean = strtolower(str_replace('.', '', $selectedKapal));
                $query->where(function($q) use ($selectedKapal, $kapalClean) {
                    $q->where('manifests.nama_kapal', $selectedKapal)
                      ->orWhereRaw("LOWER(REPLACE(manifests.nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                });
            }
            if ($selectedVoyage) {
                $query->where('manifests.no_voyage', $selectedVoyage);
            }

            // Filter by types (FCL, LCL, Cargo)
            if ($request->filled('types')) {
                $types = (array) $request->types;
                $query->whereIn('manifests.tipe_kontainer', $types);
            }

            // Search in Manifest data (ignore punctuation)
            if ($request->filled('search')) {
                $search = $request->search;
                // Remove all punctuation from search term
                $searchClean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $search);
                
                $query->where(function($q) use ($search, $searchClean) {
                    // Normal search (with punctuation)
                    $q->where('nomor_bl', 'like', "%{$search}%")
                      ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('term', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%")
                      ->orWhere('penerima', 'like', "%{$search}%")
                      ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                      ->orWhereHas('suratJalanBongkaranBatam', function($sq) use ($search) {
                          $sq->where('nomor_surat_jalan', 'like', "%{$search}%");
                      })
                      // Search without punctuation
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(nomor_bl, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(nomor_kontainer, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(no_seal, '-', ''), '.', ''), ',', ''), '/', ''), ' ', ''), '(', ''), ')', '') LIKE ?", ["%{$searchClean}%"]);
                });
            }

            $manifests = $query->orderBy('manifests.created_at', 'desc')->paginate(25);
            $suratJalans = new LengthAwarePaginator([], 0, 25); // Empty paginated collection for Surat Jalan mode
        }

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

        $pricelistUangJalanBatams = \App\Models\PricelistUangJalanBatam::orderBy('expedisi')->orderBy('ring')->get();

        $masterKegiatans = MasterKegiatan::where('type', 'kegiatan surat jalan')
                                         ->where('status', 'aktif')
                                         ->orderBy('nama_kegiatan')
                                         ->get();

        $terms = \App\Models\Term::orderBy('kode')->get();

        return view('surat-jalan-bongkaran-batam.index', compact('suratJalans', 'manifests', 'karyawanSupirs', 'karyawanKranis', 'tujuanKegiatanUtamas', 'pricelistUangJalanBatams', 'masterKegiatans', 'terms', 'selectedKapal', 'selectedVoyage'));
    }

    /**
     * Export data to Excel.
     */
    public function export(Request $request)
    {
        $selectedKapal = $request->nama_kapal;
        $selectedVoyage = $request->no_voyage;
        $mode = $request->get('mode', 'bl');

        if ($mode === 'surat_jalan') {
            $query = SuratJalanBongkaranBatam::with('manifest')
                ->where('lokasi', 'batam');
            if ($selectedKapal) {
                $kapalClean = strtolower(str_replace('.', '', $selectedKapal));
                $query->where(function($q) use ($selectedKapal, $kapalClean) {
                    $q->where('nama_kapal', $selectedKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                });
            }
            if ($selectedVoyage) {
                $query->where('no_voyage', $selectedVoyage);
            }
            if ($request->filled('types')) {
                $types = (array) $request->types;
                $query->where(function($q) use ($types) {
                    $q->whereIn('jenis_pengiriman', $types)
                      ->orWhereIn('tipe_kontainer', $types);
                });
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $searchClean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $search);
                $query->where(function($q) use ($search, $searchClean) {
                    $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('term', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%");
                });
            }
            $data = $query->orderBy('created_at', 'desc')->get();
            $filename = 'Surat_Jalan_Bongkaran_Batam_' . str_replace(' ', '_', $selectedKapal) . '_' . str_replace('/', '-', $selectedVoyage) . '.xlsx';
        } else {
            $query = Manifest::query();
            $query->leftJoin('terms as t', 'manifests.term', '=', 't.kode')
                ->select('manifests.*', 't.nama_status as term_nama');
            if ($selectedKapal) {
                $kapalClean = strtolower(str_replace('.', '', $selectedKapal));
                $query->where(function($q) use ($selectedKapal, $kapalClean) {
                    $q->where('manifests.nama_kapal', $selectedKapal)
                      ->orWhereRaw("LOWER(REPLACE(manifests.nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                });
            }
            if ($selectedVoyage) {
                $query->where('manifests.no_voyage', $selectedVoyage);
            }
            if ($request->filled('types')) {
                $types = (array) $request->types;
                $query->whereIn('manifests.tipe_kontainer', $types);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_bl', 'like', "%{$search}%")
                      ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('term', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%")
                      ->orWhere('penerima', 'like', "%{$search}%");
                });
            }
            $data = $query->orderBy('manifests.created_at', 'desc')->get();
            $filename = 'Manifest_Bongkaran_Batam_' . str_replace(' ', '_', $selectedKapal) . '_' . str_replace('/', '-', $selectedVoyage) . '.xlsx';
        }

        return Excel::download(new SuratJalanBongkaranTableExport($data, $mode, $selectedKapal, $selectedVoyage), $filename);
    }

    /**
     * Show the form for selecting kapal and voyage before creating.
     */
    public function selectKapal(Request $request)
    {
        // Get unique kapal names from BLs table
        $kapals = Manifest::select('nama_kapal')
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
            $bls = Manifest::where('nama_kapal', $request->nama_kapal)
                // Exclude BLs with nama_barang that are placeholders
                ->whereRaw("LOWER(COALESCE(nama_barang, '')) NOT LIKE ?", ['%empty%'])
                ->whereRaw("LOWER(COALESCE(nama_barang, '')) NOT LIKE ?", ['%kosong%'])
                ->whereRaw("TRIM(COALESCE(nama_barang, '')) NOT IN ('-', '')")
                ->distinct()
                ->get(['no_voyage', 'nomor_bl'])
                ->groupBy('no_voyage');
        }
        
        return view('surat-jalan-bongkaran-batam.select-kapal', compact('kapals', 'bls'));
    }

    /**
     * Get BL data based on kapal selection (AJAX endpoint)
     */
    public function getBlData(Request $request)
    {
        if (!$request->filled('nama_kapal')) {
            return response()->json(['voyages' => [], 'bls' => []]);
        }

        // Find selected kapal by nama_kapal
        $selectedKapalName = $request->nama_kapal;
        if (!$selectedKapalName) {
            return response()->json(['voyages' => [], 'bls' => []]);
        }

        // Get BL data for this kapal with container information
          $bls = Manifest::where('nama_kapal', $selectedKapalName)
              ->whereNotNull('no_voyage')
              ->whereNotNull('nomor_kontainer')
              // Exclude BLs with nama_barang indicating empty values
              ->whereRaw("LOWER(COALESCE(nama_barang, '')) NOT LIKE ?", ['%empty%'])
              ->whereRaw("LOWER(COALESCE(nama_barang, '')) NOT LIKE ?", ['%kosong%'])
              ->whereRaw("TRIM(COALESCE(nama_barang, '')) NOT IN ('-', '')")
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
        // Validate that kapal and voyage are provided
        $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        // Get unique kapal names from BLs table
        $kapals = Manifest::select('nama_kapal')
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
            $selectedBl = Manifest::where('nama_kapal', $selectedKapalName)
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
            $rawNoBlInput = $request->filled('no_kontainer') ? $request->no_kontainer : $request->no_bl;

            // Try to find by container number first
            $selectedContainer = Manifest::where('nama_kapal', $selectedKapalName)
                                   ->where('no_voyage', $noVoyage)
                                   ->where('nomor_kontainer', $rawNoBlInput)
                                   ->first(['id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'penerima', 'alamat_pengiriman', 'pelabuhan_tujuan', 'nama_barang']);

            if ($selectedContainer) {
                $selectedBl = Manifest::where('nama_kapal', $selectedKapalName)
                                 ->where('no_voyage', $noVoyage)
                                 ->where('nomor_kontainer', $rawNoBlInput)
                                 ->first(['id', 'nomor_bl']);
            } else {
                // Fallback: try to find by BL number (nomor_bl)
                $selectedBl = Manifest::where('nama_kapal', $selectedKapalName)
                                 ->where('no_voyage', $noVoyage)
                                 ->where('nomor_bl', $rawNoBlInput)
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
        }
        
        return view('surat-jalan-bongkaran-batam.create', compact(
            'kapals', 'selectedKapal', 'noVoyage', 'selectedContainer', 'selectedBl', 'karyawanSupirs', 'karyawanKranis', 'tujuanKegiatanUtamas', 'masterKegiatans', 'terms', 'kapalId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kapal_id' => 'nullable|integer',
                'nama_kapal' => 'nullable|string|max:255',
                'no_voyage' => 'nullable|string|max:255',
                'no_bl' => 'nullable|string|max:255',
                'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkaran_batams',
                'tanggal_surat_jalan' => 'required|date',
                'lanjut_muat' => 'nullable|string|in:ya,tidak',
                'nomor_sj_sebelumnya' => 'required_if:lanjut_muat,ya|nullable|string|max:255',
                'term' => 'nullable|string|max:255',
                'aktifitas' => 'nullable|string',
                'pengirim' => 'nullable|string|max:255',
                'penerima' => 'nullable|string|max:255',
                'jenis_barang' => 'nullable|string|max:1000',
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
                'manifest_id' => 'nullable|integer|exists:manifests,id',
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
                'keterangan' => 'nullable|string',
                'uang_jalan_nominal' => 'nullable|numeric|min:0',
                'f_e' => 'nullable|string|in:Full,Empty',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $validatedData['input_by'] = Auth::id();
        $validatedData['lokasi'] = 'batam';

        try {
            DB::beginTransaction();

            $suratJalanBongkaran = SuratJalanBongkaranBatam::create($validatedData);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Jalan Bongkaran Batam berhasil dibuat.',
                    'redirect' => route('surat-jalan-bongkaran-batam.list')
                ]);
            }

            return redirect()->route('surat-jalan-bongkaran-batam.list')
                           ->with('success', 'Surat Jalan Bongkaran Batam berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
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
    public function show(SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['inputBy', 'bl']);
        
        return view('surat-jalan-bongkaran-batam.show', compact('suratJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $terms = \App\Models\Term::orderBy('kode')->get();
        $masterKegiatans = MasterKegiatan::where('type', 'kegiatan surat jalan')->where('status', 'aktif')->orderBy('nama_kegiatan')->get();
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::orderBy('ke')->get();
        
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')
                              ->whereNull('tanggal_berhenti')
                              ->orderBy('nama_panggilan')
                              ->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);
        
        $karyawanKranis = \App\Models\Karyawan::where('divisi', 'krani')
                              ->whereNull('tanggal_berhenti')
                              ->orderBy('nama_panggilan')
                              ->get(['id', 'nama_panggilan', 'nama_lengkap']);

        return view('surat-jalan-bongkaran-batam.edit', compact(
            'suratJalanBongkaran', 
            'kapals', 
            'terms', 
            'masterKegiatans', 
            'tujuanKegiatanUtamas', 
            'karyawanSupirs', 
            'karyawanKranis'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        try {
            $validatedData = $request->validate([
                'kapal_id' => 'nullable|integer',
                'nama_kapal' => 'nullable|string|max:255',
                'no_voyage' => 'nullable|string|max:255',
                'no_bl' => 'nullable|string|max:255',
                'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkaran_batams,nomor_surat_jalan,' . $suratJalanBongkaran->id,
                'tanggal_surat_jalan' => 'required|date',
                'lanjut_muat' => 'nullable|string|in:ya,tidak',
                'nomor_sj_sebelumnya' => 'required_if:lanjut_muat,ya|nullable|string|max:255',
                'term' => 'nullable|string|max:255',
                'aktifitas' => 'nullable|string',
                'pengirim' => 'nullable|string|max:255',
                'penerima' => 'nullable|string|max:255',
                'jenis_barang' => 'nullable|string|max:1000',
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
                'keterangan' => 'nullable|string',
                'uang_jalan_nominal' => 'nullable|numeric|min:0',
                'f_e' => 'nullable|string|in:Full,Empty',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $suratJalanBongkaran->update($validatedData);

            DB::commit();

            return redirect()->route('surat-jalan-bongkaran-batam.list')
                           ->with('success', 'Surat Jalan Bongkaran Batam berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        try {
            DB::beginTransaction();
            
            $suratJalanBongkaran->delete();
            
            DB::commit();
            
            return redirect()->route('surat-jalan-bongkaran-batam.list')
                           ->with('success', 'Surat Jalan Bongkaran Batam berhasil dihapus.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function print(SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['inputBy', 'bl']);

        $pageNumber = 1;
        $totalPages = 1;
        
        if ($suratJalanBongkaran->bl_id) {
            $suratJalansForSameBl = SuratJalanBongkaranBatam::where('bl_id', $suratJalanBongkaran->bl_id)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            
            $totalPages = $suratJalansForSameBl->count();
            
            foreach ($suratJalansForSameBl as $index => $sj) {
                if ($sj->id == $suratJalanBongkaran->id) {
                    $pageNumber = $index + 1;
                    break;
                }
            }
        }

        return view('surat-jalan-bongkaran-batam.print', compact('suratJalanBongkaran', 'pageNumber', 'totalPages'));
    }

    public function printFromBl($bl)
    {
        $manifest = Manifest::findOrFail($bl);
        
        $printData = new \stdClass();
        $printData->tanggal_berangkat = $manifest->tanggal_berangkat ?? now()->format('Y-m-d');
        $printData->tanggal_surat_jalan = now()->format('Y-m-d');
        
        $printData->no_voyage = $manifest->no_voyage ?? '';
        $printData->nama_kapal = $manifest->nama_kapal ?? '';
        $printData->no_plat = '';
        $printData->no_bl = $manifest->nomor_bl ?? '';
        $printData->no_kontainer = $manifest->nomor_kontainer ?? '';
        $printData->jenis_pengiriman = 'IMPORT';
        $printData->jenis_barang = $manifest->nama_barang ?? '';
        $printData->penerima = $manifest->penerima ?? '';
        $printData->tujuan_pengambilan = '';
        $printData->no_seal = $manifest->no_seal ?? '';
        $printData->pelabuhan_tujuan = $manifest->pelabuhan_tujuan ?? $manifest->pelabuhan_bongkar ?? '';
        $printData->tujuan_pengiriman = $manifest->pelabuhan_tujuan ?? $manifest->pelabuhan_bongkar ?? '';
        $printData->size_kontainer = $manifest->size_kontainer ?? '';
        $printData->tipe_kontainer = $manifest->tipe_kontainer ?? 'FCL';
        $printData->kuantitas = $manifest->kuantitas ?? '';
        $printData->satuan = $manifest->satuan ?? '';
        $printData->nomor_urut = $manifest->nomor_urut ?? '';
        
        $printData->bl = $manifest;
        $printData->kapal = null;

        return view('surat-jalan-bongkaran-batam.print-from-bl', compact('printData'));
    }

    public function printBa($bl)
    {
        $manifest = Manifest::findOrFail($bl);
        
        $baData = new \stdClass();
        $baData->id = $manifest->id;
        $baData->nomor_ba = 'BA/' . date('Y/m/d') . '/' . str_pad($manifest->id, 4, '0', STR_PAD_LEFT);
        $baData->tanggal_ba = $manifest->tanggal_berangkat ? $manifest->tanggal_berangkat->format('Y-m-d') : now()->format('Y-m-d');
        $baData->manifest = $manifest;
        
        return view('surat-jalan-bongkaran-batam.print-ba', compact('baData'));
    }

    public function downloadPdf(SuratJalanBongkaranBatam $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['inputBy', 'bl']);
        
        $pageNumber = 1;
        $totalPages = 1;
        
        if ($suratJalanBongkaran->bl_id) {
            $suratJalansForSameBl = SuratJalanBongkaranBatam::where('bl_id', $suratJalanBongkaran->bl_id)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            $totalPages = $suratJalansForSameBl->count();
            foreach ($suratJalansForSameBl as $index => $sj) {
                if ($sj->id == $suratJalanBongkaran->id) {
                    $pageNumber = $index + 1;
                    break;
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat-jalan-bongkaran-batam.print', compact('suratJalanBongkaran', 'pageNumber', 'totalPages'));
        $pdf->setPaper([0, 0, 609.4488, 396.8504], 'portrait');
        
        return $pdf->download('Surat_Jalan_Bongkaran_' . $suratJalanBongkaran->nomor_surat_jalan . '.pdf');
    }

    public function getSuratJalanById($id)
    {
        try {
            $suratJalan = SuratJalanBongkaranBatam::with('manifest')->find($id);
            if (!$suratJalan) {
                return response()->json(['error' => 'Surat Jalan not found'], 404);
            }

            return response()->json([
                'id' => $suratJalan->id,
                'bl_id' => $suratJalan->bl_id,
                'nama_kapal' => $suratJalan->nama_kapal ?? '',
                'no_voyage' => $suratJalan->no_voyage ?? '',
                'nomor_surat_jalan' => $suratJalan->nomor_surat_jalan,
                'tanggal_surat_jalan' => $suratJalan->tanggal_surat_jalan && is_object($suratJalan->tanggal_surat_jalan) ? $suratJalan->tanggal_surat_jalan->format('Y-m-d') : $suratJalan->tanggal_surat_jalan,
                'term' => $suratJalan->term ?? '',
                'aktifitas' => $suratJalan->aktifitas ?? '',
                'pengirim' => $suratJalan->pengirim ?? '',
                'penerima' => $suratJalan->penerima ?? '',
                'jenis_barang' => $suratJalan->jenis_barang ?? '',
                'nama_barang_manifest' => $suratJalan->manifest->nama_barang ?? '',
                'tujuan_alamat' => $suratJalan->tujuan_alamat ?? '',
                'tujuan_pengambilan' => $suratJalan->tujuan_pengambilan ?? '',
                'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman ?? '',
                'jenis_pengiriman' => $suratJalan->jenis_pengiriman ?? '',
                'tanggal_ambil_barang' => $suratJalan->tanggal_ambil_barang && is_object($suratJalan->tanggal_ambil_barang) ? $suratJalan->tanggal_ambil_barang->format('Y-m-d') : $suratJalan->tanggal_ambil_barang,
                'supir' => $suratJalan->supir ?? '',
                'no_plat' => $suratJalan->no_plat ?? '',
                'kenek' => $suratJalan->kenek ?? '',
                'krani' => $suratJalan->krani ?? '',
                'no_kontainer' => $suratJalan->no_kontainer ?? '',
                'no_seal' => $suratJalan->no_seal ?? '',
                'no_bl' => $suratJalan->no_bl ?? '',
                'size' => $suratJalan->size ?? '',
                'karton' => $suratJalan->karton ?? 'tidak',
                'plastik' => $suratJalan->plastik ?? 'tidak',
                'terpal' => $suratJalan->terpal ?? 'tidak',
                'rit' => $suratJalan->rit ?? 'menggunakan_rit',
                'uang_jalan_type' => $suratJalan->uang_jalan_type ?? 'full',
                'uang_jalan_nominal' => $suratJalan->uang_jalan_nominal ?? 0,
                'lokasi' => $suratJalan->lokasi ?? '',
                'f_e' => $suratJalan->f_e ?? 'Full',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch Surat Jalan data'], 500);
        }
    }

    public function getManifestById($id)
    {
        try {
            $manifest = Manifest::find($id);
            if (!$manifest) {
                return response()->json(['error' => 'Manifest not found'], 404);
            }

            return response()->json([
                'id' => $manifest->id,
                'nomor_bl' => $manifest->nomor_bl,
                'nomor_manifest' => $manifest->nomor_manifest,
                'nama_kapal' => $manifest->nama_kapal,
                'no_voyage' => $manifest->no_voyage,
                'nomor_kontainer' => $manifest->nomor_kontainer,
                'no_seal' => $manifest->no_seal,
                'tipe_kontainer' => $manifest->tipe_kontainer ?? '',
                'size_kontainer' => $manifest->size_kontainer,
                'nama_barang' => $manifest->nama_barang,
                'tonnage' => $manifest->tonnage ?? '',
                'volume' => $manifest->volume ?? '',
                'status_bongkar' => $manifest->status_bongkar ?? '',
                'term' => $manifest->term ?? '',
                'term_nama' => \App\Models\Term::where('kode', $manifest->term)->value('nama_status') ?? '',
                'pengirim' => $manifest->pengirim ?? '',
                'penerima' => $manifest->penerima ?? '',
                'alamat_pengiriman' => $manifest->alamat_pengiriman ?? '',
                'pelabuhan_tujuan' => $manifest->pelabuhan_tujuan ?? '',
                'jenis_pengiriman' => $manifest->jenis_pengiriman ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch Manifest data'], 500);
        }
    }
}

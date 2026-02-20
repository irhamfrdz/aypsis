<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PergerakanKapal;
use App\Models\NaikKapal;
use App\Models\Bl;
use App\Models\MasterPricelistOb;
use App\Models\PranotaOb;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Models\TandaTerima;
use App\Models\Karyawan;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HistoryKontainer;
use Carbon\Carbon;
use App\Exports\ObExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Manifest;

class ObController extends Controller
{
    /**
     * Display the main OB page with ship and voyage selection
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman OB.');
        }

        // Check if filters are provided (coming from select page)
        $kegiatan = $request->get('kegiatan');
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        if ($namaKapal && $noVoyage) {
            // Show OB data table based on kegiatan type
            return $this->showOBData($request, $namaKapal, $noVoyage, $kegiatan);
        }

        // Show ship and voyage selection if no filters
        // Get list of ships from both naik_kapal and bls tables (distinct ship names)
        $shipsNaik = NaikKapal::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        $shipsBl = Bl::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        $shipNames = $shipsNaik->merge($shipsBl)->filter()->unique()->values()->sort()->values();

        // Convert back to objects with nama_kapal property to keep view compatibility
        $ships = $shipNames->map(function ($name) {
            return (object)['nama_kapal' => $name];
        });

        return view('ob.select', compact('ships'));
    }

    /**
     * Normalize ship name for flexible matching
     * Remove dots, extra spaces, and convert to uppercase
     */
    private function normalizeShipName($name)
    {
        // Remove dots, convert to uppercase, and normalize spaces
        $normalized = strtoupper(trim($name));
        $normalized = str_replace('.', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized); // Normalize multiple spaces to single space
        return $normalized;
    }

    /**
     * Display OB data table for selected ship and voyage
     */
    private function showOBData(Request $request, $namaKapal, $noVoyage, $kegiatan = null)
    {
        // Disable browser caching for this page to ensure fresh data
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Trim whitespace untuk memastikan matching yang tepat
        $namaKapal = trim($namaKapal);
        $noVoyage = trim($noVoyage);
        
        // Normalize ship name for flexible matching (remove dots, extra spaces)
        $normalizedKapal = $this->normalizeShipName($namaKapal);

        // Determine data source based on kegiatan
        // If kegiatan is 'muat', FORCE use naik_kapal table
        // If kegiatan is 'bongkar' or not specified, check BL first (legacy behavior)
        $useMuatData = ($kegiatan === 'muat');

        // Check if we have BL records for this ship/voyage using normalized name
        $hasBl = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->exists();

        // CRITICAL: If kegiatan is explicitly 'muat', ALWAYS use naik_kapal table
        // Only use BL if kegiatan is NOT 'muat' AND BL data exists
        if ($kegiatan !== 'muat' && $hasBl) {
            $queryBl = Bl::with(['prospek', 'supir'])
                ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage);
            if ($request->filled('status_ob')) {
                if ($request->status_ob === 'sudah') {
                    $queryBl->where('sudah_ob', true);
                } elseif ($request->status_ob === 'belum') {
                    $queryBl->where('sudah_ob', false);
                }
            }

            if ($request->filled('tipe_kontainer')) {
                $queryBl->where('tipe_kontainer', $request->tipe_kontainer);
            }

            if ($request->filled('size_kontainer')) {
                $queryBl->where('size_kontainer', $request->size_kontainer);
            }

            // Dedicated nomor_kontainer filter for BL list
            if ($request->filled('nomor_kontainer')) {
                // Normalize user input: uppercase and remove non-alphanumeric characters
                $num = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $request->nomor_kontainer));
                // Normalize nomor_kontainer in DB using SQL functions for better matching
                $queryBl->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$num}%"]);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                // Also normalize nomor_kontainer for search comparisons
                $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                $queryBl->where(function($q) use ($search, $searchNum) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%")
                      ->orWhere('nomor_bl', 'like', "%{$search}%");
                });
            }

            // Filter by nama supir
            if ($request->filled('nama_supir')) {
                $namaSupir = $request->nama_supir;
                $queryBl->whereHas('supir', function($q) use ($namaSupir) {
                    $q->where('nama_panggilan', 'like', "%{$namaSupir}%")
                      ->orWhere('nama_lengkap', 'like', "%{$namaSupir}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $bls = $queryBl->orderBy('nomor_bl', 'asc')
                ->paginate($perPage)
                ->withQueryString();

            // Enable query logging
            \DB::enableQueryLog();

            $totalKontainer = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->count();

            // Try multiple ways to count sudah_ob untuk debugging
            $sudahOB_v1 = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->where('sudah_ob', true)
                ->count();
            
            $sudahOB_v1_sql = \DB::getQueryLog();
            \DB::flushQueryLog();
            
            $sudahOB_v2 = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->where('sudah_ob', '=', 1)
                ->count();
            
            $sudahOB_v2_sql = \DB::getQueryLog();
            \DB::flushQueryLog();
            
            $sudahOB_v3 = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->whereNotNull('sudah_ob')
                ->where('sudah_ob', '!=', 0)
                ->count();

            $sudahOB_v3_sql = \DB::getQueryLog();
            \DB::flushQueryLog();

            $sudahOB = $sudahOB_v1; // Use version 1 as default
            $belumOB = $totalKontainer - $sudahOB;

            // Get sample of all BLs untuk debugging
            $allBlsSample = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->select('id', 'nomor_kontainer', 'sudah_ob', 'supir_id', 'tanggal_ob', 'nama_kapal', 'no_voyage')
                ->limit(10)
                ->get()
                ->toArray();

            // Debug logging untuk investigate issue
            \Log::info('OB Index - BL Query Debug', [
                'nama_kapal' => $namaKapal,
                'nama_kapal_normalized' => $normalizedKapal,
                'nama_kapal_length' => strlen($namaKapal),
                'nama_kapal_hex' => bin2hex($namaKapal),
                'no_voyage' => $noVoyage,
                'no_voyage_length' => strlen($noVoyage),
                'no_voyage_hex' => bin2hex($noVoyage),
                'total_kontainer' => $totalKontainer,
                'sudah_ob_v1_true' => $sudahOB_v1,
                'sudah_ob_v2_equals_1' => $sudahOB_v2,
                'sudah_ob_v3_not_null_not_zero' => $sudahOB_v3,
                'belum_ob_count' => $belumOB,
                'sql_query_v1' => $sudahOB_v1_sql,
                'sql_query_v2' => $sudahOB_v2_sql,
                'sql_query_v3' => $sudahOB_v3_sql,
                'all_bls_sample' => $allBlsSample,
                'raw_sudah_ob_data' => Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                    ->where('no_voyage', $noVoyage)
                    ->where('sudah_ob', true)
                    ->select('id', 'nomor_kontainer', 'sudah_ob', 'supir_id', 'tanggal_ob')
                    ->get()
                    ->toArray()
            ]);

            // Pre-compute pricing map and attach biaya & detected_status for each BL
            $pricelists = MasterPricelistOb::all();
            $priceMap = []; // Map: status|size => biaya
            foreach ($pricelists as $pl) {
                $key = ($pl->status_kontainer ?? '') . '|' . ($pl->size_kontainer ?? '');
                $priceMap[$key] = $pl->biaya;
            }
            
            foreach ($bls as $bl) {
                // Determine size first
                $sizeStr = null;
                if (!empty($bl->size_kontainer)) {
                    $sizeInt = intval($bl->size_kontainer);
                    if ($sizeInt === 20) {
                        $sizeStr = '20ft';
                    } elseif ($sizeInt === 40) {
                        $sizeStr = '40ft';
                    }
                }
                
                // STEP 1: Tentukan status dari nama_barang
                $status = 'full'; // default
                
                if (empty($bl->nama_barang) || trim($bl->nama_barang) === '') {
                    // Nama barang kosong = empty
                    $status = 'empty';
                } else {
                    $lowerName = strtolower(trim($bl->nama_barang));
                    // Check if it's an empty container marker
                    // Support various formats: "empty container", "emptycontainer", "empty", "mt", "mty"
                    if (str_contains($lowerName, 'empty') || 
                        $lowerName === 'mt' || // MT = Empty
                        $lowerName === 'mty') { // MTY = Empty
                        $status = 'empty';
                    } else {
                        // Barang lain = full
                        $status = 'full';
                    }
                }
                
                // STEP 2: Set biaya berdasarkan status yang sudah ditentukan
                $key = $status . '|' . ($sizeStr ?? '');
                $bl->biaya = isset($priceMap[$key]) ? $priceMap[$key] : null;
                $bl->detected_status = $status;
            }

            // Get list of supir (drivers) from karyawan table
            $supirs = \App\Models\Karyawan::where('divisi', 'supir')
                ->whereNull('tanggal_berhenti')
                ->orderBy('nama_panggilan')
                ->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);

            // Get list of gudangs for asal kontainer dropdown
            $gudangs = \App\Models\Gudang::where('status', 'aktif')
                ->orderBy('nama_gudang')
                ->get(['id', 'nama_gudang', 'lokasi']);

            return view('ob.index', compact(
                'bls',
                'namaKapal',
                'noVoyage',
                'totalKontainer',
                'sudahOB',
                'belumOB',
                'supirs',
                'gudangs',
                'kegiatan'
            ));
        }

        // Default: Get naik_kapal data for the selected ship and voyage
        $query = NaikKapal::with(['prospek', 'createdBy', 'updatedBy', 'supir'])
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage);

        // Additional filters
        if ($request->filled('status_ob')) {
            if ($request->status_ob === 'sudah') {
                $query->where('sudah_ob', true);
            } elseif ($request->status_ob === 'belum') {
                $query->where('sudah_ob', false);
            }
        }

        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }

        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

            // Dedicated nomor_kontainer filter (exact/partial match)
            if ($request->filled('nomor_kontainer')) {
                // Normalize user input: uppercase and remove non-alphanumeric characters
                $num = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $request->nomor_kontainer));
                // Normalize nomor_kontainer in DB using SQL functions for better matching
                $query->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$num}%"]);
            }

            // General search fallback
            if ($request->filled('search')) {
                $search = $request->search;
                // Normalize nomor kontainer for better matches against formatted values
                $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                $query->where(function($q) use ($search, $searchNum) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%");
                });
            }

            // Filter by nama supir
            if ($request->filled('nama_supir')) {
                $namaSupir = $request->nama_supir;
                $query->whereHas('supir', function($q) use ($namaSupir) {
                    $q->where('nama_panggilan', 'like', "%{$namaSupir}%")
                      ->orWhere('nama_lengkap', 'like', "%{$namaSupir}%");
                });
            }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $naikKapals = $query->orderBy('tanggal_muat', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Statistics
        $totalKontainer = NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->count();

        $sudahOB = NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->where('sudah_ob', true)
            ->count();

        $belumOB = $totalKontainer - $sudahOB;

        // Pre-compute pricing map and attach biaya & detected_status for each naikKapal
        $pricelists = MasterPricelistOb::all();
        $priceMap = []; // Map: status|size => biaya
        foreach ($pricelists as $pl) {
            $key = ($pl->status_kontainer ?? '') . '|' . ($pl->size_kontainer ?? '');
            $priceMap[$key] = $pl->biaya;
        }
        
        foreach ($naikKapals as $nk) {
            // Determine size first
            $sizeStr = null;
            if (!empty($nk->size_kontainer)) {
                $sizeInt = intval($nk->size_kontainer);
                if ($sizeInt === 20) {
                    $sizeStr = '20ft';
                } elseif ($sizeInt === 40) {
                    $sizeStr = '40ft';
                }
            }
            
            // STEP 1: Tentukan status dari jenis_barang
            $status = 'full'; // default
            
            if (empty($nk->jenis_barang) || trim($nk->jenis_barang) === '') {
                // Jenis barang kosong = empty
                $status = 'empty';
            } else {
                $lowerName = strtolower(trim($nk->jenis_barang));
                // Check if it's an empty container marker
                // Support various formats: "empty container", "emptycontainer", "empty", "mt", "mty"
                if (str_contains($lowerName, 'empty') || 
                    $lowerName === 'mt' || // MT = Empty
                    $lowerName === 'mty') { // MTY = Empty
                    $status = 'empty';
                } else {
                    // Barang lain = full
                    $status = 'full';
                }
            }
            
            // STEP 2: Set biaya berdasarkan status yang sudah ditentukan
            $key = $status . '|' . ($sizeStr ?? '');
            $nk->biaya = isset($priceMap[$key]) ? $priceMap[$key] : null;
            $nk->detected_status = $status;
        }

        // Get list of supir (drivers) from karyawan table
        $supirs = \App\Models\Karyawan::where('divisi', 'supir')
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_panggilan')
            ->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);

        // Get list of gudangs for asal kontainer dropdown
        $gudangs = \App\Models\Gudang::where('status', 'aktif')
            ->orderBy('nama_gudang')
            ->get(['id', 'nama_gudang', 'lokasi']);

        return view('ob.index', compact(
            'naikKapals', 
            'namaKapal', 
            'noVoyage', 
            'totalKontainer', 
            'sudahOB', 
            'belumOB',
            'supirs',
            'gudangs',
            'kegiatan'
        ));
    }

    /**
     * Get voyages for a specific ship (AJAX)
     */
    public function getVoyageByKapal(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $namaKapal = $request->get('nama_kapal');
        
        if (!$namaKapal) {
            return response()->json([
                'success' => false,
                'message' => 'Nama kapal tidak boleh kosong'
            ]);
        }

        try {
            \Log::info('getVoyageByKapal called', ['nama_kapal' => $namaKapal]);
            // Get voyages for the selected ship from naik_kapal and bls tables
            // We group by no_voyage and order by latest tanggal_muat per voyage to avoid SQL strict mode errors
            $kapalClean = strtolower(str_replace('.', '', $namaKapal));

            $voyagesNaik = NaikKapal::select('no_voyage')
                // try exact match first; if not, fallback to normalized like
                ->where(function($q) use ($namaKapal, $kapalClean) {
                    $q->where('nama_kapal', $namaKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                })
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->groupBy('no_voyage')
                ->orderByRaw('MAX(tanggal_muat) DESC')
                ->pluck('no_voyage')
                ->toArray();

            // Get voyages from Bl table as well
            $voyagesBl = Bl::select('no_voyage')
                ->where(function($q) use ($namaKapal, $kapalClean) {
                    $q->where('nama_kapal', $namaKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                })
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->orderBy('no_voyage', 'desc')
                ->pluck('no_voyage')
                ->toArray();

            // Merge and unique voyages
            $voyages = collect($voyagesNaik)->merge($voyagesBl)->filter()->unique()->values()->toArray();
            \Log::info('getVoyageByKapal results', ['nama_kapal' => $namaKapal, 'voyages_count' => count($voyages)]);
            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
            \Log::error('getVoyageByKapal error', ['error' => $e->getMessage(), 'nama_kapal' => $namaKapal]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil voyage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of ships from BLS table (for bongkar)
     */
    public function getKapalBongkar(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $kapals = Bl::select('nama_kapal')
                ->whereNotNull('nama_kapal')
                ->where('nama_kapal', '!=', '')
                ->get()
                ->map(function($item) {
                    // Normalize: remove dots after KM/KMP, trim spaces, uppercase
                    return trim(str_replace(['KM.', 'KMP.'], ['KM', 'KMP'], strtoupper($item->nama_kapal)));
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'kapals' => $kapals
            ]);
        } catch (\Exception $e) {
            \Log::error('getKapalBongkar error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kapal'
            ], 500);
        }
    }

    /**
     * Get list of ships from naik_kapal table (for muat)
     */
    public function getKapalMuat(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $kapals = NaikKapal::select('nama_kapal')
                ->whereNotNull('nama_kapal')
                ->where('nama_kapal', '!=', '')
                ->get()
                ->map(function($item) {
                    // Normalize: remove dots after KM/KMP, trim spaces, uppercase
                    return trim(str_replace(['KM.', 'KMP.'], ['KM', 'KMP'], strtoupper($item->nama_kapal)));
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'kapals' => $kapals
            ]);
        } catch (\Exception $e) {
            \Log::error('getKapalMuat error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kapal'
            ], 500);
        }
    }

    /**
     * Get voyages from BLS table for specific ship (for bongkar)
     */
    public function getVoyageBongkar(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $namaKapal = $request->get('nama_kapal');
        
        if (!$namaKapal) {
            return response()->json([
                'success' => false,
                'message' => 'Nama kapal tidak boleh kosong'
            ]);
        }

        try {
            $kapalClean = strtolower(str_replace('.', '', $namaKapal));

            $voyages = Bl::select('no_voyage')
                ->where(function($q) use ($namaKapal, $kapalClean) {
                    $q->where('nama_kapal', $namaKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                })
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->orderBy('no_voyage', 'desc')
                ->pluck('no_voyage')
                ->toArray();

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
            \Log::error('getVoyageBongkar error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil voyage'
            ], 500);
        }
    }

    /**
     * Get voyages from naik_kapal table for specific ship (for muat)
     */
    public function getVoyageMuat(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $namaKapal = $request->get('nama_kapal');
        
        if (!$namaKapal) {
            return response()->json([
                'success' => false,
                'message' => 'Nama kapal tidak boleh kosong'
            ]);
        }

        try {
            $kapalClean = strtolower(str_replace('.', '', $namaKapal));

            $voyages = NaikKapal::select('no_voyage')
                ->where(function($q) use ($namaKapal, $kapalClean) {
                    $q->where('nama_kapal', $namaKapal)
                      ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) like ?", ["%{$kapalClean}%"]);
                })
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->groupBy('no_voyage')
                ->orderByRaw('MAX(tanggal_muat) DESC')
                ->pluck('no_voyage')
                ->toArray();

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
            \Log::error('getVoyageMuat error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil voyage'
            ], 500);
        }
    }

    /**
     * Redirect to OB operations with selected ship and voyage
     */
    public function selectShipVoyage(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan seleksi.');
        }

        $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        $namaKapal = $request->nama_kapal;
        $voyage = $request->no_voyage;

        // Store selection in session for use in OB operations
        // try to find kode_kapal (if any) from naik_kapal records
        $naikKapalRecord = NaikKapal::where('nama_kapal', $namaKapal)->first();
        $kodeKapal = $naikKapalRecord->kode_kapal ?? null;

        session([
            'selected_ob_ship' => [
                'nama_kapal' => $namaKapal,
                'kode_kapal' => $kodeKapal,
            ],
            'selected_ob_voyage' => $voyage
        ]);

        // Redirect to tagihan OB with filters
        return redirect()->route('tagihan-ob.index', [
            'nama_kapal' => $namaKapal,
            'no_voyage' => $voyage
        ])->with('success', "Berhasil memilih kapal {$namaKapal} dengan voyage {$voyage}");
    }

    /**
     * Mark container as OB with selected supir
     */
    public function markAsOB(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            \Log::info("===== START markAsOB =====");
            \Log::info("Request data:", $request->all());
            
            $request->validate([
                'naik_kapal_id' => 'required|exists:naik_kapal,id',
                'supir_id' => 'required|exists:karyawans,id',
                'ke_gudang_id' => 'required|exists:gudangs,id',
                'catatan' => 'nullable|string'
            ]);

            // Start database transaction to ensure data consistency
            DB::beginTransaction();

            $naikKapal = NaikKapal::with('prospek')->findOrFail($request->naik_kapal_id);
            \Log::info("Found naik_kapal:", [
                'id' => $naikKapal->id,
                'nomor_kontainer' => $naikKapal->nomor_kontainer,
                'nama_kapal' => $naikKapal->nama_kapal,
                'no_voyage' => $naikKapal->no_voyage
            ]);
            
            // Update status OB di naik_kapal
            $naikKapal->sudah_ob = true;
            $naikKapal->supir_id = $request->supir_id;
            $naikKapal->tanggal_ob = now();
            $naikKapal->catatan_ob = $request->catatan;
            $naikKapal->updated_by = $user->id;
            // If this naik_kapal was previously marked TL, clear it because supir is selected
            if ($naikKapal->is_tl) {
                $naikKapal->is_tl = false;
            }
            $naikKapal->save();
            \Log::info("Updated naik_kapal OB status");

            // Update gudangs_id in stock_kontainers and kontainers
            try {
                if ($naikKapal->nomor_kontainer) {
                    $targetGudangId = $request->ke_gudang_id;
                    
                    // Update stock_kontainers
                    \App\Models\StockKontainer::where('nomor_seri_gabungan', $naikKapal->nomor_kontainer)
                        ->update(['gudangs_id' => $targetGudangId]);
                    
                    // Update kontainers
                    \App\Models\Kontainer::where('nomor_seri_gabungan', $naikKapal->nomor_kontainer)
                        ->update(['gudangs_id' => $targetGudangId]);
                    
                    \Log::info("Updated gudangs_id to ID: $targetGudangId for container: " . $naikKapal->nomor_kontainer);

                    // Update 'ke' field in naik_kapal for record keeping
                    $gudang = \App\Models\Gudang::find($targetGudangId);
                    if ($gudang) {
                        $naikKapal->ke = $gudang->nama_gudang;
                        $naikKapal->save();

                        // Record history
                        $typeKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $naikKapal->nomor_kontainer)->exists() ? 'kontainer' : 'stock';
                        HistoryKontainer::create([
                            'nomor_kontainer' => $naikKapal->nomor_kontainer,
                            'tipe_kontainer' => $typeKontainer,
                            'jenis_kegiatan' => 'Masuk',
                            'tanggal_kegiatan' => now(),
                            'gudang_id' => $gudang->id,
                            'keterangan' => 'OB (Overbrengen) dari Kapal: ' . ($naikKapal->nama_kapal ?? '-') . '. Voyage: ' . ($naikKapal->no_voyage ?? '-'),
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to update gudangs_id or history: ' . $e->getMessage());
            }

            // Also clear TL flag on related BLs (if any)
            try {
                Bl::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                    ->where('no_voyage', $naikKapal->no_voyage)
                    ->where('nama_kapal', $naikKapal->nama_kapal)
                    ->update(['sudah_tl' => false]);
            } catch (\Exception $e) {
                \Log::warning('Failed to clear BL.sudah_tl for container in markAsOB: ' . $e->getMessage());
            }

            // Otomatis buat record di BLS untuk kegiatan muat
            // Cek dulu apakah sudah ada BL dengan nomor kontainer dan voyage yang sama
            // PENGECUALIAN: Untuk CARGO, izinkan duplikat BL karena nomor_kontainer selalu 'CARGO'
            $isCargoContainer = (
                strtoupper(trim($naikKapal->tipe_kontainer ?? '')) === 'CARGO' ||
                stripos($naikKapal->nomor_kontainer ?? '', 'CARGO') !== false
            );

            \Log::info("Checking existing BL... (is_cargo=" . ($isCargoContainer ? 'true' : 'false') . ")");
            
            // Untuk CARGO: selalu null (buat baru), tidak ada cek duplikat
            // Untuk FCL/LCL: cek apakah sudah ada BL dengan nomor kontainer dan voyage yang sama
            $existingBl = null;
            if (!$isCargoContainer) {
                $existingBl = Bl::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                    ->where('no_voyage', $naikKapal->no_voyage)
                    ->where('nama_kapal', $naikKapal->nama_kapal)
                    ->first();
            }

            // === MANIFEST CREATION DIPINDAH KE SETELAH DB::commit() ===
            // Simpan data yang dibutuhkan untuk manifest
            $manifestDataForLater = [
                'tipe_kontainer' => $naikKapal->tipe_kontainer,
                'nomor_kontainer' => $naikKapal->nomor_kontainer,
                'no_seal' => $naikKapal->no_seal,
                'size_kontainer' => $naikKapal->size_kontainer,
                'nama_kapal' => $naikKapal->nama_kapal,
                'no_voyage' => $naikKapal->no_voyage,
                'jenis_barang' => $naikKapal->jenis_barang,
                'total_volume' => $naikKapal->total_volume,
                'total_tonase' => $naikKapal->total_tonase,
                'asal_kontainer' => $naikKapal->asal_kontainer,
                'ke' => $naikKapal->ke,
                'prospek_id' => $naikKapal->prospek_id,
                'prospek_pt_pengirim' => $naikKapal->prospek ? $naikKapal->prospek->pt_pengirim : null,
                'prospek_tujuan_pengiriman' => $naikKapal->prospek ? $naikKapal->prospek->tujuan_pengiriman : null,
            ];

            if (!$existingBl) {
                \Log::info("No existing BL found, creating new BL record...");
                
                // Buat record baru di BLS
                $bl = new Bl();
                
                // Copy data dari naik_kapal ke BLS
                $bl->nomor_kontainer = $naikKapal->nomor_kontainer;
                $bl->no_seal = $naikKapal->no_seal;
                $bl->nama_barang = $naikKapal->jenis_barang;
                $bl->tipe_kontainer = $naikKapal->tipe_kontainer;
                $bl->size_kontainer = $naikKapal->size_kontainer;
                $bl->nama_kapal = $naikKapal->nama_kapal;
                $bl->no_voyage = $naikKapal->no_voyage;
                $bl->asal_kontainer = $naikKapal->asal_kontainer;
                $bl->ke = $naikKapal->ke;
                $bl->pelabuhan_asal = $naikKapal->pelabuhan_asal;
                $bl->pelabuhan_tujuan = $naikKapal->pelabuhan_tujuan;
                $bl->tonnage = $naikKapal->total_tonase;
                $bl->volume = $naikKapal->total_volume;
                $bl->kuantitas = $naikKapal->kuantitas;
                
                // Set prospek_id jika ada dan ambil data tambahan dari prospek
                if ($naikKapal->prospek_id && $naikKapal->prospek) {
                    $bl->prospek_id = $naikKapal->prospek_id;
                    
                    // Ambil data lengkap dari prospek
                    $prospek = $naikKapal->prospek;
                    $bl->pengirim = $prospek->pt_pengirim;
                    // Ambil penerima dari tanda terima jika ada, jika tidak gunakan tujuan pengiriman
                    $penerima = null;
                    if ($prospek->tandaTerima) {
                        $penerima = $prospek->tandaTerima->penerima;
                    }
                    $bl->penerima = $penerima ?? $prospek->tujuan_pengiriman;
                    
                    // Jika no_seal belum ada, ambil dari prospek
                    if (empty($bl->no_seal) && !empty($prospek->no_seal)) {
                        $bl->no_seal = $prospek->no_seal;
                    }
                    // Ambil data lain dari prospek jika belum ada
                    if (empty($bl->tonnage) && !empty($prospek->total_ton)) {
                        $bl->tonnage = $prospek->total_ton;
                    }
                    if (empty($bl->volume) && !empty($prospek->total_volume)) {
                        $bl->volume = $prospek->total_volume;
                    }
                    if (empty($bl->kuantitas) && !empty($prospek->kuantitas)) {
                        $bl->kuantitas = $prospek->kuantitas;
                    }
                    
                    // Copy additional fields dari prospek jika ada
                    if (!empty($prospek->tanggal_muat)) {
                        $bl->tanggal_berangkat = $prospek->tanggal_muat;
                    }
                    
                    // Jika ada surat jalan terkait, ambil data alamat_pengiriman dan contact_person
                    if ($prospek->surat_jalan_id) {
                        $suratJalan = \App\Models\SuratJalan::find($prospek->surat_jalan_id);
                        if ($suratJalan) {
                            if (!empty($suratJalan->alamat_tujuan)) {
                                $bl->alamat_pengiriman = $suratJalan->alamat_tujuan;
                            }
                            if (!empty($suratJalan->contact_person)) {
                                $bl->contact_person = $suratJalan->contact_person;
                            }
                        }
                    }
                    
                    // Cek data dari Tanda Terima Terlebih Dahulu (Prioritas)
                    if ($prospek->tanda_terima_id) {
                         $tandaTerima = \App\Models\TandaTerima::find($prospek->tanda_terima_id);
                         if ($tandaTerima) {
                             if (!empty($tandaTerima->alamat_penerima)) {
                                 $bl->alamat_pengiriman = $tandaTerima->alamat_penerima;
                             }
                             if (!empty($tandaTerima->contact_person)) {
                                 $bl->contact_person = $tandaTerima->contact_person;
                             }
                         }
                    }

                    // Jika masih kosong, coba ambil dari Surat Jalan
                    if (empty($bl->alamat_pengiriman) && $prospek->surat_jalan_id) {
                        $suratJalan = \App\Models\SuratJalan::find($prospek->surat_jalan_id);
                        if ($suratJalan) {
                            if (!empty($suratJalan->alamat_tujuan)) {
                                $bl->alamat_pengiriman = $suratJalan->alamat_tujuan;
                            }
                            // Jika contact person kosong, ambil dari SJ
                            if (empty($bl->contact_person) && !empty($suratJalan->contact_person)) {
                                $bl->contact_person = $suratJalan->contact_person;
                            }
                        }
                    }
                }
                
                // Set status OB langsung
                $bl->sudah_ob = true;
                $bl->supir_id = $request->supir_id;
                $bl->tanggal_ob = now();
                $bl->catatan_ob = $request->catatan;
                
                // Generate nomor BL otomatis jika belum ada
                $lastBl = Bl::whereNotNull('nomor_bl')
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($lastBl && $lastBl->nomor_bl) {
                    // Extract number from last BL
                    preg_match('/\d+/', $lastBl->nomor_bl, $matches);
                    $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                    $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                    $bl->nomor_bl = 'BL-' . $nextNumber;
                } else {
                    $bl->nomor_bl = 'BL-000001';
                }
                
                \Log::info("Generated nomor_bl: " . $bl->nomor_bl);
                
                $bl->created_by = $user->id;
                $bl->updated_by = $user->id;
                
                \Log::info("Saving BL record with data:", [
                    'nomor_bl' => $bl->nomor_bl,
                    'nomor_kontainer' => $bl->nomor_kontainer,
                    'nama_kapal' => $bl->nama_kapal,
                    'no_voyage' => $bl->no_voyage,
                    'tipe_kontainer' => $bl->tipe_kontainer,
                    'size_kontainer' => $bl->size_kontainer
                ]);
                
                $bl->save();
                
                \Log::info("✅ SUCCESS: Auto-created BL record", [
                    'naik_kapal_id' => $naikKapal->id,
                    'bl_id' => $bl->id,
                    'nomor_bl' => $bl->nomor_bl,
                    'nomor_kontainer' => $bl->nomor_kontainer
                ]);
            } else {
                \Log::info("Found existing BL, updating OB status...", [
                    'bl_id' => $existingBl->id,
                    'nomor_kontainer' => $existingBl->nomor_kontainer
                ]);
                
                // Jika sudah ada, update status OB-nya
                $existingBl->sudah_ob = true;
                $existingBl->supir_id = $request->supir_id;
                $existingBl->tanggal_ob = now();
                $existingBl->catatan_ob = $request->catatan;
                $existingBl->updated_by = $user->id;
                // If BL was previously TL, clear TL status because now a supir is assigned
                if ($existingBl->sudah_tl) {
                    $existingBl->sudah_tl = false;
                }
                $existingBl->save();
                
                \Log::info("✅ SUCCESS: Updated existing BL record OB status");

                // Also clear TL flag on related NaikKapal rows (if any)
                try {
                    NaikKapal::where('nomor_kontainer', $existingBl->nomor_kontainer)
                        ->where('no_voyage', $existingBl->no_voyage)
                        ->where('nama_kapal', $existingBl->nama_kapal)
                        ->update(['is_tl' => false]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to clear NaikKapal.is_tl for container in markAsOB: ' . $e->getMessage());
                }
            }

            // Update status prospek menjadi 'sudah_muat' jika ada
            if ($naikKapal->prospek_id) {
                $prospek = Prospek::find($naikKapal->prospek_id);
                if ($prospek) {
                    $prospek->status = Prospek::STATUS_SUDAH_MUAT;
                    $prospek->updated_by = $user->id;
                    $prospek->save();
                    \Log::info("✅ SUCCESS: Updated prospek status to 'sudah_muat'", [
                        'prospek_id' => $prospek->id,
                        'nomor_kontainer' => $prospek->nomor_kontainer
                    ]);
                }
            }

            // Commit transaction - all changes saved successfully
            DB::commit();

            \Log::info("===== END markAsOB - transaction committed, now creating manifest =====");

            // === BUAT MANIFEST SETELAH COMMIT ===
            // Manifest dibuat di luar transaksi utama agar tidak ikut di-rollback
            // jika ada error di bagian lain (BL, stock_kontainer, dsb)
            try {
                \Log::info("🚢 Creating manifest records for MUAT operation (after commit)...");

                if (strtoupper(trim($manifestDataForLater['tipe_kontainer'])) === 'LCL') {
                    \Log::info("LCL container detected, finding tanda terima...");

                    $tandaTerimaRecords = \App\Models\TandaTerimaLclKontainerPivot::where('nomor_kontainer', $manifestDataForLater['nomor_kontainer'])
                        ->with('tandaTerima.items')
                        ->get();

                    if ($tandaTerimaRecords->count() > 0) {
                        \Log::info("Found " . $tandaTerimaRecords->count() . " tanda terima for LCL container");

                        foreach ($tandaTerimaRecords as $pivot) {
                            $tandaTerima = $pivot->tandaTerima;
                            if (!$tandaTerima) continue;

                            $manifest = new \App\Models\Manifest();
                            $manifest->nomor_kontainer = $manifestDataForLater['nomor_kontainer'];
                            $manifest->no_seal = $pivot->nomor_seal ?? $manifestDataForLater['no_seal'];
                            $manifest->tipe_kontainer = $manifestDataForLater['tipe_kontainer'];
                            $manifest->size_kontainer = $manifestDataForLater['size_kontainer'];
                            $manifest->nama_kapal = $manifestDataForLater['nama_kapal'];
                            $manifest->no_voyage = $manifestDataForLater['no_voyage'];
                            $manifest->nomor_tanda_terima = $tandaTerima->nomor_tanda_terima;
                            $manifest->pengirim = $tandaTerima->nama_pengirim;
                            $manifest->penerima = $tandaTerima->penerima;
                            $manifest->alamat_pengirim = $tandaTerima->alamat_pengirim;
                            $manifest->alamat_penerima = $tandaTerima->alamat_penerima;
                            $namaBarang = $tandaTerima->items->pluck('nama_barang')->filter()->implode(', ');
                            $manifest->nama_barang = $namaBarang ?: $manifestDataForLater['jenis_barang'];
                            $manifest->volume = $tandaTerima->items->sum('meter_kubik');
                            $manifest->tonnage = $tandaTerima->items->sum('tonase');
                            $manifest->pelabuhan_muat = $manifestDataForLater['asal_kontainer'];
                            $manifest->pelabuhan_bongkar = $manifestDataForLater['ke'];
                            $manifest->tanggal_berangkat = now();
                            $manifest->penerimaan = $tandaTerima->tanggal_tanda_terima;
                            if ($manifestDataForLater['prospek_id']) {
                                $manifest->prospek_id = $manifestDataForLater['prospek_id'];
                            }

                            // Generate nomor BL
                            $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
                            if ($lastManifest && $lastManifest->nomor_bl) {
                                preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                                $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                                $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                            } else {
                                $manifest->nomor_bl = 'MNF-000001';
                            }

                            $manifest->created_by = $user->id;
                            $manifest->updated_by = $user->id;
                            $manifest->save();

                            \Log::info("✅ Created manifest for tanda terima: " . $tandaTerima->nomor_tanda_terima, [
                                'manifest_id' => $manifest->id, 'nomor_bl' => $manifest->nomor_bl
                            ]);
                        }
                    } else {
                        \Log::warning("No tanda terima found for LCL container, creating fallback manifest");

                        // Fallback: buat 1 manifest
                        $manifest = new \App\Models\Manifest();
                        $manifest->nomor_kontainer = $manifestDataForLater['nomor_kontainer'];
                        $manifest->no_seal = $manifestDataForLater['no_seal'];
                        $manifest->tipe_kontainer = $manifestDataForLater['tipe_kontainer'];
                        $manifest->size_kontainer = $manifestDataForLater['size_kontainer'];
                        $manifest->nama_kapal = $manifestDataForLater['nama_kapal'];
                        $manifest->no_voyage = $manifestDataForLater['no_voyage'];
                        $manifest->nama_barang = $manifestDataForLater['jenis_barang'];
                        $manifest->volume = $manifestDataForLater['total_volume'];
                        $manifest->tonnage = $manifestDataForLater['total_tonase'];
                        $manifest->pelabuhan_muat = $manifestDataForLater['asal_kontainer'];
                        $manifest->pelabuhan_bongkar = $manifestDataForLater['ke'];
                        $manifest->tanggal_berangkat = now();
                        if ($manifestDataForLater['prospek_id']) {
                            $manifest->prospek_id = $manifestDataForLater['prospek_id'];
                        }

                        $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
                        if ($lastManifest && $lastManifest->nomor_bl) {
                            preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                            $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                        } else {
                            $manifest->nomor_bl = 'MNF-000001';
                        }

                        $manifest->created_by = $user->id;
                        $manifest->updated_by = $user->id;
                        $manifest->save();

                        \Log::info("✅ Created fallback manifest (no tanda terima found)");
                    }
                } else {
                    // FCL atau CARGO
                    \Log::info("FCL/CARGO manifest processing...");
                    
                    // Cek apakah CARGO (tipe atau nomor kontainer)
                    $isCargo = (
                        strtoupper(trim($manifestDataForLater['tipe_kontainer'] ?? '')) === 'CARGO' || 
                        stripos($manifestDataForLater['nomor_kontainer'] ?? '', 'CARGO') !== false
                    );
                    
                    // Cek apakah manifest sudah ada
                    $existingManifest = null;
                    
                    // Hanya cek duplikasi jika BUKAN cargo (FCL harus unik per voyage)
                    if (!$isCargo) {
                        $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $manifestDataForLater['nomor_kontainer'])
                            ->where('no_voyage', $manifestDataForLater['no_voyage'])
                            ->where('nama_kapal', $manifestDataForLater['nama_kapal'])
                            ->first();
                    }

                    if ($isCargo || !$existingManifest) {
                        \Log::info(($isCargo ? "CARGO" : "FCL") . " container, creating manifest...", [
                           'is_cargo' => $isCargo,
                           'nomor_kontainer' => $manifestDataForLater['nomor_kontainer']
                        ]);

                        $manifest = new \App\Models\Manifest();
                        $manifest->nomor_kontainer = $manifestDataForLater['nomor_kontainer'];
                        $manifest->no_seal = $manifestDataForLater['no_seal'];
                        $manifest->tipe_kontainer = $manifestDataForLater['tipe_kontainer'];
                        $manifest->size_kontainer = $manifestDataForLater['size_kontainer'];
                        $manifest->nama_kapal = $manifestDataForLater['nama_kapal'];
                        $manifest->no_voyage = $manifestDataForLater['no_voyage'];
                        $manifest->nama_barang = $manifestDataForLater['jenis_barang'];
                        $manifest->volume = $manifestDataForLater['total_volume'];
                        $manifest->tonnage = $manifestDataForLater['total_tonase'];
                        $manifest->pelabuhan_muat = $manifestDataForLater['asal_kontainer'];
                        $manifest->pelabuhan_bongkar = $manifestDataForLater['ke'];
                        $manifest->tanggal_berangkat = now();

                        if ($manifestDataForLater['prospek_id']) {
                            $manifest->prospek_id = $manifestDataForLater['prospek_id'];
                            $manifest->pengirim = $manifestDataForLater['prospek_pt_pengirim'];
                            $manifest->penerima = $manifestDataForLater['prospek_tujuan_pengiriman'];
                        }

                        $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
                        if ($lastManifest && $lastManifest->nomor_bl) {
                            preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                            $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                        } else {
                            $manifest->nomor_bl = 'MNF-000001';
                        }

                        $manifest->created_by = $user->id;
                        $manifest->updated_by = $user->id;
                        $manifest->save();

                        \Log::info("✅ Created manifest for " . ($isCargo ? "CARGO" : "FCL"), [
                            'manifest_id' => $manifest->id,
                            'nomor_bl' => $manifest->nomor_bl,
                            'nomor_kontainer' => $manifest->nomor_kontainer,
                            'tipe_kontainer' => $manifest->tipe_kontainer,
                            'nama_kapal' => $manifest->nama_kapal,
                            'no_voyage' => $manifest->no_voyage,
                        ]);
                    } else {
                        \Log::info("ℹ️ Skipping manifest creation for FCL: Manifest already exists.", [
                            'nomor_kontainer' => $manifestDataForLater['nomor_kontainer'],
                            'existing_id' => $existingManifest->id
                        ]);
                    }
                }
            } catch (\Exception $manifestException) {
                // Log error manifest tapi jangan gagalkan seluruh proses OB
                \Log::error('❌ ERROR saat membuat manifest (OB status sudah tersimpan): ' . $manifestException->getMessage());
                \Log::error('Manifest stack trace: ' . $manifestException->getTraceAsString());
            }
            // === END MANIFEST CREATION ===

            \Log::info("===== END markAsOB SUCCESS =====");

            return response()->json([
                'success' => true,
                'message' => 'Kontainer berhasil ditandai sudah OB, data BL dan status prospek telah diupdate'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            \Log::error('❌ ERROR in markAsOB: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark BL as OB with selected supir
     */
    public function markAsOBBl(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'bl_id' => 'required|exists:bls,id',
                'supir_id' => 'required|exists:karyawans,id',
                'ke_gudang_id' => 'required|exists:gudangs,id',
                'catatan' => 'nullable|string',
                'retur_barang' => 'nullable|string'
            ]);

            // Start database transaction
            DB::beginTransaction();

            $bl = Bl::findOrFail($request->bl_id);
            
            // Update status OB
            $bl->sudah_ob = true;
            $bl->supir_id = $request->supir_id;
            $bl->tanggal_ob = now();
            $bl->catatan_ob = $request->catatan;
            $bl->updated_by = $user->id;
            // Clear TL flag if present because assigning a supir means it's no longer TL
            if ($bl->sudah_tl) {
                $bl->sudah_tl = false;
            }
            $bl->save();

            // Update gudangs_id in stock_kontainers and kontainers
            try {
                if ($bl->nomor_kontainer) {
                    $targetGudangId = $request->ke_gudang_id;
                    
                    // Update stock_kontainers
                    \App\Models\StockKontainer::where('nomor_seri_gabungan', $bl->nomor_kontainer)
                        ->update(['gudangs_id' => $targetGudangId]);
                    
                    // Update kontainers
                    \App\Models\Kontainer::where('nomor_seri_gabungan', $bl->nomor_kontainer)
                        ->update(['gudangs_id' => $targetGudangId]);
                    
                    \Log::info("Updated gudangs_id to ID: $targetGudangId for container: " . $bl->nomor_kontainer);

                    // Update 'ke' field in BL for record keeping
                    $gudang = \App\Models\Gudang::find($targetGudangId);
                    if ($gudang) {
                        $bl->ke = $gudang->nama_gudang;
                        $bl->save();

                        // Record history
                        $typeKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $bl->nomor_kontainer)->exists() ? 'kontainer' : 'stock';
                        HistoryKontainer::create([
                            'nomor_kontainer' => $bl->nomor_kontainer,
                            'tipe_kontainer' => $typeKontainer,
                            'jenis_kegiatan' => 'Masuk',
                            'tanggal_kegiatan' => now(),
                            'gudang_id' => $gudang->id,
                            'keterangan' => 'OB (Overbrengen) dari Kapal: ' . ($bl->nama_kapal ?? '-') . '. Voyage: ' . ($bl->no_voyage ?? '-'),
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to update gudangs_id or history: ' . $e->getMessage());
            }

            // Update retur_barang di surat_jalans based on nomor_kontainer
            if ($request->filled('retur_barang') && $bl->nomor_kontainer) {
                try {
                    \App\Models\SuratJalan::where('no_kontainer', $bl->nomor_kontainer)
                        ->where('kegiatan', 'bongkar')
                        ->update(['retur_barang' => $request->retur_barang]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to update retur_barang in surat_jalans: ' . $e->getMessage());
                }
            }

            // Also clear any related NaikKapal.is_tl records
            try {
                NaikKapal::where('nomor_kontainer', $bl->nomor_kontainer)
                    ->where('no_voyage', $bl->no_voyage)
                    ->where('nama_kapal', $bl->nama_kapal)
                    ->update(['is_tl' => false]);
            } catch (\Exception $e) {
                \Log::warning('Failed to clear NaikKapal.is_tl for container in markAsOBBl: ' . $e->getMessage());
            }

            // Commit transaction


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BL berhasil ditandai sudah OB'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            \Log::error('Mark as OB BL error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unmark BL from OB status
     */
    public function unmarkOBBl(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'bl_id' => 'required|exists:bls,id'
            ]);

            $bl = Bl::findOrFail($request->bl_id);
            
            // Reset OB status
            $bl->sudah_ob = false;
            $bl->supir_id = null;
            $bl->tanggal_ob = null;
            $bl->catatan_ob = null;
            $bl->updated_by = $user->id;
            $bl->save();

            return response()->json([
                'success' => true,
                'message' => 'BL berhasil dibatalkan dari status OB'
            ]);
        } catch (\Exception $e) {
            \Log::error('Unmark OB BL error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unmark container from OB status
     */
    public function unmarkOB(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'naik_kapal_id' => 'required|exists:naik_kapal,id'
            ]);

            $naikKapal = NaikKapal::findOrFail($request->naik_kapal_id);
            
            // Reset OB status
            $naikKapal->sudah_ob = false;
            $naikKapal->supir_id = null;
            $naikKapal->tanggal_ob = null;
            $naikKapal->catatan_ob = null;
            $naikKapal->updated_by = $user->id;
            $naikKapal->save();

            // Set Prospek status to ACTIVE if exists
            if ($naikKapal->prospek) {
                $naikKapal->prospek->status = 'ACTIVE';
                $naikKapal->prospek->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status OB kontainer berhasil dibatalkan dan status Prospek dikembalikan ke ACTIVE'
            ]);
        } catch (\Exception $e) {
            \Log::error('Unmark OB error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear TL status for a BL record and related NaikKapal (if any)
     */
    public function clearTLBl(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'bl_id' => 'required|exists:bls,id'
            ]);

            $bl = Bl::findOrFail($request->bl_id);

            // Clear TL flag on BL
            $bl->sudah_tl = false;
            $bl->updated_by = $user->id;
            $bl->save();

            // Also attempt to clear corresponding NaikKapal.is_tl for consistency
            try {
                NaikKapal::where('nomor_kontainer', $bl->nomor_kontainer)
                    ->where('no_voyage', $bl->no_voyage)
                    ->where('nama_kapal', $bl->nama_kapal)
                    ->update(['is_tl' => false]);
            } catch (\Exception $e) {
                \Log::warning('Failed to clear NaikKapal.is_tl in clearTLBl: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Status TL berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Clear TL BL error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear TL status for a NaikKapal record and related BL (if any)
     */
    public function clearTL(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'naik_kapal_id' => 'required|exists:naik_kapal,id'
            ]);

            $nk = NaikKapal::findOrFail($request->naik_kapal_id);

            // Clear TL flag on NaikKapal
            $nk->is_tl = false;
            $nk->updated_by = $user->id;
            $nk->save();

            // Also attempt to clear corresponding BL.sudah_tl for consistency
            try {
                Bl::where('nomor_kontainer', $nk->nomor_kontainer)
                    ->where('no_voyage', $nk->no_voyage)
                    ->where('nama_kapal', $nk->nama_kapal)
                    ->update(['sudah_tl' => false]);
            } catch (\Exception $e) {
                \Log::warning('Failed to clear Bl.sudah_tl in clearTL: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Status TL berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Clear TL error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store selected items into a new OB pranota
     */
    public function masukPranota(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('ob-view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'nomor_pranota' => 'required|string|unique:pranota_obs,nomor_pranota',
                'tanggal_ob' => 'required|date',
                'nomor_accurate' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'items' => 'required|array',
                'items.*.id' => 'required|integer',
                'items.*.type' => 'required|in:naik_kapal,bl',
            ]);

            // Get nama_kapal and no_voyage from session or request
            $namaKapal = session('selected_ob_ship.nama_kapal') ?? $request->get('nama_kapal');
            $noVoyage = session('selected_ob_voyage') ?? $request->get('no_voyage');

            if (!$namaKapal || !$noVoyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi kapal dan voyage tidak ditemukan'
                ], 400);
            }

            // Build reverse mapping from pricelist: biaya => status
            $pricelist = \App\Models\MasterPricelistOb::all();
            $reverseMap = [];
            foreach ($pricelist as $p) {
                // Normalize size - remove 'ft' suffix if present, then add it back
                $sizeRaw = preg_replace('/ft$/i', '', $p->size_kontainer);
                $sizeStr = $sizeRaw . 'ft';
                $statusLower = strtolower($p->status_kontainer); // Fixed: use status_kontainer column
                $biaya = (int) $p->biaya;
                // Map biaya|size => status
                $key = $biaya . '|' . $sizeStr;
                if (!isset($reverseMap[$key])) {
                    $reverseMap[$key] = $statusLower;
                }
                \Log::info("Pricelist map: $key => $statusLower");
            }

            // Build snapshot items before create so pranota keeps essential info
            $itemsToSave = $request->items;
            foreach ($itemsToSave as $idx => $it) {
                if (!isset($it['type']) || !isset($it['id'])) continue;
                if ($it['type'] === 'bl') {
                    $bl = \DB::table('bls')->find($it['id']);
                    if ($bl) {
                        $itemsToSave[$idx]['nomor_kontainer'] = $bl->nomor_kontainer ?? null;
                        $itemsToSave[$idx]['nama_barang'] = $bl->nama_barang ?? null;
                        $itemsToSave[$idx]['size'] = $bl->size_kontainer ?? null;
                        
                        // Check if TL container - TL containers should have no biaya
                        $isTL = ($bl->sudah_tl === 1 || $bl->sudah_tl === true || $bl->sudah_tl === '1');
                        $itemsToSave[$idx]['is_tl'] = $isTL; // Store TL flag
                        
                        // Check if nama_barang contains "EMPTY" to determine status
                        $namaBarangUpper = strtoupper($bl->nama_barang ?? '');
                        $isEmptyByName = str_contains($namaBarangUpper, 'EMPTY');
                        
                        if ($isTL) {
                            $itemsToSave[$idx]['biaya'] = null; // TL containers have no cost
                            // For TL, determine status from nama_barang
                            $itemsToSave[$idx]['status'] = $isEmptyByName ? 'empty' : 'full';
                        } else {
                            // Use biaya from request if provided, otherwise from DB
                            $itemsToSave[$idx]['biaya'] = isset($it['biaya']) && $it['biaya'] !== '' ? $it['biaya'] : ($bl->biaya ?? null);
                            
                            // Recalculate status from biaya to ensure accuracy
                            $biaya = (int) ($itemsToSave[$idx]['biaya'] ?? 0);
                            // Normalize size - remove 'ft' suffix if present, then add it back
                            $sizeRaw = preg_replace('/ft$/i', '', $bl->size_kontainer ?? '');
                            $sizeStr = $sizeRaw . 'ft';
                            $mapKey = $biaya . '|' . $sizeStr;
                            \Log::info("BL lookup: biaya=$biaya, size=$sizeStr, key=$mapKey, found=" . (isset($reverseMap[$mapKey]) ? $reverseMap[$mapKey] : 'NOT FOUND'));
                            if (isset($reverseMap[$mapKey])) {
                                $itemsToSave[$idx]['status'] = $reverseMap[$mapKey];
                            } else {
                                // Fallback: check nama_barang for "EMPTY", then request status, then default
                                if ($isEmptyByName) {
                                    $itemsToSave[$idx]['status'] = 'empty';
                                } else {
                                    $itemsToSave[$idx]['status'] = $it['status'] ?? 'full';
                                }
                            }
                        }
                        
                        if (!empty($bl->supir_id)) {
                            $sup = \DB::table('karyawans')->find($bl->supir_id);
                            $itemsToSave[$idx]['supir'] = $sup ? ($sup->nama_lengkap ?? $sup->name ?? null) : null;
                        }
                    }
                } elseif ($it['type'] === 'naik_kapal') {
                    $nk = \DB::table('naik_kapal')->find($it['id']);
                    if ($nk) {
                        $itemsToSave[$idx]['nomor_kontainer'] = $nk->nomor_kontainer ?? null;
                        $itemsToSave[$idx]['nama_barang'] = $nk->jenis_barang ?? ($nk->nama_barang ?? null);
                        $itemsToSave[$idx]['size'] = $nk->size_kontainer ?? ($nk->ukuran_kontainer ?? null);
                        
                        // Check if TL container - TL containers should have no biaya
                        $isTL = ($nk->is_tl === 1 || $nk->is_tl === true || $nk->is_tl === '1');
                        $itemsToSave[$idx]['is_tl'] = $isTL; // Store TL flag
                        
                        // Check if nama_barang contains "EMPTY" to determine status
                        $namaBarangNK = $nk->jenis_barang ?? ($nk->nama_barang ?? '');
                        $namaBarangUpperNK = strtoupper($namaBarangNK);
                        $isEmptyByNameNK = str_contains($namaBarangUpperNK, 'EMPTY');
                        
                        if ($isTL) {
                            $itemsToSave[$idx]['biaya'] = null; // TL containers have no cost
                            // For TL, determine status from nama_barang
                            $itemsToSave[$idx]['status'] = $isEmptyByNameNK ? 'empty' : 'full';
                        } else {
                            // Use biaya from request if provided, otherwise from DB
                            $itemsToSave[$idx]['biaya'] = isset($it['biaya']) && $it['biaya'] !== '' ? $it['biaya'] : ($nk->biaya ?? null);
                            
                            // Recalculate status from biaya to ensure accuracy
                            $biaya = (int) ($itemsToSave[$idx]['biaya'] ?? 0);
                            // Normalize size - remove 'ft' suffix if present, then add it back
                            $sizeRaw = preg_replace('/ft$/i', '', $nk->size_kontainer ?? $nk->ukuran_kontainer ?? '');
                            $sizeStr = $sizeRaw . 'ft';
                            $mapKey = $biaya . '|' . $sizeStr;
                            \Log::info("NK lookup: biaya=$biaya, size=$sizeStr, key=$mapKey, found=" . (isset($reverseMap[$mapKey]) ? $reverseMap[$mapKey] : 'NOT FOUND'));
                            if (isset($reverseMap[$mapKey])) {
                                $itemsToSave[$idx]['status'] = $reverseMap[$mapKey];
                            } else {
                                // Fallback: check nama_barang for "EMPTY", then request status, then default
                                if ($isEmptyByNameNK) {
                                    $itemsToSave[$idx]['status'] = 'empty';
                                } else {
                                    $itemsToSave[$idx]['status'] = $it['status'] ?? 'full';
                                }
                            }
                        }
                        
                        if (!empty($nk->supir_id)) {
                            $sup = \DB::table('karyawans')->find($nk->supir_id);
                            $itemsToSave[$idx]['supir'] = $sup ? ($sup->nama_lengkap ?? $sup->name ?? null) : null;
                        }
                    }
                }
            }

            // Create pranota
            $pranota = PranotaOb::create([
                'nomor_pranota' => $request->nomor_pranota,
                'nama_kapal' => $namaKapal,
                'no_voyage' => $noVoyage,
                'tanggal_ob' => $request->tanggal_ob,
                'nomor_accurate' => $request->nomor_accurate,
                'adjustment' => $request->adjustment ?? 0,
                'keterangan' => $request->keterangan,
                'items' => $itemsToSave,
                'created_by' => $user->id,
            ]);

            // Create pivot rows
            foreach ($itemsToSave as $it) {
                $itemType = null;
                if (isset($it['type'])) {
                    if ($it['type'] === 'bl') $itemType = \App\Models\Bl::class;
                    elseif ($it['type'] === 'naik_kapal') $itemType = \App\Models\NaikKapal::class;
                    elseif ($it['type'] === 'tagihan_ob') $itemType = \App\Models\TagihanOb::class;
                }
                \App\Models\PranotaObItem::create([
                    'pranota_ob_id' => $pranota->id,
                    'item_type' => $itemType,
                    'item_id' => $it['id'] ?? null,
                    'nomor_kontainer' => $it['nomor_kontainer'] ?? null,
                    'nama_barang' => $it['nama_barang'] ?? ($it['jenis_barang'] ?? null),
                    'supir' => $it['supir'] ?? null,
                    'size' => $it['size'] ?? ($it['size_kontainer'] ?? null),
                    'biaya' => $it['biaya'] ?? null,
                    'status' => $it['status'] ?? 'full',
                    'created_by' => $user->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pranota OB berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            \Log::error('Masuk Pranota error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print OB data for a specific ship and voyage
     */
    public function print(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak halaman OB.');
        }

        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');
        $kegiatan = $request->get('kegiatan');
        $statusFilter = $request->get('status_ob');
        $tipeFilter = $request->get('tipe_kontainer');

        if (!$namaKapal || !$noVoyage) {
            abort(400, 'Nama kapal dan nomor voyage harus diisi');
        }

        // Trim whitespace
        $namaKapal = trim($namaKapal);
        $noVoyage = trim($noVoyage);

        // Check if we have BL records for this ship/voyage
        $hasBl = Bl::where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage)
            ->exists();

        // Determine which data to use based on kegiatan
        // If kegiatan is 'bongkar' or not specified but BL exists, use BL
        // If kegiatan is 'muat', always use naik_kapal
        if ($kegiatan === 'bongkar' || ($kegiatan !== 'muat' && $hasBl)) {
            // Use BL data
            $query = Bl::with(['prospek', 'supir'])
                ->where('nama_kapal', $namaKapal)
                ->where('no_voyage', $noVoyage);

            // Apply filters
            if ($statusFilter) {
                if ($statusFilter === 'sudah') {
                    $query->where('sudah_ob', true);
                } elseif ($statusFilter === 'belum') {
                    $query->where('sudah_ob', false);
                }
            }

            if ($tipeFilter) {
                $query->where('tipe_kontainer', $tipeFilter);
            }

            $bls = $query->orderBy('nomor_bl', 'asc')->get();

            return view('ob.print', compact(
                'bls',
                'namaKapal',
                'noVoyage',
                'statusFilter'
            ));
        } else {
            // Use naik_kapal data
            $query = NaikKapal::with(['prospek', 'supir'])
                ->where('nama_kapal', $namaKapal)
                ->where('no_voyage', $noVoyage);

            // Apply filters
            if ($statusFilter) {
                if ($statusFilter === 'sudah') {
                    $query->where('sudah_ob', true);
                } elseif ($statusFilter === 'belum') {
                    $query->where('sudah_ob', false);
                }
            }

            if ($tipeFilter) {
                $query->where('tipe_kontainer', $tipeFilter);
            }

            $naikKapals = $query->orderBy('tanggal_muat', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('ob.print', compact(
                'naikKapals',
                'namaKapal',
                'noVoyage',
                'statusFilter'
            ));
        }
    }

    /**
     * Export OB data to Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk export OB.');
        }

        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');
        $kegiatan = $request->get('kegiatan');
        $statusFilter = $request->get('status_ob');
        $tipeFilter = $request->get('tipe_kontainer');
        $searchFilter = $request->get('search');

        if (!$namaKapal || !$noVoyage) {
            abort(400, 'Nama kapal dan nomor voyage harus diisi');
        }

        // Trim whitespace
        $namaKapal = trim($namaKapal);
        $noVoyage = trim($noVoyage);
        
        // Normalize ship name
        $normalizedKapal = $this->normalizeShipName($namaKapal);

        // Check if we have BL records for this ship/voyage
        $hasBl = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->exists();

        $fileName = 'OB_' . str_replace(' ', '_', $namaKapal) . '_' . $noVoyage . '_' . date('Ymd_His') . '.xlsx';

        // Determine which data to use
        if ($kegiatan !== 'muat' && $hasBl) {
            // Use BL data
            $query = Bl::with(['prospek', 'supir'])
                ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage);

            // Apply filters
            if ($statusFilter) {
                if ($statusFilter === 'sudah') {
                    $query->where('sudah_ob', true);
                } elseif ($statusFilter === 'belum') {
                    $query->where('sudah_ob', false);
                }
            }

            if ($tipeFilter) {
                $query->where('tipe_kontainer', $tipeFilter);
            }

            if ($searchFilter) {
                $search = $searchFilter;
                $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                $query->where(function($q) use ($search, $searchNum) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%")
                      ->orWhere('nomor_bl', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('nomor_bl', 'asc')->get();

        } else {
            // Use naik_kapal data
            $query = NaikKapal::with(['prospek', 'supir'])
                ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage);

            // Apply filters
            if ($statusFilter) {
                if ($statusFilter === 'sudah') {
                    $query->where('sudah_ob', true);
                } elseif ($statusFilter === 'belum') {
                    $query->where('sudah_ob', false);
                }
            }

            if ($tipeFilter) {
                $query->where('tipe_kontainer', $tipeFilter);
            }

            // Dedicated nomor_kontainer filter
            if ($request->filled('nomor_kontainer')) {
                 $num = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $request->nomor_kontainer));
                 $query->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$num}%"]);
            }

            if ($searchFilter) {
                $search = $searchFilter;
                // Normalize nomor kontainer for better matches against formatted values
                $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                $query->where(function($q) use ($search, $searchNum) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('tanggal_muat', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Excel::download(new ObExport($data, $namaKapal, $noVoyage), $fileName);
    }

    /**
     * Save Asal Kontainer and Ke for multiple records
     */
    public function saveAsalKe(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->input('id');
            $type = $request->input('type');
            $asalKontainer = $request->input('asal_kontainer');
            $ke = $request->input('ke');
            
            if (!$id || !$type) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID dan tipe data harus diisi'
                ]);
            }

            $record = null;
            
            if ($type === 'bl') {
                $record = Bl::find($id);
            } elseif ($type === 'naik_kapal') {
                $record = NaikKapal::find($id);
            }

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // Check if location 'ke' changed to record in history
            $oldKe = $record->ke;
            
            $record->asal_kontainer = $asalKontainer;
            $record->ke = $ke;
            $record->save();

            // UPDATE STOCK KONTAINER LOCATION AND RECORD HISTORY
            if (!empty($ke)) {
                $gudang = \App\Models\Gudang::where('nama_gudang', $ke)->first();
                if ($gudang) {
                    $noKontainer = $record->nomor_kontainer;
                    
                    if ($noKontainer) {
                        // Find container to update location and get its type for history
                        $typeKontainer = null;
                        $knt = \App\Models\Kontainer::where('nomor_seri_gabungan', $noKontainer)->first();
                        
                        if ($knt) {
                            $typeKontainer = 'kontainer';
                            $knt->update(['gudangs_id' => $gudang->id]);
                        } else {
                            $knt = \App\Models\StockKontainer::where('nomor_seri_gabungan', $noKontainer)->first();
                            if ($knt) {
                                $typeKontainer = 'stock';
                                $knt->update(['gudangs_id' => $gudang->id]);
                            }
                        }
                        
                        // Record history if it's a new location or even if same as before to track the OB event
                        if ($knt) {
                            HistoryKontainer::create([
                                'nomor_kontainer' => $noKontainer,
                                'tipe_kontainer' => $typeKontainer,
                                'jenis_kegiatan' => 'Masuk',
                                'tanggal_kegiatan' => now(),
                                'gudang_id' => $gudang->id,
                                'keterangan' => 'Overbrengen dari Kapal: ' . ($record->nama_kapal ?? '-') . '. Voyage: ' . ($record->no_voyage ?? '-'),
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update Asal Kontainer and Ke for all records matching filters
     */
    public function saveAsalKeBulk(Request $request)
    {
        try {
            DB::beginTransaction();
            $bulkAsal = $request->input('bulk_asal_kontainer');
            $bulkKe = $request->input('bulk_ke');
            $namaKapal = $request->input('nama_kapal');
            $noVoyage = $request->input('no_voyage');
            $kegiatan = $request->input('kegiatan');
            
            // Validasi wajib nama_kapal dan no_voyage
            if (!$namaKapal || !$noVoyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama kapal dan nomor voyage harus diisi'
                ]);
            }
            
            if (!$bulkAsal && !$bulkKe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada nilai untuk diupdate'
                ]);
            }

            $normalizedKapal = $this->normalizeShipName($namaKapal);
            
            // Mirror logic from showOBData to select the table
            $hasBl = $kegiatan !== 'muat' && Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $noVoyage)
                ->exists();

            $updatedCount = 0;
            $containerNumbers = [];
            $gudang = null;

            if ($bulkKe) {
                $gudang = \App\Models\Gudang::where('nama_gudang', $bulkKe)->first();
            }

            if ($hasBl) {
                // Update BL table
                $query = Bl::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                    ->where('no_voyage', $noVoyage);

                // Apply filters (Matched with index logic)
                if ($request->filled('status_ob')) {
                    $query->where('sudah_ob', $request->input('status_ob') === 'sudah');
                }
                if ($request->filled('tipe_kontainer')) {
                    $query->where('tipe_kontainer', $request->input('tipe_kontainer'));
                }
                if ($request->filled('size_kontainer')) {
                    $query->where('size_kontainer', $request->input('size_kontainer'));
                }
                if ($request->filled('nomor_kontainer')) {
                    $num = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $request->nomor_kontainer));
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$num}%"]);
                }
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                    $query->where(function($q) use ($search, $searchNum) {
                        $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                          ->orWhere('no_seal', 'like', "%{$search}%")
                          ->orWhere('nama_barang', 'like', "%{$search}%");
                    });
                }

                $updateData = [];
                if ($bulkAsal) $updateData['asal_kontainer'] = $bulkAsal;
                if ($bulkKe) $updateData['ke'] = $bulkKe;

                // Get container numbers before update for location synchronization and history
                if ($bulkKe) {
                    $containerNumbers = (clone $query)->pluck('nomor_kontainer')->filter()->toArray();
                }

                $updatedCount = $query->update($updateData);

            } else {
                // Update NaikKapal table
                $query = NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                    ->where('no_voyage', $noVoyage);

                // Apply filters (Matched with index logic)
                if ($request->filled('status_ob')) {
                    $query->where('sudah_ob', $request->input('status_ob') === 'sudah');
                }
                if ($request->filled('tipe_kontainer')) {
                    $query->where('tipe_kontainer', $request->input('tipe_kontainer'));
                }
                if ($request->filled('size_kontainer')) {
                    $query->where('size_kontainer', $request->input('size_kontainer'));
                }
                if ($request->filled('nomor_kontainer')) {
                    $num = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $request->nomor_kontainer));
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$num}%"]);
                }
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $searchNum = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $search));
                    $query->where(function($q) use ($search, $searchNum) {
                        $q->whereRaw("REPLACE(REPLACE(REPLACE(UPPER(nomor_kontainer), ' ', ''), '-', ''), '.' , '') like ?", ["%{$searchNum}%"]) 
                          ->orWhere('no_seal', 'like', "%{$search}%")
                          ->orWhere('jenis_barang', 'like', "%{$search}%");
                    });
                }

                $updateData = [];
                if ($bulkAsal) $updateData['asal_kontainer'] = $bulkAsal;
                if ($bulkKe) $updateData['ke'] = $bulkKe;

                // Get container numbers before update for location synchronization and history
                if ($bulkKe) {
                    $containerNumbers = (clone $query)->pluck('nomor_kontainer')->filter()->toArray();
                }

                $updatedCount = $query->update($updateData);
            }

            // SYNC LOCATIONS AND RECORD HISTORY IN BULK
            if ($bulkKe && $gudang && !empty($containerNumbers)) {
                foreach ($containerNumbers as $noKontainer) {
                    $typeKontainer = null;
                    $knt = \App\Models\Kontainer::where('nomor_seri_gabungan', $noKontainer)->first();
                    
                    if ($knt) {
                        $typeKontainer = 'kontainer';
                        $knt->update(['gudangs_id' => $gudang->id]);
                    } else {
                        $knt = \App\Models\StockKontainer::where('nomor_seri_gabungan', $noKontainer)->first();
                        if ($knt) {
                            $typeKontainer = 'stock';
                            $knt->update(['gudangs_id' => $gudang->id]);
                        }
                    }
                    
                    if ($knt) {
                        HistoryKontainer::create([
                            'nomor_kontainer' => $noKontainer,
                            'tipe_kontainer' => $typeKontainer,
                            'jenis_kegiatan' => 'Masuk',
                            'tanggal_kegiatan' => now(),
                            'gudang_id' => $gudang->id,
                            'keterangan' => "Bulk Overbrengen. Kapal: {$namaKapal}. Voyage: {$noVoyage}",
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengupdate {$updatedCount} data",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor pranota otomatis
     * Format: XXX-MM-YY-000001
     * XXX = 3 digit kode (OB)
     * MM = 2 digit bulan
     * YY = 2 digit tahun
     * 000001 = 6 digit running number
     */
    public function generateNomorPranota(Request $request)
    {
        try {
            $kode = 'POB'; // Pranota OB
            $bulan = now()->format('m');
            $tahun = now()->format('y');
            
            // Get last pranota number for current month
            $prefix = "{$kode}-{$bulan}-{$tahun}-";
            $lastPranota = PranotaOb::where('nomor_pranota', 'like', $prefix . '%')
                ->orderBy('nomor_pranota', 'desc')
                ->first();
            
            if ($lastPranota) {
                // Extract running number from last pranota
                $lastNumber = (int) substr($lastPranota->nomor_pranota, -6);
                $runningNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                // First pranota for this month
                $runningNumber = '000001';
            }
            
            $nomorPranota = "{$prefix}{$runningNumber}";
            
            return response()->json([
                'success' => true,
                'nomor_pranota' => $nomorPranota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor pranota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process TL Bongkar (Tanda Langsung) - Only mark BL as OB without creating new records
     * Untuk kegiatan bongkar, TL hanya menandai sudah OB tanpa membuat record BL baru
     */
    public function processTLBongkar(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'bl_id' => 'required|integer|exists:bls,id'
            ]);

            $bl = Bl::findOrFail($request->bl_id);

            // Check if already processed
            if ($bl->sudah_ob) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kontainer ini sudah ditandai OB'
                ], 400);
            }

            // Check if already TL
            if ($bl->sudah_tl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kontainer ini sudah ditandai TL'
                ], 400);
            }

            DB::beginTransaction();

            // Mark BL as sudah OB and sudah TL (TL tidak perlu supir karena langsung dibongkar)
            $bl->sudah_ob = true;
            $bl->sudah_tl = true;
            $bl->supir_id = null;
            $bl->tanggal_ob = now();
            $bl->catatan_ob = 'Proses TL Bongkar (Tanda Langsung) - Langsung Dibongkar';
            $bl->save();

            // If BL is linked to a Prospek, update its status to 'sudah_muat'
            try {
                if ($bl->prospek_id) {
                    $prospek = Prospek::find($bl->prospek_id);
                    if ($prospek && $prospek->status !== Prospek::STATUS_SUDAH_MUAT) {
                        $prospek->status = Prospek::STATUS_SUDAH_MUAT;
                        $prospek->tanggal_muat = now();
                        $prospek->updated_by = Auth::id() ?? $prospek->updated_by;
                        $prospek->save();
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to update Prospek status for BL (processTLBongkar) ID ' . ($bl->id ?? 'unknown') . ': ' . $e->getMessage());
                // don't break TL processing for Prospek update failure
            }

            // Create Manifest if not exists (User Request)
            $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $bl->nomor_kontainer)
                ->where('no_voyage', $bl->no_voyage)
                ->where('nama_kapal', $bl->nama_kapal)
                ->first();

            if (!$existingManifest) {
                $manifestData = [
                    'nomor_bl' => $bl->nomor_bl,
                    'nomor_kontainer' => $bl->nomor_kontainer,
                    'no_seal' => $bl->no_seal,
                    'tipe_kontainer' => $bl->tipe_kontainer,
                    'size_kontainer' => $bl->size_kontainer,
                    'nama_kapal' => $bl->nama_kapal,
                    'no_voyage' => $bl->no_voyage,
                    'pelabuhan_asal' => $bl->pelabuhan_asal,
                    'pelabuhan_tujuan' => $bl->pelabuhan_tujuan,
                    'nama_barang' => $bl->nama_barang,
                    'asal_kontainer' => $bl->asal_kontainer,
                    'ke' => $bl->ke,
                    'tonnage' => $bl->tonnage,
                    'volume' => $bl->volume,
                    'kuantitas' => $bl->kuantitas ?? 1,
                    'prospek_id' => $bl->prospek_id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'pengirim' => $bl->pengirim,
                    'penerima' => $bl->penerima,
                    'alamat_pengiriman' => $bl->alamat_pengiriman,
                    'contact_person' => $bl->contact_person,
                    'term' => $bl->term,
                    'nomor_tanda_terima' => null // BL doesn't have this usually
                ];

                // Get additional data from prospek if available
                if ($bl->prospek_id) {
                    $prospek = \App\Models\Prospek::find($bl->prospek_id);
                    if ($prospek) {
                        $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
                        $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
                        $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $bl->pelabuhan_asal ?? null;
                        $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $bl->pelabuhan_tujuan ?? null;
                    }
                }

                \App\Models\Manifest::create($manifestData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil proses TL Bongkar kontainer ' . $bl->nomor_kontainer
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in processTLBongkar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal proses TL Bongkar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process TL (Tanda Langsung) - Copy naik_kapal to BL and mark as OB
     */
    public function processTL(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'naik_kapal_id' => 'required|integer|exists:naik_kapal,id'
            ]);

            $naikKapal = NaikKapal::findOrFail($request->naik_kapal_id);

            // Check if already processed
            if ($naikKapal->sudah_ob) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kontainer ini sudah ditandai OB'
                ], 400);
            }

            DB::beginTransaction();

            // Check if CARGO container (always create new BL, no dedup)
            $isCargoContainer = (
                strtoupper(trim($naikKapal->tipe_kontainer ?? '')) === 'CARGO' ||
                stripos($naikKapal->nomor_kontainer ?? '', 'CARGO') !== false
            );

            // Check if BL already exists to avoid duplication (FCL/LCL only, not CARGO)
            $bl = null;
            if (!$isCargoContainer) {
                $bl = Bl::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                    ->where('no_voyage', $naikKapal->no_voyage)
                    ->where('nama_kapal', $naikKapal->nama_kapal)
                    ->first();
            }

            if (!$bl) {
                // Create BL record from naik_kapal
                $bl = new Bl();
                $bl->nomor_kontainer = $naikKapal->nomor_kontainer;
                $bl->no_seal = $naikKapal->no_seal;
                $bl->nama_barang = $naikKapal->jenis_barang;
                $bl->tipe_kontainer = $naikKapal->tipe_kontainer;
                $bl->size_kontainer = $naikKapal->size_kontainer;
                $bl->nama_kapal = $naikKapal->nama_kapal;
                $bl->no_voyage = $naikKapal->no_voyage;
                $bl->pelabuhan_asal = $naikKapal->pelabuhan_asal;
                $bl->pelabuhan_tujuan = $naikKapal->pelabuhan_tujuan;
                $bl->asal_kontainer = $naikKapal->asal_kontainer ?? null;
                $bl->ke = $naikKapal->ke ?? null;
                
                // Link BL back to Prospek (if naik_kapal has prospek_id)
                if ($naikKapal->prospek_id) {
                    $bl->prospek_id = $naikKapal->prospek_id;
                }
            } else {
                // Update existing BL with latest data from NaikKapal if needed
                $bl->asal_kontainer = $naikKapal->asal_kontainer ?? $bl->asal_kontainer;
                $bl->ke = $naikKapal->ke ?? $bl->ke;
            }
            
            // Mark as sudah OB (TL tidak perlu supir karena langsung dimuat)
            $bl->sudah_ob = true;
            $bl->supir_id = null;
            $bl->tanggal_ob = now();
            $bl->catatan_ob = 'Proses TL (Tanda Langsung) - Langsung Dimuat';

            // Mark BL as TL as well to keep status consistent
            $bl->sudah_tl = true;

            $bl->save();

            // Update naik_kapal status
            $naikKapal->sudah_ob = true;
            $naikKapal->supir_id = null;
            $naikKapal->tanggal_ob = now();
            $naikKapal->catatan_ob = 'Proses TL (Tanda Langsung) - Langsung Dimuat';
            $naikKapal->is_tl = true;
            $naikKapal->save();

            // If NaikKapal is linked to a Prospek, update its status to 'sudah_muat'
            try {
                if ($naikKapal->prospek_id) {
                    $prospek = Prospek::find($naikKapal->prospek_id);
                    if ($prospek && $prospek->status !== Prospek::STATUS_SUDAH_MUAT) {
                        $prospek->status = Prospek::STATUS_SUDAH_MUAT;
                        $prospek->tanggal_muat = now();
                        $prospek->updated_by = Auth::id() ?? $prospek->updated_by;
                        $prospek->save();
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to update Prospek status for NaikKapal (processTL) ID ' . ($naikKapal->id ?? 'unknown') . ': ' . $e->getMessage());
                // continue without breaking TL
            }

            // === MANIFEST CREATION LOGIC ===
            // Cek apakah kontainer LCL
            if (strtoupper(trim($naikKapal->tipe_kontainer)) === 'LCL') {
                \Log::info("LCL container detected in processTL, finding tanda terima...");
                
                // Cari semua tanda terima yang terhubung dengan kontainer ini
                $tandaTerimaRecords = \App\Models\TandaTerimaLclKontainerPivot::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                    ->with('tandaTerima.items')
                    ->get();
                
                if ($tandaTerimaRecords->count() > 0) {
                    \Log::info("Found " . $tandaTerimaRecords->count() . " tanda terima for this LCL container in processTL");
                    
                    foreach ($tandaTerimaRecords as $pivot) {
                        $tandaTerima = $pivot->tandaTerima;
                        if (!$tandaTerima) continue;
                        
                        // Cek duplikasi manifest
                        $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                            ->where('no_voyage', $naikKapal->no_voyage)
                            ->where('nama_kapal', $naikKapal->nama_kapal)
                            ->where('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima)
                            ->first();
                            
                        if ($existingManifest) {
                            continue;
                        }
                        
                        // Buat manifest untuk setiap tanda terima
                        $manifest = new \App\Models\Manifest();
                        
                        // Data kontainer
                        $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
                        $manifest->no_seal = $pivot->nomor_seal ?? $naikKapal->no_seal;
                        $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
                        $manifest->size_kontainer = $naikKapal->size_kontainer;
                        
                        // Data kapal & voyage
                        $manifest->nama_kapal = $naikKapal->nama_kapal;
                        $manifest->no_voyage = $naikKapal->no_voyage;
                        
                        // Data dari tanda terima
                        $manifest->nomor_tanda_terima = $tandaTerima->nomor_tanda_terima;
                        $manifest->pengirim = $tandaTerima->nama_pengirim;
                        $manifest->penerima = $tandaTerima->penerima;
                        $manifest->alamat_pengirim = $tandaTerima->alamat_pengirim;
                        $manifest->alamat_penerima = $tandaTerima->alamat_penerima;
                        
                        // Nama barang dari items
                        $namaBarang = $tandaTerima->items->pluck('nama_barang')->filter()->implode(', ');
                        $manifest->nama_barang = $namaBarang ?: $naikKapal->jenis_barang;
                        
                        // Volume dan tonnage dari items
                        $manifest->volume = $tandaTerima->items->sum('meter_kubik');
                        $manifest->tonnage = $tandaTerima->items->sum('tonase');
                        
                        // Pelabuhan
                        $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
                        $manifest->pelabuhan_bongkar = $naikKapal->ke;
                        
                        // Tanggal
                        $manifest->tanggal_berangkat = now();
                        $manifest->penerimaan = $tandaTerima->tanggal_tanda_terima;
                        
                        // Generate nomor manifest
                        $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')
                            ->orderBy('id', 'desc')
                            ->first();
                        
                        if ($lastManifest && $lastManifest->nomor_bl) {
                            preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                            $manifest->nomor_bl = 'MNF-' . $nextNumber;
                        } else {
                            $manifest->nomor_bl = 'MNF-000001';
                        }
                        
                        // Referensi
                        if ($naikKapal->prospek_id) {
                            $manifest->prospek_id = $naikKapal->prospek_id;
                        }
                        
                        // Audit
                        $manifest->created_by = $user->id;
                        $manifest->updated_by = $user->id;
                        
                        $manifest->save();
                    }
                } else {
                    // Fallback: No Tanda Terima found for LCL, create single manifest
                    $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                        ->where('no_voyage', $naikKapal->no_voyage)
                        ->where('nama_kapal', $naikKapal->nama_kapal)
                        ->first();

                    if (!$existingManifest) {
                        $manifestData = [
                            'nomor_kontainer' => $naikKapal->nomor_kontainer,
                            'no_seal' => $naikKapal->no_seal,
                            'tipe_kontainer' => $naikKapal->tipe_kontainer,
                            'size_kontainer' => $naikKapal->size_kontainer,
                            'nama_kapal' => $naikKapal->nama_kapal,
                            'no_voyage' => $naikKapal->no_voyage,
                            'pelabuhan_asal' => $naikKapal->pelabuhan_asal,
                            'pelabuhan_tujuan' => $naikKapal->pelabuhan_tujuan,
                            'nama_barang' => $naikKapal->jenis_barang,
                            'asal_kontainer' => $naikKapal->asal_kontainer,
                            'ke' => $naikKapal->ke,
                            'tonnage' => $naikKapal->total_tonase,
                            'volume' => $naikKapal->total_volume,
                            'kuantitas' => $naikKapal->kuantitas ?? 1,
                            'prospek_id' => $naikKapal->prospek_id,
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                            'tanggal_berangkat' => now(),
                        ];

                        // Get additional data from prospek if available
                        if ($naikKapal->prospek_id) {
                            $prospek = \App\Models\Prospek::find($naikKapal->prospek_id);
                            if ($prospek) {
                                $manifestData['pengirim'] = $prospek->pt_pengirim ?? $prospek->pengirim ?? null;
                                $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
                                $manifestData['penerima'] = $prospek->pt_penerima ?? $prospek->penerima ?? null;
                                $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
                                $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $naikKapal->pelabuhan_asal ?? null;
                                $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $naikKapal->pelabuhan_tujuan ?? null;
                            }
                        }

                        // Generate nomor manifest
                        $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')
                            ->orderBy('id', 'desc')
                            ->first();

                        if ($lastManifest && $lastManifest->nomor_bl) {
                            preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                            $manifestData['nomor_bl'] = 'MNF-' . $nextNumber;
                        } else {
                            $manifestData['nomor_bl'] = 'MNF-000001';
                        }

                        \App\Models\Manifest::create($manifestData);
                    }
                }
            } else {
                // Not LCL - FCL atau CARGO
                // Untuk CARGO izinkan duplikat (nomor_kontainer selalu 'CARGO' tapi barang berbeda)
                $isCargoTL = (
                    strtoupper(trim($naikKapal->tipe_kontainer ?? '')) === 'CARGO' ||
                    stripos($naikKapal->nomor_kontainer ?? '', 'CARGO') !== false
                );

                // Cek duplikat HANYA untuk FCL (bukan CARGO)
                $existingManifest = null;
                if (!$isCargoTL) {
                    $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                        ->where('no_voyage', $naikKapal->no_voyage)
                        ->where('nama_kapal', $naikKapal->nama_kapal)
                        ->first();
                }

                if ($isCargoTL || !$existingManifest) {
                    $manifestData = [
                        'nomor_kontainer' => $naikKapal->nomor_kontainer,
                        'no_seal' => $naikKapal->no_seal,
                        'tipe_kontainer' => $naikKapal->tipe_kontainer,
                        'size_kontainer' => $naikKapal->size_kontainer,
                        'nama_kapal' => $naikKapal->nama_kapal,
                        'no_voyage' => $naikKapal->no_voyage,
                        'pelabuhan_asal' => $naikKapal->pelabuhan_asal,
                        'pelabuhan_tujuan' => $naikKapal->pelabuhan_tujuan,
                        'nama_barang' => $naikKapal->jenis_barang,
                        'asal_kontainer' => $naikKapal->asal_kontainer,
                        'ke' => $naikKapal->ke,
                        'tonnage' => $naikKapal->total_tonase,
                        'volume' => $naikKapal->total_volume,
                        'kuantitas' => $naikKapal->kuantitas ?? 1,
                        'prospek_id' => $naikKapal->prospek_id,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                        'tanggal_berangkat' => now(),
                    ];

                    // Get additional data from prospek if available
                    if ($naikKapal->prospek_id) {
                        $prospek = \App\Models\Prospek::find($naikKapal->prospek_id);
                        if ($prospek) {
                            $manifestData['pengirim'] = $prospek->pt_pengirim ?? $prospek->pengirim ?? null;
                            $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
                            $manifestData['penerima'] = $prospek->pt_penerima ?? $prospek->penerima ?? null;
                            $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
                            $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $naikKapal->pelabuhan_asal ?? null;
                            $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $naikKapal->pelabuhan_tujuan ?? null;
                        }
                    }

                    // Generate nomor manifest
                    $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($lastManifest && $lastManifest->nomor_bl) {
                        preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                        $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                        $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                        $manifestData['nomor_bl'] = 'MNF-' . $nextNumber;
                    } else {
                        $manifestData['nomor_bl'] = 'MNF-000001';
                    }

                    \App\Models\Manifest::create($manifestData);
                    \Log::info("✅ Created manifest in processTL for " . ($isCargoTL ? "CARGO" : "FCL"), [
                        'nomor_kontainer' => $naikKapal->nomor_kontainer,
                        'nama_barang' => $naikKapal->jenis_barang,
                    ]);
                } else {
                    \Log::info("ℹ️ Skipping manifest in processTL (FCL already exists)", [
                        'nomor_kontainer' => $naikKapal->nomor_kontainer,
                    ]);
                }
            }



            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil proses TL kontainer ' . $naikKapal->nomor_kontainer
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in processTL: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal proses TL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview kontainer yang akan diupdate sizenya
     */
    public function previewUpdateSize(Request $request)
    {
        try {
            $namaKapal = $request->nama_kapal;
            $noVoyage = $request->no_voyage;

            if (!$namaKapal || !$noVoyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama kapal dan nomor voyage harus diisi'
                ], 400);
            }

            // Normalize ship name
            $normalizedKapal = $this->normalizeShipName($namaKapal);

            $updates = [];

            // Check BL records
            $bls = Bl::where(function($query) use ($namaKapal, $normalizedKapal) {
                $query->where('nama_kapal', $namaKapal)
                    ->orWhereRaw('UPPER(REPLACE(nama_kapal, ".", "")) = ?', [$normalizedKapal]);
            })
            ->where('no_voyage', $noVoyage)
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '!=', '')
            ->get();

            foreach ($bls as $bl) {
                $nomorKontainer = $this->normalizeContainerNumber($bl->nomor_kontainer);
                
                // Skip CARGO containers
                if ($bl->tipe_kontainer === 'CARGO' || stripos($nomorKontainer, 'CARGO') !== false) {
                    continue;
                }

                // Try to find size from kontainers table first
                $kontainer = DB::table('kontainers')
                    ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                    ->first();

                $sumber = null;
                $sizeBaru = null;

                if ($kontainer && $kontainer->ukuran) {
                    $sizeBaru = $kontainer->ukuran;
                    $sumber = 'kontainers';
                } else {
                    // Try stock_kontainers table
                    $stockKontainer = DB::table('stock_kontainers')
                        ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                        ->first();

                    if ($stockKontainer && $stockKontainer->ukuran) {
                        $sizeBaru = $stockKontainer->ukuran;
                        $sumber = 'stock_kontainers';
                    }
                }

                // Add to updates if size is different or empty
                if ($sizeBaru && ($bl->size_kontainer != $sizeBaru || empty($bl->size_kontainer))) {
                    $updates[] = [
                        'record_type' => 'bl',
                        'record_id' => $bl->id,
                        'nomor_kontainer' => $bl->nomor_kontainer,
                        'size_sekarang' => $bl->size_kontainer,
                        'size_baru' => $sizeBaru,
                        'sumber' => $sumber
                    ];
                }
            }

            // Check NaikKapal records
            $naikKapals = NaikKapal::where(function($query) use ($namaKapal, $normalizedKapal) {
                $query->where('nama_kapal', $namaKapal)
                    ->orWhereRaw('UPPER(REPLACE(nama_kapal, ".", "")) = ?', [$normalizedKapal]);
            })
            ->where('no_voyage', $noVoyage)
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '!=', '')
            ->get();

            foreach ($naikKapals as $naikKapal) {
                $nomorKontainer = $this->normalizeContainerNumber($naikKapal->nomor_kontainer);
                
                // Skip CARGO containers
                if ($naikKapal->tipe_kontainer === 'CARGO' || stripos($nomorKontainer, 'CARGO') !== false) {
                    continue;
                }

                // Try to find size from kontainers table first
                $kontainer = DB::table('kontainers')
                    ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                    ->first();

                $sumber = null;
                $sizeBaru = null;

                if ($kontainer && $kontainer->ukuran) {
                    $sizeBaru = $kontainer->ukuran;
                    $sumber = 'kontainers';
                } else {
                    // Try stock_kontainers table
                    $stockKontainer = DB::table('stock_kontainers')
                        ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                        ->first();

                    if ($stockKontainer && $stockKontainer->ukuran) {
                        $sizeBaru = $stockKontainer->ukuran;
                        $sumber = 'stock_kontainers';
                    }
                }

                // Add to updates if size is different or empty
                if ($sizeBaru && ($naikKapal->size_kontainer != $sizeBaru || empty($naikKapal->size_kontainer))) {
                    $updates[] = [
                        'record_type' => 'naik_kapal',
                        'record_id' => $naikKapal->id,
                        'nomor_kontainer' => $naikKapal->nomor_kontainer,
                        'size_sekarang' => $naikKapal->size_kontainer,
                        'size_baru' => $sizeBaru,
                        'sumber' => $sumber
                    ];
                }
            }

            $totalKontainer = $bls->count() + $naikKapals->count();

            return response()->json([
                'success' => true,
                'updates' => $updates,
                'total_kontainer' => $totalKontainer
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in previewUpdateSize: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalize container number for comparison
     */
    private function normalizeContainerNumber($number)
    {
        return strtoupper(str_replace([' ', '-', '.'], '', $number));
    }

    /**
     * Confirm dan execute update size kontainer
     */
    public function confirmUpdateSize(Request $request)
    {
        try {
            $namaKapal = $request->nama_kapal;
            $noVoyage = $request->no_voyage;

            if (!$namaKapal || !$noVoyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama kapal dan nomor voyage harus diisi'
                ], 400);
            }

            // Normalize ship name
            $normalizedKapal = $this->normalizeShipName($namaKapal);

            DB::beginTransaction();

            $updatedBl = 0;
            $updatedNaikKapal = 0;

            // Update BL records
            $bls = Bl::where(function($query) use ($namaKapal, $normalizedKapal) {
                $query->where('nama_kapal', $namaKapal)
                    ->orWhereRaw('UPPER(REPLACE(nama_kapal, ".", "")) = ?', [$normalizedKapal]);
            })
            ->where('no_voyage', $noVoyage)
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '!=', '')
            ->get();

            foreach ($bls as $bl) {
                $nomorKontainer = $this->normalizeContainerNumber($bl->nomor_kontainer);
                
                // Skip CARGO containers
                if ($bl->tipe_kontainer === 'CARGO' || stripos($nomorKontainer, 'CARGO') !== false) {
                    continue;
                }

                // Try to find size from kontainers table first
                $kontainer = DB::table('kontainers')
                    ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                    ->first();

                $sizeBaru = null;

                if ($kontainer && $kontainer->ukuran) {
                    $sizeBaru = $kontainer->ukuran;
                } else {
                    // Try stock_kontainers table
                    $stockKontainer = DB::table('stock_kontainers')
                        ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                        ->first();

                    if ($stockKontainer && $stockKontainer->ukuran) {
                        $sizeBaru = $stockKontainer->ukuran;
                    }
                }

                // Update if size is different or empty
                if ($sizeBaru && ($bl->size_kontainer != $sizeBaru || empty($bl->size_kontainer))) {
                    $bl->size_kontainer = $sizeBaru;
                    $bl->save();
                    $updatedBl++;
                }
            }

            // Update NaikKapal records
            $naikKapals = NaikKapal::where(function($query) use ($namaKapal, $normalizedKapal) {
                $query->where('nama_kapal', $namaKapal)
                    ->orWhereRaw('UPPER(REPLACE(nama_kapal, ".", "")) = ?', [$normalizedKapal]);
            })
            ->where('no_voyage', $noVoyage)
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '!=', '')
            ->get();

            foreach ($naikKapals as $naikKapal) {
                $nomorKontainer = $this->normalizeContainerNumber($naikKapal->nomor_kontainer);
                
                // Skip CARGO containers
                if ($naikKapal->tipe_kontainer === 'CARGO' || stripos($nomorKontainer, 'CARGO') !== false) {
                    continue;
                }

                // Try to find size from kontainers table first
                $kontainer = DB::table('kontainers')
                    ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                    ->first();

                $sizeBaru = null;

                if ($kontainer && $kontainer->ukuran) {
                    $sizeBaru = $kontainer->ukuran;
                } else {
                    // Try stock_kontainers table
                    $stockKontainer = DB::table('stock_kontainers')
                        ->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(nomor_seri_gabungan, " ", ""), "-", ""), ".", "")) = ?', [$nomorKontainer])
                        ->first();

                    if ($stockKontainer && $stockKontainer->ukuran) {
                        $sizeBaru = $stockKontainer->ukuran;
                    }
                }

                // Update if size is different or empty
                if ($sizeBaru && ($naikKapal->size_kontainer != $sizeBaru || empty($naikKapal->size_kontainer))) {
                    $naikKapal->size_kontainer = $sizeBaru;
                    $naikKapal->save();
                    $updatedNaikKapal++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate size kontainer',
                'updated_count' => $updatedBl + $updatedNaikKapal,
                'updated_bl' => $updatedBl,
                'updated_naik_kapal' => $updatedNaikKapal
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in confirmUpdateSize: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate size: ' . $e->getMessage()
            ], 500);
        }
    }
}




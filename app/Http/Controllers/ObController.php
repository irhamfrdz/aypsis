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
use Carbon\Carbon;

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
                    if (str_contains($lowerName, 'empty container') || 
                        str_contains($lowerName, 'container empty') ||
                        $lowerName === 'empty' ||
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
                if (str_contains($lowerName, 'empty container') || 
                    str_contains($lowerName, 'container empty') ||
                    $lowerName === 'empty' ||
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
                ->distinct()
                ->orderBy('nama_kapal', 'asc')
                ->pluck('nama_kapal')
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
                ->distinct()
                ->orderBy('nama_kapal', 'asc')
                ->pluck('nama_kapal')
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
                'catatan' => 'nullable|string'
            ]);

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
            $naikKapal->save();
            \Log::info("Updated naik_kapal OB status");

            // Otomatis buat record di BLS untuk kegiatan muat
            // Cek dulu apakah sudah ada BL dengan nomor kontainer dan voyage yang sama
            \Log::info("Checking existing BL...");
            $existingBl = Bl::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                ->where('no_voyage', $naikKapal->no_voyage)
                ->where('nama_kapal', $naikKapal->nama_kapal)
                ->first();

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
                    $bl->penerima = $prospek->tujuan_pengiriman;
                    
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
                    
                    // Jika ada tanda terima terkait, coba ambil data dari sana
                    if (empty($bl->alamat_pengiriman) && $prospek->tanda_terima_id) {
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
                $existingBl->save();
                
                \Log::info("✅ SUCCESS: Updated existing BL record OB status");
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

            \Log::info("===== END markAsOB SUCCESS =====");
            
            return response()->json([
                'success' => true,
                'message' => 'Kontainer berhasil ditandai sudah OB, data BL dan status prospek telah diupdate'
            ]);
        } catch (\Exception $e) {
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
                'catatan' => 'nullable|string'
            ]);

            $bl = Bl::findOrFail($request->bl_id);
            
            // Update status OB
            $bl->sudah_ob = true;
            $bl->supir_id = $request->supir_id;
            $bl->tanggal_ob = now();
            $bl->catatan_ob = $request->catatan;
            $bl->updated_by = $user->id;
            $bl->save();

            return response()->json([
                'success' => true,
                'message' => 'BL berhasil ditandai sudah OB'
            ]);
        } catch (\Exception $e) {
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

            return response()->json([
                'success' => true,
                'message' => 'Status OB kontainer berhasil dibatalkan'
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
                            // Fallback to request status or default
                            $itemsToSave[$idx]['status'] = $it['status'] ?? 'full';
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
                            // Fallback to request status or default
                            $itemsToSave[$idx]['status'] = $it['status'] ?? 'full';
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
     * Save Asal Kontainer and Ke for multiple records
     */
    public function saveAsalKe(Request $request)
    {
        try {
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

            $record->asal_kontainer = $asalKontainer;
            $record->ke = $ke;
            $record->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data'
            ]);

        } catch (\Exception $e) {
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

            $updatedCount = 0;

            // Determine which table to update based on kegiatan
            if ($kegiatan === 'bongkar' || $request->has('nomor_bl')) {
                // Update BL table
                $query = Bl::where('nama_kapal', $namaKapal)
                    ->where('no_voyage', $noVoyage);

                // Apply filters
                if ($request->filled('status_ob')) {
                    $query->where('sudah_ob', $request->input('status_ob') === 'sudah');
                }
                if ($request->filled('tipe_kontainer')) {
                    $query->where('tipe_kontainer', $request->input('tipe_kontainer'));
                }
                if ($request->filled('size_kontainer')) {
                    $query->where('size_kontainer', $request->input('size_kontainer'));
                }
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('nomor_kontainer', 'like', "%{$search}%")
                          ->orWhere('no_seal', 'like', "%{$search}%")
                          ->orWhere('nama_barang', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('nomor_kontainer')) {
                    $query->where('nomor_kontainer', 'like', '%' . $request->input('nomor_kontainer') . '%');
                }

                $updateData = [];
                if ($bulkAsal) $updateData['asal_kontainer'] = $bulkAsal;
                if ($bulkKe) $updateData['ke'] = $bulkKe;

                $updatedCount = $query->update($updateData);
            } else {
                // Update NaikKapal table
                $query = NaikKapal::where('nama_kapal', $namaKapal)
                    ->where('no_voyage', $noVoyage);

                // Apply filters
                if ($request->filled('status_ob')) {
                    $query->where('sudah_ob', $request->input('status_ob') === 'sudah');
                }
                if ($request->filled('tipe_kontainer')) {
                    $query->where('tipe_kontainer', $request->input('tipe_kontainer'));
                }
                if ($request->filled('size_kontainer')) {
                    $query->where('size_kontainer', $request->input('size_kontainer'));
                }
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $query->where(function($q) use ($search) {
                        $q->where('nomor_kontainer', 'like', "%{$search}%")
                          ->orWhere('no_seal', 'like', "%{$search}%")
                          ->orWhere('jenis_barang', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('nomor_kontainer')) {
                    $query->where('nomor_kontainer', 'like', '%' . $request->input('nomor_kontainer') . '%');
                }

                $updateData = [];
                if ($bulkAsal) $updateData['asal_kontainer'] = $bulkAsal;
                if ($bulkKe) $updateData['ke'] = $bulkKe;

                $updatedCount = $query->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengupdate {$updatedCount} data",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
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
            
            // Mark as sudah OB (TL tidak perlu supir karena langsung dimuat)
            $bl->sudah_ob = true;
            $bl->supir_id = null;
            $bl->tanggal_ob = now();
            $bl->catatan_ob = 'Proses TL (Tanda Langsung) - Langsung Dimuat';

            // Mark BL as TL as well to keep status consistent
            $bl->sudah_tl = true;

            // Link BL back to Prospek (if naik_kapal has prospek_id)
            if ($naikKapal->prospek_id) {
                $bl->prospek_id = $naikKapal->prospek_id;
            }

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
}




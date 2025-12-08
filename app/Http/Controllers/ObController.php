<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PergerakanKapal;
use App\Models\NaikKapal;
use App\Models\Bl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        if ($namaKapal && $noVoyage) {
            // Show OB data table with naik_kapal data
            return $this->showOBData($request, $namaKapal, $noVoyage);
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
     * Display OB data table for selected ship and voyage
     */
    private function showOBData(Request $request, $namaKapal, $noVoyage)
    {
        // Check if we have BL records for this ship/voyage
        $hasBl = Bl::where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage)
            ->exists();

        // If BL exists for this combination, prefer displaying BL records
        if ($hasBl) {
            $queryBl = Bl::with(['prospek', 'supir'])
                ->where('nama_kapal', $namaKapal)
                ->where('no_voyage', $noVoyage);

            $perPage = $request->get('per_page', 15);
            $bls = $queryBl->orderBy('nomor_bl', 'asc')
                ->paginate($perPage)
                ->withQueryString();

            $totalKontainer = Bl::where('nama_kapal', $namaKapal)
                ->where('no_voyage', $noVoyage)
                ->count();

            $sudahOB = Bl::where('nama_kapal', $namaKapal)
                ->where('no_voyage', $noVoyage)
                ->where('sudah_ob', true)
                ->count();

            $belumOB = $totalKontainer - $sudahOB;

            // Get list of supir (drivers) from karyawan table
            $supirs = \App\Models\Karyawan::where('divisi', 'supir')
                ->whereNull('tanggal_berhenti')
                ->orderBy('nama_panggilan')
                ->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);

            return view('ob.index', compact(
                'bls',
                'namaKapal',
                'noVoyage',
                'totalKontainer',
                'sudahOB',
                'belumOB',
                'supirs'
            ));
        }

        // Default: Get naik_kapal data for the selected ship and voyage
        $query = NaikKapal::with(['prospek', 'createdBy', 'updatedBy', 'supir'])
            ->where('nama_kapal', $namaKapal)
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $naikKapals = $query->orderBy('tanggal_muat', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Statistics
        $totalKontainer = NaikKapal::where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage)
            ->count();

        $sudahOB = NaikKapal::where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage)
            ->where('sudah_ob', true)
            ->count();

        $belumOB = $totalKontainer - $sudahOB;

        // Get list of supir (drivers) from karyawan table
        $supirs = \App\Models\Karyawan::where('divisi', 'supir')
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_panggilan')
            ->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);

        return view('ob.index', compact(
            'naikKapals', 
            'namaKapal', 
            'noVoyage', 
            'totalKontainer', 
            'sudahOB', 
            'belumOB',
            'supirs'
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
            $request->validate([
                'naik_kapal_id' => 'required|exists:naik_kapal,id',
                'supir_id' => 'required|exists:karyawans,id',
                'catatan' => 'nullable|string'
            ]);

            $naikKapal = NaikKapal::findOrFail($request->naik_kapal_id);
            
            // Update status OB
            $naikKapal->sudah_ob = true;
            $naikKapal->supir_id = $request->supir_id;
            $naikKapal->tanggal_ob = now();
            $naikKapal->catatan_ob = $request->catatan;
            $naikKapal->updated_by = $user->id;
            $naikKapal->save();

            return response()->json([
                'success' => true,
                'message' => 'Kontainer berhasil ditandai sudah OB'
            ]);
        } catch (\Exception $e) {
            \Log::error('Mark as OB error: ' . $e->getMessage());
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
}



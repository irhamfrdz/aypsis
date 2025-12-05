<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PergerakanKapal;
use App\Models\NaikKapal;
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
        // Get list of ships from naik_kapal table (distinct ship names)
        $ships = NaikKapal::select('nama_kapal')
            ->distinct()
            ->orderBy('nama_kapal', 'asc')
            ->get();

        return view('ob.select', compact('ships'));
    }

    /**
     * Display OB data table for selected ship and voyage
     */
    private function showOBData(Request $request, $namaKapal, $noVoyage)
    {
        // Get naik_kapal data for the selected ship and voyage
        $query = NaikKapal::with(['prospek', 'createdBy', 'updatedBy'])
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

        return view('ob.index', compact(
            'naikKapals', 
            'namaKapal', 
            'noVoyage', 
            'totalKontainer', 
            'sudahOB', 
            'belumOB'
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
            // Get voyages for the selected ship from naik_kapal
            // We group by no_voyage and order by latest tanggal_muat per voyage to avoid SQL strict mode errors
            $kapalClean = strtolower(str_replace('.', '', $namaKapal));

            $voyages = NaikKapal::select('no_voyage')
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
}


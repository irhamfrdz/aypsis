<?php

namespace App\Http\Controllers;

use App\Models\Bl;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of BL records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check permission (you may want to adjust this based on your permission system)
        if (!in_array($user->role, ["admin", "user_admin"])) {
            // Check specific permissions if needed
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk melihat data BL");
            }
        }

        $query = Bl::with('prospek');

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_voyage', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kapal
        if ($request->filled('kapal')) {
            $query->where('nama_kapal', 'like', "%{$request->kapal}%");
        }
        
        // Filter berdasarkan nama_kapal (dari select page)
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', 'like', "%{$request->nama_kapal}%");
        }

        // Filter berdasarkan voyage
        if ($request->filled('voyage')) {
            $query->where('no_voyage', $request->voyage);
        }
        
        // Filter berdasarkan no_voyage (dari select page)
        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->no_voyage);
        }

        // Sort berdasarkan parameter
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'nomor_bl', 'nomor_kontainer', 'nama_kapal', 'no_voyage', 'nama_barang'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $bls = $query->paginate(15)->withQueryString();

        return view('bl.index', compact('bls'));
    }

    /**
     * Show the form for selecting kapal and voyage.
     */
    public function select()
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk membuat BL");
            }
        }

        $masterKapals = MasterKapal::orderBy('nama_kapal')->get();
        return view('bl.select', compact('masterKapals'));
    }

    /**
     * Store a newly created BL.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-create")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk membuat BL");
            }
        }

        // Validate input
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'no_voyage' => 'required|string|max:50',
        ]);

        // For now just return confirmation
        // Later this can be extended to show a form for creating BL details
        $masterKapal = MasterKapal::find($request->kapal_id);
        
        return redirect()->route('bl.index')
            ->with('success', "BL request received for kapal {$masterKapal->nama_kapal} voyage {$request->no_voyage}");
    }

    /**
     * Display the specified BL.
     */
    public function show(Bl $bl)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk melihat detail BL");
            }
        }

        $bl->load('prospek');
        return view('bl.show', compact('bl'));
    }

    /**
     * Update the nomor_bl field for a BL record.
     */
    public function updateNomorBl(Request $request, Bl $bl)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-edit")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengupdate BL'], 403);
            }
        }

        // Validate input
        $request->validate([
            'nomor_bl' => 'nullable|string|max:255',
        ]);

        try {
            $bl->update([
                'nomor_bl' => $request->nomor_bl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor BL berhasil diupdate',
                'nomor_bl' => $bl->nomor_bl ?: '-'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nomor BL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get BL data by kapal and voyage (API endpoint)
     */
    public function getByKapalVoyage(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        $bls = Bl::with('prospek')
            ->where('nama_kapal', $request->nama_kapal)
            ->where('no_voyage', $request->no_voyage)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bls,
            'count' => $bls->count()
        ]);
    }
}
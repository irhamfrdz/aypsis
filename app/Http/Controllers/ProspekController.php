<?php

namespace App\Http\Controllers;

use App\Models\Prospek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProspekController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-view')) {
                abort(403, "Tidak memiliki akses ke halaman prospek");
            }

            $query = Prospek::with(['createdBy', 'updatedBy'])->orderBy('created_at', 'desc');

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan tipe
            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            // Filter berdasarkan tujuan
            if ($request->filled('tujuan')) {
                $query->where('tujuan_pengiriman', 'like', '%' . $request->tujuan . '%');
            }

            // Filter berdasarkan ukuran
            if ($request->filled('ukuran')) {
                $query->where('ukuran', $request->ukuran);
            }

            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_supir', 'like', '%' . $search . '%')
                      ->orWhere('barang', 'like', '%' . $search . '%')
                      ->orWhere('pt_pengirim', 'like', '%' . $search . '%')
                      ->orWhere('nomor_kontainer', 'like', '%' . $search . '%')
                      ->orWhere('no_seal', 'like', '%' . $search . '%')
                      ->orWhere('tujuan_pengiriman', 'like', '%' . $search . '%')
                      ->orWhere('nama_kapal', 'like', '%' . $search . '%');
                });
            }

            $prospeks = $query->paginate(15)->appends($request->query());

            return view('prospek.index', compact('prospeks'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data prospek: ' . $e->getMessage());
        }
    }

    // Create and Store methods removed - Prospek is read-only

    /**
     * Display the specified resource.
     */
    public function show(Prospek $prospek)
    {
        $user = Auth::user();
        if (!$this->hasProspekPermission($user, 'prospek-view')) {
            abort(403, "Tidak memiliki akses untuk melihat detail prospek");
        }

        $prospek->load(['createdBy', 'updatedBy']);

        return view('prospek.show', compact('prospek'));
    }

    // Edit, Update, and Destroy methods removed - Prospek is read-only

    /**
     * Check if user has specific prospek permission
     */
    private function hasProspekPermission($user, $permission)
    {
        try {
            return DB::table('user_permissions')
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->where('user_permissions.user_id', $user->id)
                ->where('permissions.name', $permission)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}

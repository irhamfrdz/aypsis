<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PergerakanKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ObController extends Controller
{
    /**
     * Display the main OB page with ship and voyage selection
     */
    public function index()
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman OB.');
        }

        // Get all active ships
        $masterKapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal', 'asc')
            ->get();

        return view('ob.select', compact('masterKapals'));
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

        // Get voyages for the selected ship
        $voyages = PergerakanKapal::where('nama_kapal', $namaKapal)
            ->whereNotNull('voyage')
            ->where('voyage', '!=', '')
            ->orderBy('tanggal_sandar', 'desc')
            ->distinct()
            ->pluck('voyage')
            ->toArray();

        return response()->json([
            'success' => true,
            'voyages' => $voyages
        ]);
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
            'kapal_id' => 'required|exists:master_kapals,id',
            'no_voyage' => 'required|string',
        ]);

        $ship = MasterKapal::findOrFail($request->kapal_id);
        $voyage = $request->no_voyage;

        // Store selection in session for use in OB operations
        session([
            'selected_ob_ship' => [
                'id' => $ship->id,
                'nama_kapal' => $ship->nama_kapal,
                'kode_kapal' => $ship->kode_kapal,
            ],
            'selected_ob_voyage' => $voyage
        ]);

        // Redirect to tagihan OB with filters
        return redirect()->route('tagihan-ob.index', [
            'nama_kapal' => $ship->nama_kapal,
            'no_voyage' => $voyage
        ])->with('success', "Berhasil memilih kapal {$ship->nama_kapal} dengan voyage {$voyage}");
    }
}


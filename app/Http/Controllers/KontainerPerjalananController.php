<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontainerPerjalanan;
use Illuminate\Support\Facades\DB;

class KontainerPerjalananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display list of containers in transit
     */
    public function index(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        $query = KontainerPerjalanan::with(['suratJalan', 'gudangTujuan'])
            ->where('status', 'dalam_perjalanan')
            ->orderBy('waktu_keluar', 'desc');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%")
                    ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_keluar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_keluar', '<=', $request->tanggal_sampai);
        }

        $kontainersInTransit = $query->paginate(20);

        return view('kontainer-perjalanan.index', compact('kontainersInTransit'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\Kontainer;
use App\Models\StockKontainer;
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

        $query = DB::table('surat_jalans as sj')
            ->select(
                'sj.id',
                'sj.no_surat_jalan',
                'sj.tanggal_surat_jalan',
                'sj.no_kontainer',
                'sj.tujuan_pengiriman',
                'sj.supir',
                'sj.no_plat',
                'sj.waktu_keluar',
                'sj.catatan_keluar',
                'sj.tipe_kontainer',
                'sj.size as ukuran'
            )
            ->whereNotNull('sj.waktu_keluar')
            ->whereNull('sj.tanggal_tanda_terima')
            ->orderBy('sj.waktu_keluar', 'desc');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sj.no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('sj.no_kontainer', 'like', "%{$search}%")
                    ->orWhere('sj.supir', 'like', "%{$search}%")
                    ->orWhere('sj.no_plat', 'like', "%{$search}%")
                    ->orWhere('sj.tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('sj.waktu_keluar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('sj.waktu_keluar', '<=', $request->tanggal_sampai);
        }

        $kontainersInTransit = $query->paginate(20);

        return view('kontainer-perjalanan.index', compact('kontainersInTransit'));
    }
}

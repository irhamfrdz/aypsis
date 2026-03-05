<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\StockKontainer;
use App\Models\Kontainer;

class ObAntarGudangController extends Controller
{
    /**
     * Display the select gudang page.
     */
    public function select()
    {
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        return view('ob-antar-gudang.select', compact('gudangs'));
    }

    /**
     * Display the index page with kontainer data for selected gudang.
     */
    public function index(Request $request)
    {
        $gudangId = $request->input('gudang_id');
        
        if (!$gudangId) {
            return redirect()->route('ob-antar-gudang.select')
                ->with('error', 'Silakan pilih gudang terlebih dahulu.');
        }

        $gudang = Gudang::findOrFail($gudangId);
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        // Build queries with filters
        $search = $request->input('search');
        $filterStatus = $request->input('status');
        $filterUkuran = $request->input('ukuran');
        $filterTipe = $request->input('tipe_kontainer');

        // Query stock_kontainers for this gudang
        $stockKontainersQuery = StockKontainer::where('gudangs_id', $gudangId);
        
        if ($search) {
            $stockKontainersQuery->where(function($q) use ($search) {
                $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                  ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                  ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $stockKontainersQuery->where('status', $filterStatus);
        }

        if ($filterUkuran) {
            $stockKontainersQuery->where('ukuran', $filterUkuran);
        }

        if ($filterTipe) {
            $stockKontainersQuery->where('tipe_kontainer', $filterTipe);
        }

        $stockKontainers = $stockKontainersQuery->with('gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'stock_page')
            ->appends($request->query());

        // Query kontainers for this gudang
        $kontainersQuery = Kontainer::where('gudangs_id', $gudangId);
        
        if ($search) {
            $kontainersQuery->where(function($q) use ($search) {
                $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                  ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                  ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $kontainersQuery->where('status', $filterStatus);
        }

        if ($filterUkuran) {
            $kontainersQuery->where('ukuran', $filterUkuran);
        }

        if ($filterTipe) {
            $kontainersQuery->where('tipe_kontainer', $filterTipe);
        }

        $kontainers = $kontainersQuery->with('gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'kontainer_page')
            ->appends($request->query());

        // Stats
        $totalStockKontainers = StockKontainer::where('gudangs_id', $gudangId)->count();
        $totalKontainers = Kontainer::where('gudangs_id', $gudangId)->count();
        $totalAll = $totalStockKontainers + $totalKontainers;

        // Size breakdown
        $stockSizes = StockKontainer::where('gudangs_id', $gudangId)
            ->selectRaw("ukuran, COUNT(*) as total")
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();
            
        $kontainerSizes = Kontainer::where('gudangs_id', $gudangId)
            ->selectRaw("ukuran, COUNT(*) as total")
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();

        return view('ob-antar-gudang.index', compact(
            'gudang',
            'gudangs',
            'stockKontainers',
            'kontainers',
            'totalStockKontainers',
            'totalKontainers',
            'totalAll',
            'stockSizes',
            'kontainerSizes',
            'search',
            'filterStatus',
            'filterUkuran',
            'filterTipe'
        ));
    }
}

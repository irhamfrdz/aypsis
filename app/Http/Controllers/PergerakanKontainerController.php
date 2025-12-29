<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\Gudang;
use Illuminate\Support\Facades\DB;

class PergerakanKontainerController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $gudangFilter = $request->input('gudang');
        $statusFilter = $request->input('status');
        $perPage = $request->input('per_page', 25);

        // Get kontainers from both tables
        $kontainers = Kontainer::with('gudang')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_seri', 'like', "%{$search}%")
                      ->orWhere('kode_pemilik', 'like', "%{$search}%")
                      ->orWhere('nomor_urut', 'like', "%{$search}%");
                });
            })
            ->when($gudangFilter, function($query, $gudangFilter) {
                $query->where('gudangs_id', $gudangFilter);
            })
            ->when($statusFilter, function($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $stockKontainers = StockKontainer::with('gudang')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('kode_pemilik', 'like', "%{$search}%")
                      ->orWhere('nomor_seri', 'like', "%{$search}%")
                      ->orWhere('nomor_urut', 'like', "%{$search}%");
                });
            })
            ->when($gudangFilter, function($query, $gudangFilter) {
                $query->where('gudangs_id', $gudangFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Get all gudangs for filter dropdown
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        // Statistics
        $totalKontainers = Kontainer::count();
        $totalStockKontainers = StockKontainer::count();
        $kontainersByGudang = Kontainer::select('gudangs_id', DB::raw('count(*) as total'))
            ->groupBy('gudangs_id')
            ->with('gudang')
            ->get();

        return view('pergerakan-kontainer.index', compact(
            'kontainers',
            'stockKontainers',
            'gudangs',
            'totalKontainers',
            'totalStockKontainers',
            'kontainersByGudang'
        ));
    }
}

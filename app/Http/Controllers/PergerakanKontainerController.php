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
        $perPage = $request->input('per_page', 25);

        // Get kontainers from kontainers table with 'kontainer' type
        $kontainersQuery = Kontainer::query()
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                      ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                      ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                      ->orWhere('akhiran_kontainer', 'like', "%{$search}%");
                });
            })
            ->when($gudangFilter, function($query, $gudangFilter) {
                $query->where('gudangs_id', $gudangFilter);
            })
            ->select(
                'id',
                'nomor_seri_gabungan',
                'awalan_kontainer',
                'nomor_seri_kontainer',
                'akhiran_kontainer',
                'tipe_kontainer',
                'ukuran',
                'gudangs_id',
                'status',
                'created_at',
                DB::raw("'kontainer' as source_table")
            );

        // Get kontainers from stock_kontainers table with 'stock' type
        $stockQuery = StockKontainer::query()
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                      ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                      ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                      ->orWhere('akhiran_kontainer', 'like', "%{$search}%");
                });
            })
            ->when($gudangFilter, function($query, $gudangFilter) {
                $query->where('gudangs_id', $gudangFilter);
            })
            ->select(
                'id',
                'nomor_seri_gabungan',
                'awalan_kontainer',
                'nomor_seri_kontainer',
                'akhiran_kontainer',
                'tipe_kontainer',
                'ukuran',
                'gudangs_id',
                'status',
                'created_at',
                DB::raw("'stock' as source_table")
            );

        // Merge both queries using union and paginate
        $allKontainers = $kontainersQuery
            ->union($stockQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Get all gudangs for filter dropdown
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        // Statistics
        $totalKontainers = Kontainer::count();
        $totalStockKontainers = StockKontainer::count();
        $totalAll = $totalKontainers + $totalStockKontainers;

        return view('pergerakan-kontainer.index', compact(
            'allKontainers',
            'gudangs',
            'totalKontainers',
            'totalStockKontainers',
            'totalAll'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kontainer_id' => 'required',
            'source_table' => 'required|in:kontainer,stock',
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
            'tanggal_pergerakan' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Update gudangs_id based on source table
            if ($request->source_table === 'kontainer') {
                $kontainer = Kontainer::findOrFail($request->kontainer_id);
                $gudangAsal = $kontainer->gudangs_id;
                $kontainer->update([
                    'gudangs_id' => $request->gudang_tujuan_id
                ]);
            } else {
                $kontainer = StockKontainer::findOrFail($request->kontainer_id);
                $gudangAsal = $kontainer->gudangs_id;
                $kontainer->update([
                    'gudangs_id' => $request->gudang_tujuan_id
                ]);
            }

            // You can create a movement log here if needed
            // Example: PergerakanKontainerLog::create([...]);

            DB::commit();

            return redirect()->route('pergerakan-kontainer.index')
                ->with('success', 'Pergerakan kontainer berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pergerakan-kontainer.index')
                ->with('error', 'Gagal menyimpan pergerakan kontainer: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Support\Facades\DB;

class StockKontainerPergudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::where('status', 'aktif')->get();
        if ($gudangs->isEmpty()) {
            $gudangs = Gudang::all(); // Fallback if status is different
        }

        // Count for Kontainer (Sewa)
        $kontainerCounts = Kontainer::select('gudangs_id', DB::raw('count(*) as total'))
            ->groupBy('gudangs_id')
            ->pluck('total', 'gudangs_id');

        // Count for Stock Kontainer
        $stockCounts = StockKontainer::select('gudangs_id', DB::raw('count(*) as total'))
            ->groupBy('gudangs_id')
            ->pluck('total', 'gudangs_id');

        // Map data for display
        $data = $gudangs->map(function($gudang) use ($kontainerCounts, $stockCounts) {
            $sewaCount = $kontainerCounts[$gudang->id] ?? 0;
            $stockCount = $stockCounts[$gudang->id] ?? 0;
            return [
                'id' => $gudang->id,
                'nama_gudang' => $gudang->nama_gudang,
                'lokasi' => $gudang->lokasi,
                'total_sewa' => $sewaCount,
                'total_stock' => $stockCount,
                'total_gabungan' => $sewaCount + $stockCount
            ];
        });

        // Add a "Tanpa Gudang" record for items without a warehouse
        $unassignedSewa = Kontainer::whereNull('gudangs_id')->count();
        $unassignedStock = StockKontainer::whereNull('gudangs_id')->count();
        
        if ($unassignedSewa > 0 || $unassignedStock > 0) {
            $data->push([
                'id' => null,
                'nama_gudang' => 'Tanpa Gudang / Belum Ditentukan',
                'lokasi' => '-',
                'total_sewa' => $unassignedSewa,
                'total_stock' => $unassignedStock,
                'total_gabungan' => $unassignedSewa + $unassignedStock
            ]);
        }

        return view('master-kontainer.stock-pergudang', compact('data'));
    }
}

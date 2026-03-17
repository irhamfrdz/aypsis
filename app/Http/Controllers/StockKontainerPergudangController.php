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

    public function show($id)
    {
        $gudang = null;
        if ($id !== 'none' && $id != '') {
            $gudang = Gudang::find($id);
            if (!$gudang) {
                return redirect()->route('kontainer.stock-pergudang')->with('error', 'Gudang tidak ditemukan.');
            }
        }

        $namaGudang = $gudang ? $gudang->nama_gudang : 'Tanpa Gudang / Belum Ditentukan';

        // 1. Ambil dari Kontainer (Sewa)
        $querySewa = \App\Models\Kontainer::where('status', '!=', 'inactive');
        if ($id === 'none' || $id == '') {
            $querySewa->whereNull('gudangs_id');
        } else {
            $querySewa->where('gudangs_id', $id);
        }
        $sewas = $querySewa->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status')->get()->map(function($i) {
            $i->tipe_sumber = 'Sewa';
            return $i;
        });

        // 2. Ambil dari StockKontainer (Milik Sendiri)
        $queryStock = \App\Models\StockKontainer::where('status', '!=', 'inactive');
        if ($id === 'none' || $id == '') {
            $queryStock->whereNull('gudangs_id');
        } else {
            $queryStock->where('gudangs_id', $id);
        }
        $stocks = $queryStock->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status')->get()->map(function($i) {
            $i->tipe_sumber = 'Stock';
            return $i;
        });

        // Merge lists together
        $allContainers = $sewas->concat($stocks);

        return view('master-kontainer.stock-pergudang-detail', compact('allContainers', 'namaGudang'));
    }
}

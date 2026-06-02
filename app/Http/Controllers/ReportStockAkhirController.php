<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use App\Models\StockBanLuarBatam;
use App\Models\StockAmprahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportStockAkhirController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('stock-ban-view') && !$user->can('stock-amprahan-view')) {
            abort(403, 'Unauthorized');
        }

        $search = $request->input('search');
        $lokasi = $request->input('lokasi');

        // 1. Fetch Stock Ban (Jakarta)
        $banQuery = StockBan::with(['namaStockBan', 'mobil'])
            ->where('status', 'Stok');

        if ($lokasi) {
            $banQuery->where('lokasi', 'like', "%{$lokasi}%");
        }
        if ($search) {
            $banQuery->where(function ($q) use ($search) {
                $q->where('nomor_seri', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        $stockBans = $banQuery->get();

        // 2. Fetch Stock Ban (Batam)
        $banBatamQuery = StockBanLuarBatam::with(['namaStockBan', 'mobil'])
            ->where('status', 'Stok');

        if ($lokasi) {
            $banBatamQuery->where('lokasi', 'like', "%{$lokasi}%");
        }
        if ($search) {
            $banBatamQuery->where(function ($q) use ($search) {
                $q->where('nomor_seri', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        $stockBanBatams = $banBatamQuery->get();

        // 3. Fetch Stock Amprahan
        $amprahanQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'vendorAmprahan'])
            ->where('jumlah', '>', 0);

        if ($lokasi) {
            $amprahanQuery->where('lokasi', 'like', "%{$lokasi}%");
        }
        if ($search) {
            $amprahanQuery->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('nomor_bukti', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        $stockAmprahans = $amprahanQuery->get();

        // Summaries
        $totalBanStok = $stockBans->count();
        $totalBanBatamStok = $stockBanBatams->count();
        $totalAmprahanStok = $stockAmprahans->sum('jumlah');

        $valuasiBan = $stockBans->sum('harga_beli') + $stockBanBatams->sum('harga_beli');
        
        $valuasiAmprahan = $stockAmprahans->reduce(function ($carry, $item) {
            return $carry + (($item->harga_satuan * $item->jumlah) + $item->adjustment);
        }, 0);

        return view('report-stock-akhir.index', compact(
            'stockBans',
            'stockBanBatams',
            'stockAmprahans',
            'totalBanStok',
            'totalBanBatamStok',
            'totalAmprahanStok',
            'valuasiBan',
            'valuasiAmprahan',
            'search',
            'lokasi'
        ));
    }
}

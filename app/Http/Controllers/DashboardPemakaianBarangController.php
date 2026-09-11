<?php

namespace App\Http\Controllers;

use App\Models\StockAmprahanUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPemakaianBarangController extends Controller
{
    public function index(Request $request)
    {
        $categories = ['penerima' => 'Penerima / Karyawan', 'kendaraan' => 'Kendaraan / Truck', 'alat_berat' => 'Alat Berat', 'kapal' => 'Kapal', 'kantor' => 'Kantor'];
        $usages = null;
        $totalNilai = 0;

        if ($request->hasAny(['kategori_pemakai', 'from_date', 'to_date'])) {
            $filters = $request->validate([
                'kategori_pemakai' => 'required|in:penerima,kendaraan,alat_berat,kapal,kantor',
                'from_date' => 'required|date_format:Y-m-d',
                'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            ]);
            $query = StockAmprahanUsage::whereBetween('tanggal_pengambilan', [Carbon::parse($filters['from_date'])->startOfDay(), Carbon::parse($filters['to_date'])->endOfDay()]);
            if ($filters['kategori_pemakai'] === 'kendaraan') {
                $query->where(fn ($q) => $q->whereNotNull('kendaraan_id')->orWhereNotNull('truck_id')->orWhereNotNull('buntut_id'));
            } else {
                $column = $filters['kategori_pemakai'] === 'kantor' ? 'kantor' : $filters['kategori_pemakai'].'_id';
                $query->whereNotNull($column);
                if ($column === 'kantor') {
                    $query->where('kantor', '!=', '');
                }
            }
            // Same branch restriction as valuasi pemakaian.
            $user = $request->user();
            if ($user && $user->karyawan && strtoupper($user->karyawan->cabang ?? '') === 'BATAM'
                && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
                $query->whereHas('stockAmprahan', fn ($q) => $q->where('lokasi', 'like', '%BATAM%'));
            }
            $totalNilai = (clone $query)
                ->leftJoin('stock_amprahans', 'stock_amprahans.id', '=', 'stock_amprahan_usages.stock_amprahan_id')
                ->selectRaw('COALESCE(SUM(stock_amprahan_usages.jumlah * COALESCE(stock_amprahans.harga_satuan, 0)), 0) as total_nilai')->value('total_nilai');
            $usages = $query->with(['stockAmprahan.masterNamaBarangAmprahan', 'stockAmprahan.vendorAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'alatBerat', 'kapal'])
                ->orderByDesc('tanggal_pengambilan')->orderByDesc('id')->paginate(25)->withQueryString();
        }

        return view('dashboard-pemakaian-barang.index', compact('categories', 'usages', 'totalNilai'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\AlatBerat;
use App\Models\StockAmprahanUsage;
use App\Models\StockBan;
use Illuminate\Http\Request;

class RekapBiayaAssetController extends Controller
{
    /**
     * Display a listing of the resource (filter form).
     */
    public function index()
    {
        // Get all mobil
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        
        // Get all alat berat
        $alatBerats = AlatBerat::where('status', 'aktif')
                        ->orWhereNull('status')
                        ->orderBy('nama')
                        ->get();

        return view('rekap-biaya-asset.index', compact('mobils', 'alatBerats'));
    }

    /**
     * Show the detailed costs for the selected asset.
     */
    public function show(Request $request)
    {
        $request->validate([
            'asset_type' => 'required|in:mobil,alat_berat',
            'asset_id' => 'required|integer',
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer',
        ]);

        $type = $request->asset_type;
        $id = $request->asset_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Base query for Stock Amprahan Usage
        $query = StockAmprahanUsage::with(['stockAmprahan', 'penerima', 'createdBy']);

        $assetName = '';
        if ($type === 'mobil') {
            // Can be kendaraan_id, truck_id, or buntut_id
            $query->where(function($q) use ($id) {
                $q->where('kendaraan_id', $id)
                  ->orWhere('truck_id', $id)
                  ->orWhere('buntut_id', $id);
            });
            $mobil = Mobil::find($id);
            $assetName = $mobil ? ($mobil->nomor_polisi ?? 'Truk ' . $id) : 'Unknown';
        } else {
            // alat berat
            $query->where('alat_berat_id', $id);
            $ab = AlatBerat::find($id);
            $assetName = $ab ? ($ab->nama ?? 'Alat Berat ' . $id) : 'Unknown';
        }

        if ($bulan) {
            $query->whereMonth('tanggal_pengambilan', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal_pengambilan', $tahun);
        }

        $usages = $query->orderBy('tanggal_pengambilan', 'desc')->get();

        // Calculate totals
        $totalNominal = 0;
        foreach ($usages as $usage) {
            $hargaSatuan = floatval($usage->stockAmprahan->harga_satuan ?? 0);
            $jumlah = floatval($usage->jumlah);
            $total = $hargaSatuan * $jumlah;
            
            $usage->apportioned = [
                'nominal' => $total,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $total,
            ];
            $usage->is_amprahan = true;
            $usage->nomor_invoice = $usage->stockAmprahan->nomor_bukti ?? '-';
            $usage->tanggal = $usage->tanggal_pengambilan;
            $usage->jenis_biaya = 'Pemakaian Amprahan (' . ($usage->stockAmprahan->nama_barang ?? 'Barang') . ')';
            $usage->klasifikasiBiaya = (object)['nama' => 'Stock Amprahan'];

            $totalNominal += $total;
        }

        // Fetch Pemakaian Ban
        $banQuery = StockBan::with(['namaStockBan']);
        
        if ($type === 'mobil') {
            $banQuery->where('mobil_id', $id);
        } else {
            $banQuery->where('alat_berat_id', $id);
        }

        if ($bulan) {
            $banQuery->where(function($q) use ($bulan) {
                // Check both tanggal_digunakan and tanggal_keluar since both are used to indicate usage date
                $q->whereMonth('tanggal_digunakan', $bulan)
                  ->orWhere(function($q2) use ($bulan) {
                      $q2->whereNull('tanggal_digunakan')->whereMonth('tanggal_keluar', $bulan);
                  });
            });
        }
        if ($tahun) {
            $banQuery->where(function($q) use ($tahun) {
                $q->whereYear('tanggal_digunakan', $tahun)
                  ->orWhere(function($q2) use ($tahun) {
                      $q2->whereNull('tanggal_digunakan')->whereYear('tanggal_keluar', $tahun);
                  });
            });
        }

        $bans = $banQuery->get();

        foreach ($bans as $ban) {
            $total = floatval($ban->harga_beli ?? 0);
            
            $ban->apportioned = [
                'nominal' => $total,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $total,
            ];
            $ban->is_amprahan = false;
            $ban->is_ban = true;
            $ban->nomor_invoice = $ban->nomor_bukti ?? '-';
            $ban->tanggal = $ban->tanggal_digunakan ?? $ban->tanggal_keluar;
            $ban->jenis_biaya = 'Pemakaian Ban (' . ($ban->namaStockBan->nama ?? 'Ban') . ' - ' . ($ban->nomor_seri ?? '-') . ')';
            $ban->klasifikasiBiaya = (object)['nama' => 'Stock Ban'];
            
            // To match view expectation
            $ban->jumlah = 1;
            
            // Need to mock relationship for view if it expects it
            $ban->stockAmprahan = (object)[
                'nama_barang' => ($ban->namaStockBan->nama ?? 'Ban') . ' (' . ($ban->nomor_seri ?? '-') . ')',
                'harga_satuan' => $total
            ];

            $totalNominal += $total;
            $usages->push($ban);
        }

        // Sort combined usages by date descending
        $usages = $usages->sortByDesc('tanggal')->values();

        $summary = [
            'total_nominal' => $totalNominal,
            'total_ppn' => 0,
            'total_pph' => 0,
            'grand_total' => $totalNominal,
        ];

        // Group by classification/jenis_biaya
        $grouped = $usages->groupBy(function ($item) {
            return $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? 'Lain-lain';
        });

        return view('rekap-biaya-asset.show', compact('usages', 'summary', 'grouped', 'assetName', 'type', 'bulan', 'tahun'));
    }
}

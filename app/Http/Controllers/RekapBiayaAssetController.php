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

        // Fetch CURRENT Pemakaian Ban
        $banQuery = StockBan::with(['namaStockBan']);
        
        if ($type === 'mobil') {
            $banQuery->where('mobil_id', $id);
        } else {
            $banQuery->where('alat_berat_id', $id);
        }

        $currentBans = $banQuery->get();
        $currentBanKeys = []; // To avoid putting current bans into history

        foreach ($currentBans as $banModel) {
            $tanggal = $banModel->tanggal_digunakan ?? $banModel->tanggal_keluar ?: date('Y-m-d');
            
            if ($bulan && \Carbon\Carbon::parse($tanggal)->format('n') != $bulan) continue;
            if ($tahun && \Carbon\Carbon::parse($tanggal)->format('Y') != $tahun) continue;

            $currentBanKeys[$banModel->id . '_' . $tanggal] = true;

            $ban = clone $banModel;
            $ban->apportioned = [
                'nominal' => 0, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 0,
            ];
            $ban->is_amprahan = false;
            $ban->is_ban_current = true;
            $ban->is_ban = true;
            $ban->nomor_invoice = $banModel->nomor_bukti ?? '-';
            $ban->tanggal = $tanggal;
            $ban->jenis_biaya = 'Pemakaian Ban (' . ($ban->namaStockBan->nama ?? 'Ban') . ' - ' . ($ban->nomor_seri ?? '-') . ')';
            $ban->klasifikasiBiaya = (object)['nama' => 'Stock Ban'];
            $ban->jumlah = 1;
            $ban->display_nomor_seri = $ban->nomor_seri ?? '-';
            $ban->display_merk = $ban->namaStockBan->nama ?? 'Ban';
            $ban->stockAmprahan = (object)[
                'nama_barang' => ($ban->namaStockBan->nama ?? 'Ban') . ' (' . ($ban->nomor_seri ?? '-') . ')',
                'harga_satuan' => 0
            ];

            $usages->push($ban);
        }

        // Fetch HISTORICAL Pemakaian Ban from AuditLog
        $field = $type === 'mobil' ? 'mobil_id' : 'alat_berat_id';
        $audits = \App\Models\AuditLog::where('auditable_type', \App\Models\StockBan::class)
            ->where('action', 'updated')
            ->where(function($q) use ($id, $field) {
                $q->whereJsonContains("new_values->$field", (int)$id)
                  ->orWhereJsonContains("new_values->$field", (string)$id);
            })->get();

        $banIds = $audits->pluck('auditable_id')->unique();
        $bansModel = StockBan::with(['namaStockBan'])->whereIn('id', $banIds)->get()->keyBy('id');

        $processedHistories = [];

        foreach ($audits as $audit) {
            $newVals = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;
            $oldVals = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
            
            $oldId = $oldVals[$field] ?? null;
            if ($oldId == $id) continue;

            $banModel = $bansModel->get($audit->auditable_id);
            if (!$banModel) continue;

            $tanggal = $newVals['tanggal_digunakan'] ?? $newVals['tanggal_keluar'] ?? $audit->created_at->format('Y-m-d');
            
            if ($bulan && \Carbon\Carbon::parse($tanggal)->format('n') != $bulan) continue;
            if ($tahun && \Carbon\Carbon::parse($tanggal)->format('Y') != $tahun) continue;

            // Skip if this is the current active assignment
            if (isset($currentBanKeys[$banModel->id . '_' . $tanggal])) continue;

            // Avoid duplicate historical logs for same day if any
            $histKey = $banModel->id . '_' . $tanggal;
            if (isset($processedHistories[$histKey])) continue;
            $processedHistories[$histKey] = true;

            $ban = clone $banModel;
            $ban->apportioned = [
                'nominal' => 0, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 0,
            ];
            $ban->is_amprahan = false;
            $ban->is_ban_history = true;
            $ban->is_ban = true;
            $ban->nomor_invoice = $newVals['nomor_bukti'] ?? $banModel->nomor_bukti ?? '-';
            $ban->tanggal = $tanggal;
            $ban->jenis_biaya = 'Pemakaian Ban (' . ($ban->namaStockBan->nama ?? 'Ban') . ' - ' . ($newVals['nomor_seri'] ?? $banModel->nomor_seri ?? '-') . ')';
            $ban->klasifikasiBiaya = (object)['nama' => 'Stock Ban'];
            $ban->jumlah = 1;
            $ban->display_nomor_seri = $newVals['nomor_seri'] ?? $banModel->nomor_seri ?? '-';
            $ban->display_merk = $ban->namaStockBan->nama ?? 'Ban';
            $ban->stockAmprahan = (object)[
                'nama_barang' => ($ban->namaStockBan->nama ?? 'Ban') . ' (' . ($newVals['nomor_seri'] ?? $banModel->nomor_seri ?? '-') . ')',
                'harga_satuan' => 0
            ];

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

        // Group by classification/jenis_biaya (excluding Ban)
        $grouped = $usages->filter(function ($item) {
            return !isset($item->is_ban) || !$item->is_ban;
        })->groupBy(function ($item) {
            return $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? 'Lain-lain';
        });

        return view('rekap-biaya-asset.show', compact('usages', 'summary', 'grouped', 'assetName', 'type', 'bulan', 'tahun'));
    }
}

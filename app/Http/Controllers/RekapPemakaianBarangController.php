<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\StockBan;
use App\Models\MasterNamaStockBan;
use Carbon\Carbon;

class RekapPemakaianBarangController extends Controller
{
    public function index(Request $request)
    {
        $namaBarang = $request->input('nama_barang');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch distinct names
        $amprahanItems = StockAmprahan::select('nama_barang')
                            ->whereHas('usages')
                            ->distinct()
                            ->pluck('nama_barang')
                            ->toArray();
                            
        $banItems = MasterNamaStockBan::select('nama')
                            ->distinct()
                            ->pluck('nama')
                            ->toArray();

        // Combine and sort alphabetically
        $allBarang = array_unique(array_merge($amprahanItems, $banItems));
        sort($allBarang);

        $results = collect();

        if ($namaBarang && $startDate && $endDate) {
            $isAmprahan = in_array($namaBarang, $amprahanItems);
            $isBan = in_array($namaBarang, $banItems);
            
            if ($isAmprahan) {
                $amprahanUsages = StockAmprahanUsage::with(['stockAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'alatBerat'])
                    ->whereHas('stockAmprahan', function($q) use ($namaBarang) {
                        $q->where('nama_barang', $namaBarang);
                    })
                    ->whereBetween('tanggal_pengambilan', [$startDate, $endDate])
                    ->get();
                    
                foreach ($amprahanUsages as $usage) {
                    $unitName = '-';
                    if ($usage->kendaraan) $unitName = $usage->kendaraan->nomor_polisi;
                    elseif ($usage->truck) $unitName = $usage->truck->nomor_polisi;
                    elseif ($usage->buntut) $unitName = $usage->buntut->nomor_polisi;
                    elseif ($usage->alatBerat) $unitName = $usage->alatBerat->nama;
                    
                    $results->push((object)[
                        'tanggal' => Carbon::parse($usage->tanggal_pengambilan)->format('Y-m-d'),
                        'nama_barang' => $usage->stockAmprahan->nama_barang ?? '-',
                        'penerima' => $usage->penerima->nama ?? '-',
                        'unit' => $unitName,
                        'qty' => floatval($usage->jumlah) . ' ' . ($usage->stockAmprahan->satuan ?? ''),
                        'keterangan' => $usage->keterangan,
                        'sumber' => 'Amprahan'
                    ]);
                }
            }
            
            if ($isBan) {
                $banUsages = StockBan::with(['namaStockBan', 'penerima', 'mobil', 'alatBerat'])
                    ->whereHas('namaStockBan', function($q) use ($namaBarang) {
                        $q->where('nama', $namaBarang);
                    })
                    ->where(function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal_digunakan', [$startDate, $endDate])
                          ->orWhere(function($subQ) use ($startDate, $endDate) {
                              $subQ->whereNull('tanggal_digunakan')
                                   ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                   ->where('status', 'Terpakai');
                          });
                    })
                    ->get();
                    
                foreach ($banUsages as $ban) {
                    $unitName = '-';
                    if ($ban->mobil) $unitName = $ban->mobil->nomor_polisi;
                    elseif ($ban->alatBerat) $unitName = $ban->alatBerat->nama;
                    
                    $tgl = $ban->tanggal_digunakan ? Carbon::parse($ban->tanggal_digunakan)->format('Y-m-d') : $ban->updated_at->format('Y-m-d');
                    
                    $results->push((object)[
                        'tanggal' => $tgl,
                        'nama_barang' => 'Ban: ' . ($ban->namaStockBan->nama ?? 'Unknown') . ' (' . ($ban->merk ?? '-') . ')',
                        'penerima' => $ban->penerima->nama ?? '-',
                        'unit' => $unitName,
                        'qty' => '1 Pcs',
                        'keterangan' => ($ban->keterangan ? $ban->keterangan . ' | ' : '') . 'No Seri: ' . ($ban->nomor_seri ?? '-'),
                        'sumber' => 'Stock Ban'
                    ]);
                }
            }
            
            // Sort results by date desc
            $results = $results->sortByDesc('tanggal')->values();
        }

        return view('rekap-pemakaian-barang.index', compact('allBarang', 'results', 'namaBarang', 'startDate', 'endDate'));
    }
}

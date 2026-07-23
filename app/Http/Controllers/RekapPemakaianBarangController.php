<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\StockBan;
use App\Models\NamaStockBan;
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
                            
        $banItems = NamaStockBan::select('nama')
                            ->distinct()
                            ->pluck('nama')
                            ->toArray();

        // Combine and sort alphabetically
        $allBarang = array_unique(array_merge($amprahanItems, $banItems));
        sort($allBarang);

        $results = collect();

        if ($namaBarang) {
            $isAmprahan = in_array($namaBarang, $amprahanItems);
            $isBan = in_array($namaBarang, $banItems);
            
            if ($isAmprahan) {
                $amprahanUsages = StockAmprahanUsage::with(['stockAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'alatBerat', 'kapal', 'chasisBatam'])
                    ->whereHas('stockAmprahan', function($q) use ($namaBarang) {
                        $q->where('nama_barang', $namaBarang);
                    })
                    ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal_pengambilan', [$startDate, $endDate]);
                    })
                    ->get();
                    
                foreach ($amprahanUsages as $usage) {
                    $unitName = '-';
                    if ($usage->kendaraan) $unitName = $usage->kendaraan->nomor_polisi;
                    elseif ($usage->truck) $unitName = $usage->truck->nomor_polisi;
                    elseif ($usage->buntut) $unitName = $usage->buntut->nomor_polisi;
                    elseif ($usage->alatBerat) $unitName = $usage->alatBerat->nama;
                    elseif ($usage->kapal) $unitName = $usage->kapal->nama_kapal;
                    elseif ($usage->chasisBatam) $unitName = $usage->chasisBatam->kode;
                    elseif ($usage->kantor) $unitName = $usage->kantor;
                    
                    $penerimaName = '-';
                    if ($usage->penerima) {
                        $penerimaName = $usage->penerima->nama_lengkap ?? $usage->penerima->nama_panggilan ?? '-';
                    }
                    
                    $results->push((object)[
                        'tanggal' => Carbon::parse($usage->tanggal_pengambilan)->format('Y-m-d'),
                        'nama_barang' => $usage->stockAmprahan->nama_barang ?? '-',
                        'penerima' => $penerimaName,
                        'unit' => $unitName,
                        'qty' => floatval($usage->jumlah) . ' ' . ($usage->stockAmprahan->satuan ?? ''),
                        'keterangan' => $usage->keterangan,
                        'sumber' => 'Amprahan'
                    ]);
                }
            }
            
            if ($isBan) {
                $banUsages = StockBan::with(['namaStockBan', 'penerima', 'mobil', 'alatBerat', 'kapal'])
                    ->whereHas('namaStockBan', function($q) use ($namaBarang) {
                        $q->where('nama', $namaBarang);
                    })
                    ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                        $q->where(function($q2) use ($startDate, $endDate) {
                            $q2->whereBetween('tanggal_digunakan', [$startDate, $endDate])
                               ->orWhere(function($subQ) use ($startDate, $endDate) {
                                   $subQ->whereNull('tanggal_digunakan')
                                        ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                        ->where('status', 'Terpakai');
                               });
                        });
                    }, function($q) {
                        $q->where(function($q2) {
                            $q2->whereNotNull('tanggal_digunakan')
                               ->orWhere('status', 'Terpakai');
                        });
                    })
                    ->get();
                    
                foreach ($banUsages as $ban) {
                    $unitName = '-';
                    if ($ban->mobil) $unitName = $ban->mobil->nomor_polisi;
                    elseif ($ban->alatBerat) $unitName = $ban->alatBerat->nama;
                    elseif ($ban->kapal) $unitName = $ban->kapal->nama_kapal;
                    
                    $penerimaName = '-';
                    if ($ban->penerima) {
                        $penerimaName = $ban->penerima->nama_lengkap ?? $ban->penerima->nama_panggilan ?? '-';
                    }
                    
                    $tgl = $ban->tanggal_digunakan ? Carbon::parse($ban->tanggal_digunakan)->format('Y-m-d') : $ban->updated_at->format('Y-m-d');
                    
                    $results->push((object)[
                        'tanggal' => $tgl,
                        'nama_barang' => 'Ban: ' . ($ban->namaStockBan->nama ?? 'Unknown') . ' (' . ($ban->merk ?? '-') . ')',
                        'penerima' => $penerimaName,
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

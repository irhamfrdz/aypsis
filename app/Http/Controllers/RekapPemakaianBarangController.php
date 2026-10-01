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
        $aktiva = $request->validate([
            'aktiva' => 'nullable|in:kendaraan,alat_berat,kantor,kapal',
        ])['aktiva'] ?? null;

        // Fetch distinct type barang from master for Amprahan
        $amprahanItems = StockAmprahan::with('masterNamaBarangAmprahan')
                            ->whereHas('usages')
                            ->whereNotNull('master_nama_barang_amprahan_id')
                            ->get()
                            ->pluck('masterNamaBarangAmprahan.nama_barang')
                            ->filter()
                            ->unique()
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
                        $q->whereHas('masterNamaBarangAmprahan', function($q2) use ($namaBarang) {
                            $q2->where('nama_barang', $namaBarang);
                        });
                    })
                    ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal_pengambilan', [$startDate, $endDate]);
                    })
                    ->get();
                    
                foreach ($amprahanUsages as $usage) {
                    $unitName = '-';
                    $jenisAktiva = null;
                    if ($usage->kendaraan) {
                        $jenisAktiva = 'kendaraan';
                        $nopol = trim($usage->kendaraan->nomor_polisi);
                        $unitName = (!empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->kendaraan->no_kir) ?: '-');
                    } elseif ($usage->truck) {
                        $jenisAktiva = 'kendaraan';
                        $nopol = trim($usage->truck->nomor_polisi);
                        $unitName = (!empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->truck->no_kir) ?: '-');
                    } elseif ($usage->buntut) {
                        $jenisAktiva = 'kendaraan';
                        $nopol = trim($usage->buntut->nomor_polisi);
                        $unitName = (!empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->buntut->no_kir) ?: '-');
                    } elseif ($usage->alatBerat) {
                        $jenisAktiva = 'alat_berat';
                        $unitName = $usage->alatBerat->nama;
                    } elseif ($usage->kapal) {
                        $jenisAktiva = 'kapal';
                        $unitName = $usage->kapal->nama_kapal;
                    } elseif ($usage->chasisBatam) {
                        $jenisAktiva = 'kendaraan';
                        $unitName = $usage->chasisBatam->kode;
                    } elseif ($usage->kantor) {
                        $jenisAktiva = 'kantor';
                        $unitName = $usage->kantor;
                    }

                    if ($aktiva && $jenisAktiva !== $aktiva) {
                        continue;
                    }
                    
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
                        'raw_qty' => floatval($usage->jumlah),
                        'satuan' => $usage->stockAmprahan->satuan ?? '',
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
                    $jenisAktiva = null;
                    if ($ban->mobil) {
                        $jenisAktiva = 'kendaraan';
                        $nopol = trim($ban->mobil->nomor_polisi);
                        $unitName = (!empty($nopol) && $nopol !== '-') ? $nopol : (trim($ban->mobil->no_kir) ?: '-');
                    } elseif ($ban->alatBerat) {
                        $jenisAktiva = 'alat_berat';
                        $unitName = $ban->alatBerat->nama;
                    } elseif ($ban->kapal) {
                        $jenisAktiva = 'kapal';
                        $unitName = $ban->kapal->nama_kapal;
                    }

                    if ($aktiva && $jenisAktiva !== $aktiva) {
                        continue;
                    }
                    
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
                        'raw_qty' => 1,
                        'satuan' => 'Pcs',
                        'keterangan' => ($ban->keterangan ? $ban->keterangan . ' | ' : '') . 'No Seri: ' . ($ban->nomor_seri ?? '-'),
                        'sumber' => 'Stock Ban'
                    ]);
                }
            }
            
            // Sort results by date desc
            $results = $results->sortByDesc('tanggal')->values();
        }

        return view('rekap-pemakaian-barang.index', compact('allBarang', 'results', 'namaBarang', 'startDate', 'endDate', 'aktiva'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NamaStockBan;
use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\StockBan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapPemakaianBarangController extends Controller
{
    public function index(Request $request)
    {
        $namaBarang = $request->input('nama_barang');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filters = $request->validate([
            'aktiva' => 'nullable|in:kendaraan,alat_berat,kantor,kapal',
            'lokasi' => 'nullable|in:jakarta,batam,tanjung_pinang',
        ]);
        $aktiva = $filters['aktiva'] ?? null;
        $lokasi = in_array($aktiva, ['kendaraan', 'alat_berat'], true) ? ($filters['lokasi'] ?? null) : null;

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
                    ->whereHas('stockAmprahan', function ($q) use ($namaBarang) {
                        $q->whereHas('masterNamaBarangAmprahan', function ($q2) use ($namaBarang) {
                            $q2->where('nama_barang', $namaBarang);
                        });
                    })
                    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal_pengambilan', [$startDate, $endDate]);
                    })
                    ->get();

                foreach ($amprahanUsages as $usage) {
                    $unitName = '-';
                    $jenisAktiva = null;
                    $lokasiAktiva = null;
                    if ($usage->kendaraan) {
                        $jenisAktiva = 'kendaraan';
                        $lokasiAktiva = $this->normalizeAssetLocation($usage->kendaraan->lokasi);
                        $nopol = trim($usage->kendaraan->nomor_polisi);
                        $unitName = (! empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->kendaraan->no_kir) ?: '-');
                    } elseif ($usage->truck) {
                        $jenisAktiva = 'kendaraan';
                        $lokasiAktiva = $this->normalizeAssetLocation($usage->truck->lokasi);
                        $nopol = trim($usage->truck->nomor_polisi);
                        $unitName = (! empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->truck->no_kir) ?: '-');
                    } elseif ($usage->buntut) {
                        $jenisAktiva = 'kendaraan';
                        $lokasiAktiva = $this->normalizeAssetLocation($usage->buntut->lokasi);
                        $nopol = trim($usage->buntut->nomor_polisi);
                        $unitName = (! empty($nopol) && $nopol !== '-') ? $nopol : (trim($usage->buntut->no_kir) ?: '-');
                    } elseif ($usage->alatBerat) {
                        $jenisAktiva = 'alat_berat';
                        $lokasiAktiva = $this->normalizeAssetLocation($usage->alatBerat->lokasi);
                        $unitName = $usage->alatBerat->nama;
                    } elseif ($usage->kapal) {
                        $jenisAktiva = 'kapal';
                        $unitName = $usage->kapal->nama_kapal;
                    } elseif ($usage->chasisBatam) {
                        $jenisAktiva = 'kendaraan';
                        $lokasiAktiva = $this->normalizeAssetLocation($usage->chasisBatam->lokasi) ?? 'batam';
                        $unitName = $usage->chasisBatam->kode;
                    } elseif ($usage->kantor) {
                        $jenisAktiva = 'kantor';
                        $unitName = $usage->kantor;
                    }

                    if ($aktiva && $jenisAktiva !== $aktiva) {
                        continue;
                    }
                    if ($lokasi && in_array($jenisAktiva, ['kendaraan', 'alat_berat'], true) && $lokasiAktiva !== $lokasi) {
                        continue;
                    }

                    $penerimaName = '-';
                    if ($usage->penerima) {
                        $penerimaName = $usage->penerima->nama_lengkap ?? $usage->penerima->nama_panggilan ?? '-';
                    }

                    $results->push((object) [
                        'tanggal' => Carbon::parse($usage->tanggal_pengambilan)->format('Y-m-d'),
                        'nama_barang' => $usage->stockAmprahan->nama_barang ?? '-',
                        'penerima' => $penerimaName,
                        'unit' => $unitName,
                        'qty' => floatval($usage->jumlah).' '.($usage->stockAmprahan->satuan ?? ''),
                        'raw_qty' => floatval($usage->jumlah),
                        'satuan' => $usage->stockAmprahan->satuan ?? '',
                        'keterangan' => $usage->keterangan,
                        'sumber' => 'Amprahan',
                    ]);
                }
            }

            if ($isBan) {
                $banUsages = StockBan::with(['namaStockBan', 'penerima', 'mobil', 'alatBerat', 'kapal'])
                    ->whereHas('namaStockBan', function ($q) use ($namaBarang) {
                        $q->where('nama', $namaBarang);
                    })
                    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                        $q->where(function ($q2) use ($startDate, $endDate) {
                            $q2->whereBetween('tanggal_digunakan', [$startDate, $endDate])
                                ->orWhere(function ($subQ) use ($startDate, $endDate) {
                                    $subQ->whereNull('tanggal_digunakan')
                                        ->whereBetween('updated_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                                        ->where('status', 'Terpakai');
                                });
                        });
                    }, function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNotNull('tanggal_digunakan')
                                ->orWhere('status', 'Terpakai');
                        });
                    })
                    ->get();

                foreach ($banUsages as $ban) {
                    $unitName = '-';
                    $jenisAktiva = null;
                    $lokasiAktiva = null;
                    if ($ban->mobil) {
                        $jenisAktiva = 'kendaraan';
                        $lokasiAktiva = $this->normalizeAssetLocation($ban->mobil->lokasi);
                        $nopol = trim($ban->mobil->nomor_polisi);
                        $unitName = (! empty($nopol) && $nopol !== '-') ? $nopol : (trim($ban->mobil->no_kir) ?: '-');
                    } elseif ($ban->alatBerat) {
                        $jenisAktiva = 'alat_berat';
                        $lokasiAktiva = $this->normalizeAssetLocation($ban->alatBerat->lokasi);
                        $unitName = $ban->alatBerat->nama;
                    } elseif ($ban->kapal) {
                        $jenisAktiva = 'kapal';
                        $unitName = $ban->kapal->nama_kapal;
                    }

                    if ($aktiva && $jenisAktiva !== $aktiva) {
                        continue;
                    }
                    if ($lokasi && in_array($jenisAktiva, ['kendaraan', 'alat_berat'], true) && $lokasiAktiva !== $lokasi) {
                        continue;
                    }

                    $penerimaName = '-';
                    if ($ban->penerima) {
                        $penerimaName = $ban->penerima->nama_lengkap ?? $ban->penerima->nama_panggilan ?? '-';
                    }

                    $tgl = $ban->tanggal_digunakan ? Carbon::parse($ban->tanggal_digunakan)->format('Y-m-d') : $ban->updated_at->format('Y-m-d');

                    $results->push((object) [
                        'tanggal' => $tgl,
                        'nama_barang' => 'Ban: '.($ban->namaStockBan->nama ?? 'Unknown').' ('.($ban->merk ?? '-').')',
                        'penerima' => $penerimaName,
                        'unit' => $unitName,
                        'qty' => '1 Pcs',
                        'raw_qty' => 1,
                        'satuan' => 'Pcs',
                        'keterangan' => ($ban->keterangan ? $ban->keterangan.' | ' : '').'No Seri: '.($ban->nomor_seri ?? '-'),
                        'sumber' => 'Stock Ban',
                    ]);
                }
            }

            // Sort results by date desc
            $results = $results->sortByDesc('tanggal')->values();
        }

        return view('rekap-pemakaian-barang.index', compact('allBarang', 'results', 'namaBarang', 'startDate', 'endDate', 'aktiva', 'lokasi'));
    }

    private function normalizeAssetLocation(?string $location): ?string
    {
        $location = strtoupper(trim((string) $location));

        if ($location === 'PNG' || str_contains($location, 'TANJUNG PINANG') || str_contains($location, 'TANJUNGPINANG') || str_contains($location, 'SRI BINTAN')) {
            return 'tanjung_pinang';
        }
        if ($location === 'JKT' || str_contains($location, 'JAKARTA') || str_contains($location, 'SUNDA KELAPA') || str_contains($location, 'GARASI SEMUT')) {
            return 'jakarta';
        }
        if ($location === 'BTM' || str_contains($location, 'BATAM') || str_contains($location, 'SRIMAS') || str_contains($location, 'BATU AMPAR')) {
            return 'batam';
        }

        return null;
    }
}

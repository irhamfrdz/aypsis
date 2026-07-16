<?php

namespace App\Exports;

use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanStockPerBulanExport implements WithMultipleSheets
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function sheets(): array
    {
        $date = Carbon::parse($this->bulan . '-01')->endOfMonth();

        // Ambil semua container
        $stocks = StockKontainer::where('status', '!=', 'inactive')->get();
        $sewas = Kontainer::where('status', '!=', 'inactive')->get();
        
        $allContainers = collect();
        foreach ($stocks as $s) {
            $allContainers->push((object)[
                'no' => $s->nomor_seri_gabungan,
                'ukuran' => $s->ukuran,
                'tipe' => $s->tipe_kontainer,
                'kategori' => 'STOCK',
                'gudang_id' => $s->gudangs_id,
                'created_at' => $s->created_at,
                'tanggal_masuk' => $s->tanggal_masuk
            ]);
        }
        foreach ($sewas as $s) {
            $allContainers->push((object)[
                'no' => $s->nomor_seri_gabungan,
                'ukuran' => $s->ukuran,
                'tipe' => $s->tipe_kontainer,
                'kategori' => 'SEWA',
                'gudang_id' => $s->gudangs_id,
                'created_at' => $s->created_at,
                'tanggal_masuk' => $s->tanggal_sewa // Use closest field
            ]);
        }

        // Remove duplicates by nomor (prioritize stock)
        $allContainers = $allContainers->unique('no');

        // Ambil history
        $histories = HistoryKontainer::orderBy('tanggal_kegiatan', 'asc')->get()->groupBy('nomor_kontainer');
        $gudangs = Gudang::all()->keyBy('id');

        $containersByGudang = [];
        // Initialize for all gudang
        foreach ($gudangs as $g) {
            $containersByGudang[$g->id] = collect();
        }
        $containersByGudang['none'] = collect(); // Tanpa Gudang

        foreach ($allContainers as $c) {
            // Check if container was created after the date
            if ($c->created_at && Carbon::parse($c->created_at)->isAfter($date)) {
                continue;
            }

            $containerHist = $histories->get($c->no);
            $gudangId = null;
            $tanggalMasuk = null;
            $asalTransaksi = '-';
            
            if ($containerHist) {
                foreach ($containerHist as $h) {
                    $hDate = Carbon::parse($h->tanggal_kegiatan);
                    if ($hDate->isAfter($date)) {
                        break;
                    }
                    $gudangId = $h->gudang_id;
                    $tanggalMasuk = $h->tanggal_kegiatan;
                    
                    if ($h->keterangan && $h->keterangan !== '-') {
                        $asalTransaksi = str_ireplace('OB (Overbrengen)', 'OB', $h->keterangan);
                    } elseif ($h->jenis_kegiatan && $h->jenis_kegiatan !== '-') {
                        $asalTransaksi = $h->jenis_kegiatan;
                    } else {
                        $asalTransaksi = '-';
                    }
                }
            }

            if ($gudangId === null) {
                $gudangId = $c->gudang_id;
                $tanggalMasuk = $c->tanggal_masuk;
                $asalTransaksi = '-';
            }
            
            $c->tanggal_masuk_computed = $tanggalMasuk;
            $c->asal_transaksi_computed = $asalTransaksi;

            if ($gudangId && isset($gudangs[$gudangId])) {
                $containersByGudang[$gudangId]->push($c);
            } else {
                $containersByGudang['none']->push($c);
            }
        }

        $sheets = [];
        
        foreach ($gudangs as $g) {
            if ($containersByGudang[$g->id]->count() > 0) {
                $title = substr(str_replace(['*', ':', '?', '[', ']', '/', '\\'], '', $g->nama_gudang), 0, 31); // Max 31 chars for sheet title
                $sheets[] = new GudangStockBulanSheet($title, $containersByGudang[$g->id]);
            }
        }

        if ($containersByGudang['none']->count() > 0) {
            $sheets[] = new GudangStockBulanSheet('Tanpa Gudang', $containersByGudang['none']);
        }

        if (count($sheets) === 0) {
            // Fallback empty sheet
            $sheets[] = new GudangStockBulanSheet('Data Kosong', collect());
        }

        return $sheets;
    }
}

class GudangStockBulanSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $title;
    protected $collection;
    protected $no = 1;

    public function __construct($title, $collection)
    {
        $this->title = $title;
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return [
            ['Data Stock Kontainer Gudang: ' . $this->title],
            ['No', 'Nomor Kontainer', 'Ukuran', 'Tipe', 'Tanggal Masuk', 'Asal Transaksi', 'Kategori']
        ];
    }

    public function map($row): array
    {
        return [
            $this->no++,
            $row->no ?? '-',
            $row->ukuran ?? '-',
            $row->tipe ?? '-',
            $row->tanggal_masuk_computed ? Carbon::parse($row->tanggal_masuk_computed)->format('d/m/Y') : '-',
            $row->asal_transaksi_computed ?? '-',
            $row->kategori ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}

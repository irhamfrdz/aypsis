<?php

namespace App\Exports;

use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockAmprahanHistoryExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $id = $this->filters['id'] ?? null;
        $combined = collect();

        // 1. Get Additions (Masuk)
        $additionsQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages']);

        if ($id) {
            $additionsQuery->where('id', $id);
        }

        if (! empty($this->filters['from_date'])) {
            $additionsQuery->where(function ($q) {
                $q->whereDate('tanggal_beli', '>=', $this->filters['from_date'])
                    ->orWhere(function ($sq) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $this->filters['from_date']);
                    });
            });
        }
        if (! empty($this->filters['to_date'])) {
            $additionsQuery->where(function ($q) {
                $q->whereDate('tanggal_beli', '<=', $this->filters['to_date'])
                    ->orWhere(function ($sq) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $this->filters['to_date']);
                    });
            });
        }
        if (! empty($this->filters['lokasi'])) {
            $additionsQuery->where('lokasi', $this->filters['lokasi']);
        }

        // Hide additions if plate filter is active
        if (empty($this->filters['mobil_id'])) {
            $additions = $additionsQuery->get()->map(function ($item) {
                $totalUsage = $item->usages->sum('jumlah');
                $initialStock = $item->jumlah + $totalUsage;

                return (object) [
                    'type' => 'Masuk',
                    'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                    'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-'),
                    'lokasi' => $item->lokasi ?? '-',
                    'jumlah' => $initialStock,
                    'penerima' => '-',
                    'kendaraan' => '-',
                    'truck' => '-',
                    'buntut' => '-',
                    'kapal' => '-',
                    'alat_berat' => '-',
                    'kantor' => '-',
                    'kilometer' => '-',
                    'keterangan' => 'Stock Masuk: '.($item->nomor_bukti ? 'Bukti #'.$item->nomor_bukti : 'Awal'),
                    'harga_satuan' => $item->harga_satuan ?? 0,
                    'oleh' => $item->createdBy->name ?? '-',
                ];
            });
            $combined = $combined->concat($additions);
        }

        // 2. Get Usages (Keluar)
        $usagesQuery = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'chasisBatam', 'kapal', 'alatBerat', 'createdBy']);

        if ($id) {
            $usagesQuery->where('stock_amprahan_id', $id);
        }

        if (! empty($this->filters['from_date'])) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $this->filters['from_date']);
        }
        if (! empty($this->filters['to_date'])) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $this->filters['to_date']);
        }
        if (! empty($this->filters['lokasi'])) {
            $usagesQuery->whereHas('stockAmprahan', function ($q) {
                $q->where('lokasi', $this->filters['lokasi']);
            });
        }
        if (! empty($this->filters['mobil_id'])) {
            $mobilId = $this->filters['mobil_id'];
            $usagesQuery->where(function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
            $buntutVal = '-';
            if ($usage->chasisBatam) {
                $buntutVal = $usage->chasisBatam->kode;
            } elseif ($usage->buntut) {
                $buntutVal = $usage->buntut->no_kir ?? $usage->buntut->nomor_polisi;
            }

            return (object) [
                'type' => 'Keluar',
                'tanggal_raw' => $usage->tanggal_pengambilan,
                'nama_barang' => $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-'),
                'lokasi' => $usage->stockAmprahan->lokasi ?? '-',
                'jumlah' => $usage->jumlah,
                'penerima' => $usage->penerima->nama_lengkap ?? '-',
                'kendaraan' => $usage->kendaraan ? ($usage->kendaraan->nomor_polisi.' - '.$usage->kendaraan->merek) : '-',
                'truck' => $usage->truck ? ($usage->truck->nomor_polisi.' - '.$usage->truck->merek) : '-',
                'buntut' => $buntutVal,
                'kapal' => $usage->kapal->nama_kapal ?? '-',
                'alat_berat' => $usage->alatBerat ? ($usage->alatBerat->kode_alat.' - '.$usage->alatBerat->nama) : '-',
                'kantor' => $usage->kantor ?? '-',
                'kilometer' => $usage->kilometer ?? '-',
                'keterangan' => $usage->keterangan,
                'harga_satuan' => $usage->stockAmprahan->harga_satuan ?? 0,
                'oleh' => $usage->createdBy->name ?? '-',
            ];
        });
        $combined = $combined->concat($usages);

        // Sort by Date Desc
        return $combined->sortByDesc('tanggal_raw')->values()->map(function ($entry, $index) {
            return [
                $index + 1,
                date('d/m/Y', strtotime($entry->tanggal_raw)),
                $entry->type,
                $entry->nama_barang,
                $entry->lokasi,
                $entry->type == 'Masuk' ? $entry->jumlah : -$entry->jumlah,
                $entry->penerima,
                $entry->kendaraan,
                $entry->truck,
                $entry->buntut,
                $entry->kapal,
                $entry->alat_berat,
                $entry->kantor,
                $entry->kilometer,
                $entry->keterangan,
                $entry->harga_satuan,
                ($entry->jumlah) * ($entry->harga_satuan),
                $entry->oleh,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Tipe',
            'Nama Barang',
            'Lokasi',
            'Jumlah',
            'Penerima',
            'Kendaraan',
            'Truck',
            'Buntut',
            'Kapal',
            'Alat Berat',
            'Kantor',
            'KM',
            'Keterangan',
            'Harga Satuan',
            'Total',
            'Oleh',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastCol = 'R';
                $lastRow = $event->sheet->getHighestRow();

                // Style the header row
                $event->sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E40AF'], // Blue-800
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Border all cells
                $event->sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Center alignment for some columns
                $event->sheet->getStyle("A2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

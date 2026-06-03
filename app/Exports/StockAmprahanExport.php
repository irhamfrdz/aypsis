<?php

namespace App\Exports;

use App\Models\StockAmprahan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockAmprahanExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $mobilId = $this->filters['mobil_id'] ?? null;

        $query = StockAmprahan::with(['masterNamaBarangAmprahan', 'vendorAmprahan'])
            ->withSum(['usages as usages_sum_jumlah' => function ($q) use ($mobilId) {
                if ($mobilId) {
                    $q->where('kendaraan_id', $mobilId)
                        ->orWhere('truck_id', $mobilId)
                        ->orWhere('buntut_id', $mobilId);
                }
            }], 'jumlah')
            ->latest();

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%')
                    ->orWhere('nomor_bukti', 'like', '%'.$search.'%')
                    ->orWhereHas('masterNamaBarangAmprahan', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', '%'.$search.'%');
                    });
            });
        }

        if (! empty($this->filters['lokasi'])) {
            if ($this->filters['lokasi'] === 'LAINNYA') {
                $query->where(function ($q) {
                    $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                        ->orWhereNull('lokasi');
                });
            } else {
                $query->where('lokasi', $this->filters['lokasi']);
            }
        }

        if (! empty($this->filters['type_amprahan'])) {
            $query->where('type_amprahan', $this->filters['type_amprahan']);
        }

        if (! empty($this->filters['from_date'])) {
            $query->where(function ($q) {
                $q->whereDate('tanggal_beli', '>=', $this->filters['from_date'])
                    ->orWhere(function ($sq) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $this->filters['from_date']);
                    });
            });
        }

        if (! empty($this->filters['to_date'])) {
            $query->where(function ($q) {
                $q->whereDate('tanggal_beli', '<=', $this->filters['to_date'])
                    ->orWhere(function ($sq) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $this->filters['to_date']);
                    });
            });
        }

        if ($mobilId) {
            $query->whereHas('usages', function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });
        }

        return $query->get()->map(function ($item, $index) {
            $pemakaian = $item->usages_sum_jumlah ?? 0;
            $sisa = $item->jumlah - $pemakaian;

            return [
                $index + 1,
                $item->nomor_bukti ?? '-',
                $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') : '-',
                $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-'),
                $item->type_amprahan ?? '-',
                $item->lokasi ?? '-',
                $item->satuan ?? '-',
                $item->jumlah,
                $pemakaian,
                $sisa,
                $item->harga_satuan,
                $item->harga_satuan * $item->jumlah,
            ];
        });
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'No. Bukti',
            'Tanggal',
            'Nama Barang',
            'Tipe',
            'Lokasi',
            'Satuan',
            'Stock Awal',
            'Pemakaian',
            'Sisa Stock',
            'Harga Satuan',
            'Total Harga',
        ];

        if (! empty($this->filters['mobil_id'])) {
            $mobil = \App\Models\Mobil::find($this->filters['mobil_id']);
            if ($mobil) {
                $headings[8] = 'Pemakaian ('.$mobil->nomor_polisi.')';
            }
        }

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastCol = 'L';
                $lastRow = $event->sheet->getHighestRow();

                // Style the header row
                $event->sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1D4ED8'], // Blue-700
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

                // Number format for currency
                $event->sheet->getStyle("K2:L{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

                // Center alignment for some columns
                $event->sheet->getStyle("A2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle("G2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

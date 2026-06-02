<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportStockAkhirExport implements WithMultipleSheets
{
    protected $stockBans;

    protected $stockBanBatams;

    protected $stockAmprahans;

    public function __construct($stockBans, $stockBanBatams, $stockAmprahans)
    {
        $this->stockBans = $stockBans;
        $this->stockBanBatams = $stockBanBatams;
        $this->stockAmprahans = $stockAmprahans;
    }

    public function sheets(): array
    {
        return [
            new BanStockSheet('Ban Jakarta', $this->stockBans),
            new BanStockSheet('Ban Batam', $this->stockBanBatams),
            new AmprahanStockSheet('Stock Amprahan', $this->stockAmprahans),
        ];
    }
}

class BanStockSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'No',
            'No Seri / Kode',
            'Nama Stock',
            'Merk',
            'Ukuran',
            'Kondisi',
            'Lokasi / Posisi',
            'Harga Beli',
            'Tanggal Masuk',
        ];
    }

    public function map($ban): array
    {
        return [
            $this->no++,
            $ban->nomor_seri ?? '-',
            $ban->namaStockBan?->nama ?? '-',
            $ban->merk ?? '-',
            $ban->ukuran ?? '-',
            ucfirst($ban->kondisi),
            $ban->lokasi ?? '-',
            $ban->harga_beli,
            $ban->tanggal_masuk ? $ban->tanggal_masuk->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}

class AmprahanStockSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'No',
            'No Bukti',
            'Nama Barang',
            'Tipe',
            'Qty Sisa',
            'Satuan',
            'Harga Satuan',
            'Total Nilai',
            'Lokasi',
        ];
    }

    public function map($item): array
    {
        $totalValue = ($item->harga_satuan * $item->jumlah) + $item->adjustment;

        return [
            $this->no++,
            $item->nomor_bukti ?? '-',
            $item->nama_barang ?? ($item->masterNamaBarangAmprahan?->nama_barang ?? '-'),
            $item->type_amprahan ?? '-',
            $item->jumlah,
            $item->satuan ?? '-',
            $item->harga_satuan,
            $totalValue,
            $item->lokasi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '065F46'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\StockBan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OpnameBanLuarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $bulan;
    protected $tahun;
    protected $no = 1;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        // Mengambil semua ban yang statusnya Stok atau Rusak (belum terpakai/terjual)
        // Karena opname fisik biasanya mencari barang yang ADA di gudang saat ini.
        return StockBan::with(['namaStockBan'])
            ->whereIn('status', ['Stok', 'Rusak'])
            ->orderBy('lokasi')
            ->orderBy('kondisi')
            ->get();
    }

    public function title(): string
    {
        return 'Opname ' . $this->bulan . '-' . $this->tahun;
    }

    public function headings(): array
    {
        return [
            ['LEMBAR KERJA OPNAME BAN LUAR'],
            ['Periode: ' . $this->bulan . ' / ' . $this->tahun],
            [],
            [
                'No',
                'No Seri / Kode',
                'Nama Stock',
                'Merk',
                'Ukuran',
                'Kondisi',
                'Lokasi',
                'Status Sistem',
                'Tanggal Masuk'
            ]
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
            strtoupper($ban->kondisi ?? '-'),
            $ban->lokasi ?? '-',
            $ban->status ?? '-',
            $ban->tanggal_masuk ? $ban->tanggal_masuk->format('d/m/Y') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for titles
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        // Style the title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Style the headers (row 4)
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Blue color
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style the rest of the table borders
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 4) {
            $sheet->getStyle('A5:I' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        return [];
    }
}

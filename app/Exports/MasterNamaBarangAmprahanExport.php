<?php

namespace App\Exports;

use App\Models\MasterNamaBarangAmprahan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MasterNamaBarangAmprahanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MasterNamaBarangAmprahan::query();

        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where('nama_barang', 'LIKE', '%' . $searchTerm . '%');
        }

        return $query->latest()->get()->map(function($item, $index) {
            return [
                $index + 1,
                $item->nama_barang,
                $item->status === 'active' ? 'Aktif' : 'Tidak Aktif',
                $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style the header row
                $event->sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'], // Indigo color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Center align the No column
                $event->sheet->getStyle('A2:A' . ($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Center align the Status column
                $event->sheet->getStyle('C2:C' . ($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add border to all cells
                $event->sheet->getStyle('A1:D' . ($event->sheet->getHighestRow()))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);

                // Set row height for header
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}

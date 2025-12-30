<?php

namespace App\Exports;

use App\Models\Pengirim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PengirimExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pengirim::query();

        // Apply search filter if provided
        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_pengirim', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function($pengirim, $index) {
            return [
                $index + 1,
                $pengirim->kode,
                $pengirim->nama_pengirim,
                $pengirim->catatan ?? '-',
                $pengirim->status === 'active' ? 'Aktif' : 'Tidak Aktif',
                $pengirim->created_at ? $pengirim->created_at->format('d/m/Y H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Pengirim',
            'Catatan',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style the header row
                $event->sheet->getStyle('A1:F1')->applyFromArray([
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
                $event->sheet->getStyle('E2:E' . ($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add border to all cells
                $event->sheet->getStyle('A1:F' . ($event->sheet->getHighestRow()))
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

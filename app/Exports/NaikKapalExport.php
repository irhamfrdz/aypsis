<?php

namespace App\Exports;

use App\Models\NaikKapal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class NaikKapalExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $kapalNama;
    protected $noVoyage;

    public function __construct(array $filters = [], $kapalNama = '', $noVoyage = '')
    {
        $this->filters = $filters;
        $this->kapalNama = $kapalNama;
        $this->noVoyage = $noVoyage;
    }

    public function collection()
    {
        // Ambil semua data dari kapal dan voyage yang dipilih tanpa filter
        $query = NaikKapal::with(['prospek'])
            ->where('nama_kapal', $this->kapalNama)
            ->where('no_voyage', $this->noVoyage)
            ->orderBy('created_at', 'desc');

        $naikKapals = $query->get();

        return $naikKapals->map(function($naikKapal, $index) {
            $prospek = $naikKapal->prospek;

            return [
                $index + 1,
                $naikKapal->nomor_kontainer ?: '-',
                $naikKapal->no_seal ?: '-',
                $naikKapal->jenis_barang ?: '-',
                $naikKapal->tipe_kontainer ?: '-',
                $prospek ? $prospek->nama_supir : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Kontainer',
            'Nomor Seal',
            'Jenis Barang',
            'Tipe Kontainer',
            'Nama Supir'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style header row
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '7C3AED'], // Purple-600
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Add borders to all data cells
                $highestRow = $event->sheet->getHighestRow();
                $highestColumn = $event->sheet->getHighestColumn();
                
                $event->sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Center align for specific columns
                $event->sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $event->sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Nomor Seal
                $event->sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tipe Kontainer

                // Set row height for header
                $event->sheet->getRowDimension(1)->setRowHeight(25);

                // Auto-wrap text for jenis barang column
                $event->sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setWrapText(true);
            },
        ];
    }
}

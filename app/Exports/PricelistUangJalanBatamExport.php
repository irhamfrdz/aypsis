<?php

namespace App\Exports;

use App\Models\PricelistUangJalanBatam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PricelistUangJalanBatamExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $search;

    public function __construct($search = '')
    {
        $this->search = $search;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PricelistUangJalanBatam::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('expedisi', 'like', "%{$this->search}%")
                  ->orWhere('ring', 'like', "%{$this->search}%")
                  ->orWhere('size', 'like', "%{$this->search}%")
                  ->orWhere('f_e', 'like', "%{$this->search}%")
                  ->orWhere('status', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('expedisi')
                     ->orderBy('ring')
                     ->orderBy('size')
                     ->orderBy('f_e')
                     ->get();
    }

    /**
     * @var PricelistUangJalanBatam $pricelist
     */
    public function map($pricelist): array
    {
        return [
            $pricelist->expedisi,
            $pricelist->ring,
            $pricelist->rute,
            $pricelist->size,
            $pricelist->f_e,
            $pricelist->tarif,
            $pricelist->tarif_base,
            $pricelist->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Expedisi',
            'Ring',
            'Rute',
            'Size',
            'F/E',
            'Tarif',
            'Tarif Base',
            'Status',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Expedisi
            'B' => 10, // Ring
            'C' => 30, // Rute
            'D' => 12, // Size
            'E' => 12, // F/E
            'F' => 15, // Tarif
            'G' => 15, // Tarif Base
            'H' => 15, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo color
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

        // Auto filter for headings
        $sheet->setAutoFilter('A1:H1');

        return [];
    }
}

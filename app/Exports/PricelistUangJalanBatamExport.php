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
                  ->orWhere('status', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('expedisi')
                     ->orderBy('ring')
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
            $pricelist->tarif_20ft_full,
            $pricelist->tarif_20ft_empty,
            $pricelist->tarif_40ft_full,
            $pricelist->tarif_40ft_empty,
            $pricelist->tarif_antarlokasi_20ft,
            $pricelist->tarif_antarlokasi_40ft,
            $pricelist->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Expedisi',
            'Ring',
            'Tarif 20FT Full',
            'Tarif 20FT Empty',
            'Tarif 40FT Full',
            'Tarif 40FT Empty',
            'Tarif Antarlokasi 20FT',
            'Tarif Antarlokasi 40FT',
            'Status',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Expedisi
            'B' => 10, // Ring
            'C' => 18, // Tarif 20FT Full
            'D' => 18, // Tarif 20FT Empty
            'E' => 18, // Tarif 40FT Full
            'F' => 18, // Tarif 40FT Empty
            'G' => 20, // Tarif Antarlokasi 20FT
            'H' => 20, // Tarif Antarlokasi 40FT
            'I' => 15, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:I1')->applyFromArray([
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
        $sheet->setAutoFilter('A1:I1');

        return [];
    }
}

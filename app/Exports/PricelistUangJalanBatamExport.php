<?php

namespace App\Exports;

use App\Models\PricelistUangJalanBatam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PricelistUangJalanBatamExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    protected $search;

    protected $kelolaBbmId;

    public function __construct($search = '', $kelolaBbmId = '')
    {
        $this->search = $search;
        $this->kelolaBbmId = $kelolaBbmId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PricelistUangJalanBatam::query();

        if ($this->kelolaBbmId === 'base') {
            $query->whereNull('kelola_bbm_id');
        } elseif ($this->kelolaBbmId !== '') {
            $query->where('kelola_bbm_id', $this->kelolaBbmId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('expedisi', 'like', "%{$this->search}%")
                    ->orWhere('ring', 'like', "%{$this->search}%")
                    ->orWhere('wilayah', 'like', "%{$this->search}%")
                    ->orWhere('status', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('expedisi')
            ->orderBy('ring')
            ->get();
    }

    /**
     * @var PricelistUangJalanBatam
     */
    public function map($pricelist): array
    {
        return [
            $pricelist->expedisi,
            $pricelist->ring,
            $pricelist->wilayah,
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
            'Wilayah',
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
            'C' => 15, // Wilayah
            'D' => 18, // Tarif 20FT Full
            'E' => 18, // Tarif 20FT Empty
            'F' => 18, // Tarif 40FT Full
            'G' => 18, // Tarif 40FT Empty
            'H' => 20, // Tarif Antarlokasi 20FT
            'I' => 20, // Tarif Antarlokasi 40FT
            'J' => 15, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:J1')->applyFromArray([
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
        $sheet->setAutoFilter('A1:J1');

        return [];
    }
}

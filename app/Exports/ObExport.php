<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class ObExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;
    protected $namaKapal;
    protected $noVoyage;

    public function __construct($data, $namaKapal, $noVoyage)
    {
        $this->data = $data;
        $this->namaKapal = $namaKapal;
        $this->noVoyage = $noVoyage;
    }

    public function collection()
    {
        return $this->data->map(function($item, $index) {
            // Determine if it is Bl or NaikKapal
            // Check based on class or available attributes
            $isBl = $item instanceof \App\Models\Bl;
            
            if ($isBl) {
                return [
                    $index + 1,
                    $item->nomor_bl ?: '-',
                    $item->nomor_kontainer ?: '-',
                    $item->no_seal ?: '-',
                    $item->nama_barang ?: '-',
                    $item->asal_kontainer ?: '-',
                    $item->ke ?: '-',
                    $item->tipe_kontainer ?: '-',
                    $item->size_kontainer ? $item->size_kontainer . ' Feet' : '-',
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                    $item->sudah_ob ? 'Sudah OB' : 'Belum OB',
                    $item->tanggal_ob ? Carbon::parse($item->tanggal_ob)->format('d/m/Y H:i') : '-',
                    $item->supir ? ($item->supir->nama_panggilan ?? $item->supir->nama_lengkap ?? '-') : '-',
                    $this->namaKapal,
                    $this->noVoyage
                ];
            } else {
                // NaikKapal
                return [
                    $index + 1,
                    '-', // No BL for NaikKapal
                    $item->nomor_kontainer ?: '-',
                    $item->no_seal ?: '-',
                    $item->jenis_barang ?: '-',
                    $item->asal_kontainer ?: '-',
                    $item->ke ?: '-',
                    $item->tipe_kontainer ?: '-',
                    $item->size_kontainer ? $item->size_kontainer . ' Feet' : '-',
                    $item->tanggal_muat ? Carbon::parse($item->tanggal_muat)->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y H:i') : '-'),
                    $item->sudah_ob ? 'Sudah OB' : 'Belum OB',
                    $item->tanggal_ob ? Carbon::parse($item->tanggal_ob)->format('d/m/Y H:i') : '-',
                    $item->supir ? ($item->supir->nama_panggilan ?? $item->supir->nama_lengkap ?? '-') : '-',
                    $this->namaKapal,
                    $this->noVoyage
                ];
            }
        });
    }

    public function headings(): array
    {
        return [
            'No', 'No. BL', 'No. Kontainer', 'No. Seal', 'Nama Barang', 
            'Asal Kontainer', 'Ke', 'Tipe', 'Size', 
            'Tanggal Dibuat', 'Status OB', 'Tanggal OB', 'Supir', 
            'Nama Kapal', 'No. Voyage'
        ];
    }
    
    public function title(): string
    {
        return 'OB Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style header row
                $event->sheet->getStyle('A1:O1')->applyFromArray([
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
                $highestColumn = 'O'; // Fixed column to O
                
                if ($highestRow > 1) {
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
                    $event->sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tipe
                    $event->sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Size
                    
                    // Set row height for header
                    $event->sheet->getRowDimension(1)->setRowHeight(25);
                }
            },
        ];
    }
}

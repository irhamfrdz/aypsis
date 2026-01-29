<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class SuratJalanBongkaranTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $mode;
    protected $kapal;
    protected $voyage;

    public function __construct($data, $mode = 'manifest', $kapal = '', $voyage = '')
    {
        $this->data = $data;
        $this->mode = $mode;
        $this->kapal = $kapal;
        $this->voyage = $voyage;
    }

    public function collection()
    {
        return $this->data->map(function($item, $index) {
            if ($this->mode === 'surat_jalan') {
                $feets = $item->size ?: '-';
                if ($feets !== '-' && !str_contains($feets, '"')) {
                    $feets .= '"';
                }

                return [
                    $index + 1,
                    $item->manifest->nomor_urut ?? '-',
                    ($item->no_kontainer ?: '-') . ($item->no_seal ? ' / ' . $item->no_seal : ''),
                    $item->penerima ?: '-',
                    $feets,
                    '', // TGL KRM
                    '', // TR
                    '', // TGL KBL
                    '', // TR
                    '', // AYP
                    '', // PR
                    '', // 20
                    '', // 40
                    '', // SPPB
                    $item->jenis_barang ?: '-',
                    $item->tujuan_alamat ?: '-',
                ];
            } else {
                $feets = $item->size_kontainer ?: '-';
                if ($feets !== '-' && !str_contains($feets, '"')) {
                    $feets .= '"';
                }

                return [
                    $index + 1,
                    $item->nomor_urut ?? '-',
                    ($item->nomor_kontainer ?: '-') . ($item->no_seal ? ' / ' . $item->no_seal : ''),
                    $item->penerima ?: '-',
                    $feets,
                    '', // TGL KRM
                    '', // TR
                    '', // TGL KBL
                    '', // TR
                    '', // AYP
                    '', // PR
                    '', // 20
                    '', // 40
                    '', // SPPB
                    $item->nama_barang ?: '-',
                    $item->alamat_pengiriman ?: '-',
                ];
            }
        });
    }

    public function headings(): array
    {
        $date = Carbon::now()->format('d-M-y');
        return [
            [
                strtoupper($this->kapal), 
                '', '', '', '', '', '', 
                'VOY.' . strtoupper($this->voyage ?? ''), 
                '', '', '', '', '', '', '', 
                $date
            ],
            [
                'NO', 
                'BL', 
                'MARK AND NUMBERS', 
                'RELASI', 
                'FEET', 
                'TGL KRM', 
                'TR', 
                'TGL KBL', 
                'TR', 
                'CHASIS', 
                '', '', '', 
                'SPPB', 
                'JENIS BARANG', 
                'ALAMAT'
            ],
            [
                '', '', '', '', '', '', '', '', '', 
                'AYP', 
                'PR', 
                '20', 
                '40', 
                '', '', ''
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Style Top Header (Row 1)
                $sheet->getStyle('A1:P1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1')->getFont()->setUnderline(true);
                // Align Voyage to center and Date to right
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('P1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Table Headings (Row 2 & 3)
                $headerRange = 'A2:P3';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Merge CHASIS in Row 2
                $sheet->mergeCells('J2:M2');
                
                // Merge single-row headers across Row 2 and 3
                $colsToMerge = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'N', 'O', 'P'];
                foreach ($colsToMerge as $col) {
                    $sheet->mergeCells($col . '2:' . $col . '3');
                }

                // Table Borders
                $lastRow = count($this->data) + 3;
                if ($lastRow > 3) {
                    $sheet->getStyle('A2:P' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    
                    // Center align some data columns
                    $sheet->getStyle('A4:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E4:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Auto-sizing or fixed widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(8);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(8);
                $sheet->getColumnDimension('O')->setWidth(35);
                $sheet->getColumnDimension('P')->setWidth(40);
                
                // Set row height for headers
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
            }
        ];
    }
}

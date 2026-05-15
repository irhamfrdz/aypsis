<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;

class PranotaLemburSingleExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $pranota;

    public function __construct($pranota)
    {
        $this->pranota = $pranota;
    }

    public function collection()
    {
        $muatItems = $this->pranota->suratJalans->map(function($sj) {
            return [
                'no' => null,
                'tanggal' => $sj->tandaTerima ? Carbon::parse($sj->tandaTerima->tanggal)->format('d/M/Y') : '-',
                'no_sj' => $sj->no_surat_jalan,
                'pengirim' => $sj->pengirim ?: '-',
                'supir' => $sj->pivot->supir,
                'nik' => $sj->supir_nik ?: '-',
                'no_plat' => $sj->pivot->no_plat,
                'type' => 'Muat',
                'biaya_lembur' => (float)$sj->pivot->biaya_lembur,
                'biaya_nginap' => (float)$sj->pivot->biaya_nginap,
                'total' => (float)$sj->pivot->total_biaya,
            ];
        });

        $bongkaranItems = $this->pranota->suratJalanBongkarans->map(function($sjb) {
            return [
                'no' => null,
                'tanggal' => $sjb->tandaTerima ? Carbon::parse($sjb->tandaTerima->tanggal_tanda_terima)->format('d/M/Y') : '-',
                'no_sj' => $sjb->nomor_surat_jalan,
                'pengirim' => $sjb->pengirim ?: '-',
                'supir' => $sjb->pivot->supir,
                'nik' => $sjb->supir_nik ?: '-',
                'no_plat' => $sjb->pivot->no_plat,
                'type' => 'Bongkaran',
                'biaya_lembur' => (float)$sjb->pivot->biaya_lembur,
                'biaya_nginap' => (float)$sjb->pivot->biaya_nginap,
                'total' => (float)$sjb->pivot->total_biaya,
            ];
        });

        $allItems = $muatItems->concat($bongkaranItems)->sortBy(function($item) {
            return $item['tanggal'] == '-' ? '9999-99-99' : Carbon::createFromFormat('d/M/Y', $item['tanggal'])->format('Y-m-d');
        })->values();

        return $allItems->map(function($item, $index) {
            $item['no'] = $index + 1;
            return $item;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal TT',
            'No Surat Jalan',
            'Pengirim',
            'Supir',
            'NIK',
            'No Plat',
            'Tipe',
            'Biaya Lembur',
            'Biaya Nginap',
            'Total',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'K';
                $headerRow = 6;
                $dataStartRow = 7;

                // Set fixed width for No column
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);

                // Insert title rows
                $sheet->insertNewRowBefore(1, 5);
                $sheet->setCellValue('A1', 'PRANOTA LEMBUR / NGINAP');
                $sheet->setCellValue('A2', 'Nomor: ' . $this->pranota->nomor_pranota);
                $sheet->setCellValue('A3', 'Tanggal: ' . $this->pranota->tanggal_pranota->format('d/m/Y'));
                if ($this->pranota->catatan) {
                    $sheet->setCellValue('A4', 'Catatan: ' . $this->pranota->catatan);
                }

                // Merge and style titles
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A2:A3")->getFont()->setBold(true);

                // Style Header
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E40AF'], // Blue 800
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Find Last Data Row and Add Summary Rows
                $lastDataRow = $sheet->getHighestRow();
                $currentRow = $lastDataRow + 1;

                // Subtotal
                $sheet->setCellValue('J' . $currentRow, 'Subtotal');
                $sheet->setCellValue('K' . $currentRow, "=SUM(K{$dataStartRow}:K{$lastDataRow})");
                $sheet->getStyle("J{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                $currentRow++;

                // Adjustment
                if ($this->pranota->adjustment != 0) {
                    $label = 'Adjustment' . ($this->pranota->alasan_adjustment ? ' ('.$this->pranota->alasan_adjustment.')' : '');
                    $sheet->setCellValue('J' . $currentRow, $label);
                    $sheet->setCellValue('K' . $currentRow, (float)$this->pranota->adjustment);
                    $sheet->getStyle("J{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                    $currentRow++;
                }

                // Total
                $sheet->setCellValue('J' . $currentRow, 'TOTAL');
                $sheet->setCellValue('K' . $currentRow, (float)$this->pranota->total_setelah_adjustment);
                $sheet->getStyle("J{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FCD34D'], // Yellow 300
                    ],
                ]);

                // Table borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$currentRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Right align numeric columns
                $sheet->getStyle("I{$dataStartRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}

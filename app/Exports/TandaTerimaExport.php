<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TandaTerimaExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected $tandaTerimaIds;

    public function __construct(array $tandaTerimaIds)
    {
        $this->tandaTerimaIds = $tandaTerimaIds;
    }

    /**
     * Get container type from database
     */
    private function getContainerType(?string $noKontainer): string
    {
        if (empty($noKontainer) || strtoupper($noKontainer) === 'CARGO') {
            return 'DRY';
        }

        // Try to find in kontainers table first
        $kontainer = Kontainer::where('nomor_seri_gabungan', $noKontainer)
            ->orWhere(function($q) use ($noKontainer) {
                $q->whereRaw("CONCAT(awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer) = ?", [$noKontainer]);
            })
            ->first();

        if ($kontainer && $kontainer->tipe_kontainer) {
            $tipeKontainer = strtoupper($kontainer->tipe_kontainer);
        } else {
            // If not found, try stock_kontainers table
            $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $noKontainer)
                ->orWhere(function($q) use ($noKontainer) {
                    $q->whereRaw("CONCAT(awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer) = ?", [$noKontainer]);
                })
                ->first();

            $tipeKontainer = $stockKontainer ? strtoupper($stockKontainer->tipe_kontainer) : '';
        }

        // Normalize tipe kontainer
        if (str_contains($tipeKontainer, 'DRY')) {
            return 'DRY';
        } elseif (str_contains($tipeKontainer, 'REEFER')) {
            return 'REEFER';
        } elseif (str_contains($tipeKontainer, 'HC')) {
            return 'HC';
        } elseif (str_contains($tipeKontainer, 'FLAT')) {
            return 'FLAT RACK';
        } elseif (str_contains($tipeKontainer, 'OPEN')) {
            return 'OPEN TOP';
        }

        return 'DRY'; // Default fallback
    }

    /**
     * Get status based on kegiatan (F for Full, E for Empty)
     */
    private function getStatus(?string $kegiatan): string
    {
        if (!$kegiatan) {
            return '';
        }

        $kegiatan = strtolower($kegiatan);
        if (str_contains($kegiatan, 'isi') || str_contains($kegiatan, 'full')) {
            return 'F';
        } elseif (str_contains($kegiatan, 'kosong') || str_contains($kegiatan, 'empty')) {
            return 'E';
        }

        return '';
    }

    /**
     * Get POD based on tujuan pengiriman
     */
    private function getPod(?string $tujuanPengiriman): string
    {
        if (!$tujuanPengiriman) {
            return '';
        }

        $tujuan = strtolower($tujuanPengiriman);
        if (str_contains($tujuan, 'batam')) {
            return 'IDBTM';
        } elseif (str_contains($tujuan, 'pinang')) {
            return 'IDKID';
        }

        return '';
    }

    /**
     * Format expired date to dd/mm/yy format
     */
    private function formatExpiredDate(?\Carbon\Carbon $expiredDate): string
    {
        return $expiredDate ? $expiredDate->format('d/m/y') : '';
    }

    public function collection()
    {
        // Get tanda terimas with relations, grouped by nomor_ro
        $tandaTerimas = TandaTerima::with(['suratJalan'])
            ->whereIn('id', $this->tandaTerimaIds)
            ->orderBy('nomor_ro')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('nomor_ro');

        $rows = collect();

        // Process each RO group
        foreach ($tandaTerimas as $nomorRo => $groupedTandaTerimas) {
            // Add RO header row
            $rows->push([
                'RO NOMOR :',
                ($nomorRo ?: 'N/A'),
                '', '', '', '', '', '', ''
            ]);

            // Add column headers
            $rows->push([
                'CONTAINER_NO',
                'SIZE',
                'TIPE',
                'STATUS',
                'BERAT',
                'EXPIRED_DATE',
                'CONSIGNEE',
                'REMARK',
                'POD'
            ]);

            // Add data for this RO
            foreach ($groupedTandaTerimas as $tandaTerima) {
                $rows->push([
                    $tandaTerima->no_kontainer ?? '',           // CONTAINER_NO
                    $tandaTerima->size ?? '',                   // SIZE
                    $this->getContainerType($tandaTerima->no_kontainer), // TIPE
                    $this->getStatus($tandaTerima->kegiatan),   // STATUS (F/E)
                    15000,                                       // BERAT fixed
                    $this->formatExpiredDate($tandaTerima->expired_date), // EXPIRED_DATE
                    '02522267',                                 // CONSIGNEE (fixed value)
                    '',                                         // REMARK (kosong)
                    $this->getPod($tandaTerima->tujuan_pengiriman) // POD (IDBTM/IDKID)
                ]);
            }

            // Add empty row between RO groups
            $rows->push(['', '', '', '', '', '', '', '', '']);
        }

        return $rows;
    }

    /**
     * Register events to style the sheet after it's created
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'], // Yellow header
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $roStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6E6FA'], // Light lavender
                    ],
                ];

                // Apply styles and merge RO rows, style header rows
                for ($row = 1; $row <= $highestRow; $row++) {
                    $firstCell = (string) $sheet->getCell('A' . $row)->getValue();
                    if ($firstCell !== null && str_starts_with(trim($firstCell), 'RO NOMOR :')) {
                        // Style the label (A) and the RO value (B) cells separately
                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($roStyle);
                    }

                    // If this row contains the column headers (CONTAINER_NO in column A), style it yellow
                    if (trim(strtoupper($firstCell)) === 'CONTAINER_NO') {
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($headerStyle);
                        // Set wrap text and center alignment for header
                        $sheet->getStyle("A{$row}:I{$row}")->getAlignment()->setWrapText(true);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                    }
                }

                // Optionally set column widths minimum or rely on ShouldAutoSize
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
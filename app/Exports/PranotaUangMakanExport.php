<?php

namespace App\Exports;

use App\Models\PranotaUangMakan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PranotaUangMakanExport extends DefaultValueBinder implements FromArray, WithStrictNullComparison, WithColumnFormatting, WithCustomValueBinder, WithEvents, WithTitle
{
    public function __construct(protected PranotaUangMakan $pranota) {}

    public function title(): string
    {
        return 'Pranota Uang Makan';
    }

    public function bindValue(Cell $cell, $value)
    {
        // Keep employee identifiers and all text literal, including text starting with '='.
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return ['B' => '@', 'E' => '@', 'G' => '#,##0', 'H' => '#,##0', 'I' => '#,##0', 'J' => '#,##0'];
    }

    public function array(): array
    {
        $this->pranota->loadMissing('details.karyawan');
        $rows = [
            ['PERINCIAN UANG MAKAN'],
            ['Nomor Pranota: '.$this->pranota->nomor_pranota],
            ['Tanggal: '.($this->pranota->tanggal_pranota?->format('d/m/Y') ?? '-')],
            // An empty array is removed by Laravel Excel's row flattener.
            array_fill(0, 11, ''),
            ['NO', 'NIK', 'NAMA KARYAWAN', 'DEPARTEMEN', 'NO REKENING', 'KEHADIRAN', 'NOMINAL / HARI', 'NOMINAL AWAL', 'ADJUSTMENT', 'TOTAL AKHIR', 'CATATAN'],
        ];
        $details = $this->pranota->details->sortBy(fn ($detail) => $detail->karyawan?->nama_lengkap ?? '')->values();
        foreach ($details as $index => $detail) {
            preg_match('/\d+/', (string) $detail->kehadiran, $matches);
            $hadir = (int) ($matches[0] ?? 0);
            $rows[] = [
                $index + 1,
                (string) ($detail->karyawan?->nik ?? ''),
                $detail->karyawan?->nama_lengkap ?? 'Karyawan tidak tersedia',
                $detail->karyawan?->departemen ?? '',
                (string) ($detail->karyawan?->akun_bank ?? ''),
                $hadir,
                $hadir > 0 ? round((float) $detail->nominal_awal / $hadir, 2) : 0,
                (float) $detail->nominal_awal,
                (float) $detail->adjustment,
                (float) $detail->total_akhir,
                (string) ($detail->catatan ?? ''),
            ];
        }
        $rows[] = ['', '', 'TOTAL', '', '', $details->sum(function ($detail) {
            preg_match('/\d+/', (string) $detail->kehadiran, $matches);

            return (int) ($matches[0] ?? 0);
        }), '', (float) $details->sum('nominal_awal'), (float) $details->sum('adjustment'), (float) $details->sum('total_akhir'), ''];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $lastRow = $sheet->getHighestRow();
            $sheet->setShowGridlines(false);
            $sheet->getStyle('A1:K'.$lastRow)->getFont()->setName('Calibri')->setSize(11)->getColor()->setARGB('FF334155');
            $sheet->getStyle('A1:K'.$lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            foreach ([1, 2, 3] as $row) {
                $sheet->mergeCells("A{$row}:K{$row}");
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF0F172A');
            $sheet->getRowDimension(1)->setRowHeight(36);
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getRowDimension(3)->setRowHeight(22);
            $sheet->getRowDimension(4)->setRowHeight(12);
            $sheet->getStyle('A5:K'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getStyle('A5:K5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle('A5:K5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A5F');
            for ($row = 6; $row < $lastRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(-1);
                if ($row % 2 === 1) {
                    $sheet->getStyle("A{$row}:K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
                foreach (['B', 'E'] as $column) {
                    $sheet->getCell($column.$row)->getIgnoredErrors()->setNumberStoredAsText(true);
                }
            }
            $sheet->mergeCells('C'.$lastRow.':E'.$lastRow);
            $sheet->getStyle('A'.$lastRow.':K'.$lastRow)->getFont()->setBold(true);
            $sheet->getStyle('A'.$lastRow.':K'.$lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($lastRow)->setRowHeight(28);
            $sheet->getStyle('A5:K5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
            $sheet->getRowDimension(5)->setRowHeight(36);
            foreach (['A' => 7, 'B' => 12, 'C' => 34, 'D' => 28, 'E' => 22, 'F' => 14, 'G' => 18, 'H' => 20, 'I' => 18, 'J' => 20, 'K' => 36] as $column => $width) {
                $sheet->getColumnDimension($column)->setAutoSize(false)->setWidth($width);
            }
            $sheet->getStyle('A6:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F6:F'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G6:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            foreach (['C', 'D', 'K'] as $column) {
                $sheet->getStyle($column.'6:'.$column.$lastRow)->getAlignment()->setWrapText(true);
            }
            $sheet->getStyle('G6:J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0);"-"');
            $sheet->getStyle('J6:J'.$lastRow)->getFont()->setBold(true)->getColor()->setARGB('FF166534');
            $sheet->freezePane('C6');
            $sheet->setAutoFilter('A5:K'.max(5, $lastRow - 1));
            $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 5);
            $sheet->getPageSetup()->setPrintArea('A1:K'.$lastRow);
        }];
    }
}

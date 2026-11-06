<?php

namespace App\Exports;

use App\Models\PranotaBpjsDetail;
use App\Models\PranotaBpjsHeader;
use App\Services\BpjsJournalAccount;
use App\Services\BpjsJournalAmounts;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PranotaBpjsTemplateExport implements Responsable, WithColumnFormatting
{
    private Worksheet $template;

    private array $rows = [];

    private string $fileName;

    public function __construct(private readonly PranotaBpjsHeader $pranota)
    {
        $path = base_path('BPJS 26 11.xlsx');
        $spreadsheet = IOFactory::load($path);
        $this->template = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getSheet(0);
        $this->template->setTitle('Rincian BPJS');
        $this->fileName = 'BPJS_'.$pranota->periode_tahun.'_'.str_pad($pranota->periode_bulan, 2, '0', STR_PAD_LEFT).'_'.$pranota->nomor_pranota.'.xlsx';

        foreach ($this->pranota->details as $index => $detail) {
            $employee = $detail->karyawan_pranota;
            $groups = [];
            if (trim((string) $employee?->group_bp_jamsostek) !== '') $groups[] = 'jamsostek';
            if (trim((string) $employee?->group_jkn) !== '') $groups[] = 'jkn';
            if (count($groups) < 2) {
                $this->rows[] = $this->mapDetail($detail, $index + 1);
                continue;
            }
            foreach ($groups as $group) {
                $this->rows[] = $this->mapDetail($detail, $index + 1, $group);
            }
        }
    }

    public function toResponse($request): BinaryFileResponse
    {
        $this->writeRowsIntoTemplate();
        $temporaryPath = tempnam(sys_get_temp_dir(), 'bpjs-export-');
        $writer = IOFactory::createWriter($this->template->getParent(), 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save($temporaryPath);

        return response()->download($temporaryPath, $this->fileName)->deleteFileAfterSend(true);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => 'dd/mm/yyyy',
            'G' => 'dd/mm/yyyy',
            'AL' => NumberFormat::FORMAT_TEXT,
        ];
    }

    private function writeRowsIntoTemplate(): void
    {
        $sheet = $this->template;
        foreach (['BC' => 'AKUN JURNAL', 'BD' => 'TUNJANGAN JURNAL', 'BE' => 'BIAYA JURNAL', 'BF' => 'HUTANG JURNAL', 'BG' => 'GROUP JKN', 'BH' => 'GROUP BP JAMSOSTEK', 'BI' => 'JAMSOSTEK GABUNGAN DATA LAMA'] as $column => $heading) {
            $sheet->setCellValue($column.'1', $heading);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $firstDataRow = 2;
        $templateGrandTotalRow = 0;
        for ($row = $firstDataRow; $row <= $sheet->getHighestDataRow(); $row++) {
            if ($sheet->getCell('C'.$row)->getValue() === 'Grand Total') {
                $templateGrandTotalRow = $row;
                break;
            }
        }
        if ($templateGrandTotalRow === 0) {
            throw new \RuntimeException('Baris Grand Total pada template BPJS tidak ditemukan.');
        }

        $templateDataCapacity = $templateGrandTotalRow - $firstDataRow - 1;
        $rowCount = count($this->rows);
        if ($rowCount < $templateDataCapacity) {
            // Pertahankan satu baris kosong sebelum ringkasan, seperti file contoh.
            $sheet->removeRow($firstDataRow + $rowCount, $templateDataCapacity - $rowCount);
        } elseif ($rowCount > $templateDataCapacity) {
            $extraRows = $rowCount - $templateDataCapacity;
            $sheet->insertNewRowBefore($templateGrandTotalRow - 1, $extraRows);
            for ($row = $templateGrandTotalRow - 1; $row < $templateGrandTotalRow - 1 + $extraRows; $row++) {
                for ($columnIndex = 1; $columnIndex <= 54; $columnIndex++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->duplicateStyle($sheet->getStyle($column.($templateGrandTotalRow - 2)), $column.$row);
                }
            }
        }

        foreach ($this->rows as $index => $row) {
            $excelRow = $firstDataRow + $index;
            foreach ($row as $columnIndex => $value) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                if (in_array($column, ['B', 'D', 'E', 'AL'], true) && $value !== null && $value !== '') {
                    $sheet->setCellValueExplicit($column.$excelRow, (string) $value, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($column.$excelRow, $value);
                }
            }
        }

        $lastDataRow = $firstDataRow + $rowCount - 1;
        if ($rowCount > 0) {
            $sheet->getStyle('L2:L'.$lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('BD2:BF'.$lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('BI2:BI'.$lastDataRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $this->writeDynamicSummary($sheet, $firstDataRow, $lastDataRow);
        $sheet->setAutoFilter('A1:BI'.max(1, $lastDataRow));
        $sheet->freezePane('A2');
        $sheet->setSelectedCell('A1');
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
        $sheet->setTitle('Rincian BPJS');
    }

    private function writeDynamicSummary(Worksheet $sheet, int $firstDataRow, int $lastDataRow): void
    {
        $grandTotalRow = $lastDataRow + 2;
        $roundingRow = $grandTotalRow + 1;
        $summaryHeaderRow = $grandTotalRow + 3;
        $groupTotalRow = $grandTotalRow + 18;
        $groupCombinedRow = $grandTotalRow + 19;
        $hasData = $lastDataRow >= $firstDataRow;

        for ($columnIndex = $this->columnIndex('L'); $columnIndex <= $this->columnIndex('AU'); $columnIndex++) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            if ($column === 'AL') {
                continue;
            }
            $sheet->setCellValue($column.$grandTotalRow, $hasData
                ? '=SUBTOTAL(109,'.$column.$firstDataRow.':'.$column.$lastDataRow.')' : '=0');
        }
        foreach (['S', 'X', 'AA'] as $column) {
            $sheet->setCellValue($column.$roundingRow, '=ROUNDDOWN('.$column.$grandTotalRow.',0)');
        }

        $groupRows = range($summaryHeaderRow + 1, $groupTotalRow - 2);
        $groupKey = fn (string $label): string => mb_strtoupper(trim($label));
        $templateGroups = [];
        $emptyRows = [];
        foreach ($groupRows as $row) {
            $label = trim((string) $sheet->getCell('AE'.$row)->getValue());
            if ($label === '') {
                $emptyRows[] = $row;
            } else {
                $templateGroups[$groupKey($label)] = $row;
            }
        }
        $exportGroups = [];
        foreach ($this->rows as $dataRow) {
            $label = trim((string) ($dataRow[$this->columnIndex('H') - 1] ?? ''));
            if ($label !== '') {
                $exportGroups[$groupKey($label)] = $label;
            }
        }
        foreach ($exportGroups as $key => $group) {
            if (isset($templateGroups[$key])) {
                continue;
            }
            $row = array_shift($emptyRows);
            if ($row === null) {
                foreach ($templateGroups as $templateGroup => $candidate) {
                    if (! isset($exportGroups[$templateGroup])) {
                        $row = $candidate;
                        unset($templateGroups[$templateGroup]);
                        break;
                    }
                }
            }
            if ($row === null) {
                throw new \RuntimeException('Jumlah grup BPJS melebihi kapasitas ringkasan pada template Excel.');
            }
            $sheet->setCellValue('AE'.$row, $group);
            $templateGroups[$key] = $row;
        }

        foreach ($groupRows as $row) {
            if (trim((string) $sheet->getCell('AE'.$row)->getValue()) === '') {
                continue;
            }
            foreach (['AF' => ['AE', 'AF', 'AG'], 'AG' => ['AJ'], 'AH' => ['AH', 'AI', 'AK']] as $target => $sourceColumns) {
                $formula = $hasData
                    ? '='.$this->visibleGroupSum($sourceColumns, $firstDataRow, $lastDataRow, $row)
                    : '=0';
                $sheet->setCellValue($target.$row, $formula);
            }
        }
        foreach (['AF', 'AG', 'AH'] as $column) {
            $sheet->setCellValue($column.$groupTotalRow,
                '=SUM('.$column.($summaryHeaderRow + 1).':'.$column.($groupTotalRow - 2).')');
        }
        $sheet->setCellValue('AI'.$groupTotalRow, '=SUM(AF'.$groupTotalRow.':AH'.$groupTotalRow.')');
        $sheet->setCellValue('AF'.$groupCombinedRow, '=AF'.$groupTotalRow.'+AG'.$groupTotalRow);

        // Angka contoh di bagian catatan template tidak berasal dari data pranota.
        foreach ([$grandTotalRow + 7, $grandTotalRow + 8, $grandTotalRow + 9,
            $grandTotalRow + 10, $grandTotalRow + 11] as $row) {
            $sheet->setCellValue('L'.$row, null);
        }
    }

    private function visibleGroupSum(array $columns, int $firstDataRow, int $lastDataRow, int $summaryRow): string
    {
        $groupCondition = '--($H$'.$firstDataRow.':$H$'.$lastDataRow.'=$AE$'.$summaryRow.')';
        return implode('+', array_map(function (string $column) use ($firstDataRow, $lastDataRow, $groupCondition): string {
            $firstCell = $column.'$'.$firstDataRow;
            $range = $column.'$'.$firstDataRow.':'.$column.'$'.$lastDataRow;
            return 'SUMPRODUCT(SUBTOTAL(109,OFFSET('.$firstCell.',ROW('.$range.')-ROW('.$firstCell.'),0,1)),'.$groupCondition.')';
        }, $columns));
    }

    private function columnIndex(string $column): int
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
    }

    private function mapDetail(PranotaBpjsDetail $detail, int $number, ?string $component = null): array
    {
        $employee = $detail->karyawan_pranota;
        $parts = $detail->rincian_bpjs ?? [];
        $summary = $detail->snapshot_bpjs['ringkasan'] ?? [];
        $jknGroup = $employee?->group_jkn;
        $jamsostekGroup = $employee?->group_bp_jamsostek;
        $jknType = $employee?->tipe_jkn;
        $jamsostekType = $employee?->tipe_bp_jamsostek;

        $value = fn (string $field): float => (float) ($parts[$field] ?? 0);
        $summaryValue = fn (string $key, float $fallback = 0): float => (float) ($summary[$key] ?? $detail->tunjangan_manual[$key] ?? $fallback);

        $ppu = ['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang'];
        $bpu = ['jkk_hutang', 'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jht_biaya', 'noncrew_jht_hutang', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'];
        $totalPpu = array_sum(array_map($value, $ppu));
        $totalBpu = array_sum(array_map($value, $bpu));
        $jkn4 = (float) $detail->bpjs_kesehatan;
        $jkn1 = (float) $detail->bpjs_ketenagakerjaan;

        $row = array_fill(0, 61, null);
        $set = function (string $column, mixed $cell) use (&$row): void {
            $row[$this->columnIndex($column) - 1] = $cell;
        };
        $date = fn ($input) => $input ? (strtotime((string) $input) ?: null) : null;

        $set('A', 1);
        $set('B', (string) ($employee?->nik ?? ''));
        $set('C', $employee?->nama_lengkap);
        $set('D', (string) ($employee?->no_ketenagakerjaan ?? $employee?->no_bpjs ?? $employee?->jkn ?? ''));
        $set('E', (string) ($employee?->nik_ktp ?? $employee?->ktp ?? ''));
        $set('F', $date($employee?->tanggal_lahir));
        $set('G', $date($employee?->tanggal_masuk));
        $set('H', $component === 'jkn' ? $jknType : ($jamsostekType ?: $jknType));
        $set('I', $component === 'jkn' ? $jknGroup : ($jamsostekGroup ?: $jknGroup));
        $set('J', trim((string) ($detail->karyawan?->cabang_bpjs)) ?: ($employee?->cabang_bpjs ?? ''));
        $set('K', 'x');
        $set('L', $this->parseWage($employee?->dpp_bp_jamsostek ?? $employee?->dpp_jkn ?? 0));
        foreach (['M' => 'jht_biaya', 'N' => 'jht_hutang', 'O' => 'jkk_tunjangan', 'P' => 'jkm_tunjangan', 'Q' => 'jp_biaya', 'R' => 'jp_hutang'] as $column => $field) $set($column, $value($field));
        $set('S', $totalPpu);
        foreach (['T' => 'noncrew_jht_biaya', 'U' => 'jkk_hutang', 'V' => 'bpu_jkk_tunjangan', 'W' => 'bpu_jkm'] as $column => $field) $set($column, $value($field));
        $set('X', $totalBpu);
        $set('Y', (float) $detail->kis_tunjangan);
        $set('Z', $jkn1);
        $set('AA', $jkn4);
        $set('AB', 'x'); $set('AC', 'x'); $set('AD', 'x');
        $set('AE', $summaryValue('ppu', $value('jkk_tunjangan') + $value('jkm_tunjangan')));
        $set('AF', $summaryValue('bpu', $value('bpu_jkk_tunjangan') + $value('bpu_jkm') + $value('noncrew_jkk_tunjangan') + $value('noncrew_jkm_tunjangan')));
        $set('AG', $summaryValue('jkn', (float) $detail->kis_tunjangan));
        $set('AH', $detail->summaryBurden('kary_jht'));
        $set('AI', $detail->summaryBurden('kary_jkn'));
        $set('AJ', $detail->summaryBurden('pt'));
        $set('AK', $detail->summaryBurden('non_kary'));
        $set('AL', (string) ($detail->no_voucher ?? ''));
        $set('AM', 'x'); $set('AN', 'x'); $set('AO', 'x');
        $set('AP', $totalPpu + $totalBpu);
        $set('AQ', $value('jkk_hutang') + $value('noncrew_jht_hutang'));
        $set('AR', (float) $detail->kis_tunjangan);
        $set('AS', $jkn1);
        $set('AT', $detail->summaryBurden('kary_jht') + $detail->summaryBurden('kary_jkn'));
        $set('AU', 'x');
        $set('AW', $employee?->pekerjaan ?? $employee?->posisi ?? $employee?->group_posisi ?? '');
        $set('BB', 'x');
        $accounts = new BpjsJournalAccount;
        $set('BC', $accounts->label((string) $jamsostekType, (string) $jamsostekGroup) ?? 'Belum dipetakan');
        [$tunjangan, $biaya, $hutang] = (new BpjsJournalAmounts)->forDetail($detail, $component);
        $set('BD', $tunjangan);
        $set('BE', $biaya);
        $set('BF', $hutang);
        $set('BG', $jknGroup);
        $set('BH', $jamsostekGroup);
        $set('BI', $detail->rincian_bpjs === null
            ? array_sum(array_map(fn ($field) => (float) $detail->$field, $ppu)) : null);

        if ($component === 'jkn') {
            $set('L', $this->parseWage($employee?->dpp_jkn ?? 0));
            foreach (['M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'AE', 'AF', 'AH', 'AJ', 'AK', 'AP', 'AQ', 'BI'] as $column) $set($column, null);
            $set('AT', $detail->summaryBurden('kary_jkn'));
        } elseif ($component === 'jamsostek') {
            foreach (['Y', 'Z', 'AA', 'AG', 'AI', 'AR', 'AS'] as $column) $set($column, null);
            $set('AT', $detail->summaryBurden('kary_jht'));
        }

        return $row;
    }

    private function parseWage(mixed $value): float
    {
        if (is_string($value)) {
            $value = trim($value);
            if (preg_match('/^\d{1,3}(?:\.\d{3})+(?:,\d+)?$/', $value)) {
                $value = str_replace('.', '', $value);
            }
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }
}

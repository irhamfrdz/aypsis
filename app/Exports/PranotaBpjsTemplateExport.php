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
        $groupHeading = $sheet->getCell('H1')->getValue();
        $typeHeading = $sheet->getCell('I1')->getValue();
        $sheet->setCellValue('H1', $typeHeading);
        $sheet->setCellValue('I1', $groupHeading);
        foreach (['BC' => 'AKUN JURNAL', 'BD' => 'TUNJANGAN JURNAL', 'BE' => 'BIAYA JURNAL', 'BF' => 'HUTANG JURNAL', 'BG' => 'GROUP JKN', 'BH' => 'GROUP BP JAMSOSTEK', 'BI' => 'JAMSOSTEK GABUNGAN DATA LAMA'] as $column => $heading) {
            $sheet->setCellValue($column.'1', $heading);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $firstDataRow = 2;
        $lastTemplateRow = $sheet->getHighestDataRow();
        if ($lastTemplateRow >= $firstDataRow) {
            $sheet->removeRow($firstDataRow, $lastTemplateRow - $firstDataRow + 1);
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

        $lastRow = max(2, count($this->rows) + 1);
        $sheet->getStyle('L2:L'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('BD2:BF'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('BI2:BI'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setAutoFilter('A1:BI'.$lastRow);
        $sheet->freezePane('A2');
        $sheet->setSelectedCell('A1');
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
        $sheet->setTitle('Rincian BPJS');
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
        $set('H', $component === 'jkn' ? $jknGroup : ($jamsostekGroup ?: $jknGroup));
        $set('I', $component === 'jkn' ? $jknType : ($jamsostekType ?: $jknType));
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

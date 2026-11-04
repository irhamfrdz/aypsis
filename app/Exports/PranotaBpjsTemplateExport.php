<?php

namespace App\Exports;

use App\Models\PranotaBpjsDetail;
use App\Models\PranotaBpjsHeader;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PranotaBpjsTemplateExport implements FromArray, WithCustomStartCell, WithEvents, WithColumnFormatting
{
    private Worksheet $template;

    private array $rows = [];

    public function __construct(private readonly PranotaBpjsHeader $pranota)
    {
        $path = base_path('BPJS 26 11.xlsx');
        $spreadsheet = IOFactory::load($path);
        $this->template = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getSheet(0);
        $this->template->setTitle('Rincian BPJS');

        foreach ($this->pranota->details as $index => $detail) {
            $this->rows[] = $this->mapDetail($detail, $index + 1);
        }
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function startCell(): string
    {
        return 'A2';
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                foreach ($this->rows as $index => $row) {
                    $excelRow = $index + 2;
                    $sheet->fromArray([$row], null, 'A'.$excelRow, true);
                    foreach (['B', 'D', 'E', 'AL'] as $column) {
                        $cell = $row[$this->columnIndex($column) - 1] ?? null;
                        if ($cell !== null && $cell !== '') {
                            $sheet->setCellValueExplicit($column.$excelRow, (string) $cell, DataType::TYPE_STRING);
                        }
                    }
                }

                $lastRow = max(2, count($this->rows) + 1);
                $sheet->setAutoFilter('A1:BB'.$lastRow);
                $sheet->freezePane('A2');
                $sheet->setSelectedCell('A1');
                $sheet->getPageSetup()->setOrientation(Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
                $sheet->setTitle('Rincian BPJS');
            },
        ];
    }

    private function columnIndex(string $column): int
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
    }

    private function mapDetail(PranotaBpjsDetail $detail, int $number): array
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

        $row = array_fill(0, 54, null);
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
        $set('H', $jknGroup ?: $jamsostekGroup);
        $set('I', $jamsostekType ?: $jknType);
        $set('J', $employee?->lokasi ?? $employee?->cabang ?? $employee?->penempatan);
        $set('K', 'x');
        $set('L', (float) ($employee?->dpp_bp_jamsostek ?? $employee?->dpp_jkn ?? 0));
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

        return $row;
    }
}

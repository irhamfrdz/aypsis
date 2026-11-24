<?php

namespace Tests\Unit;

use App\Exports\PranotaUangMakanExport;
use App\Models\Karyawan;
use App\Models\PranotaUangMakan;
use App\Models\PranotaUangMakanDetail;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class PranotaUangMakanExportTest extends TestCase
{
    private function export(bool $empty = false): PranotaUangMakanExport
    {
        $pranota = $this->getMockBuilder(PranotaUangMakan::class)->onlyMethods(['loadMissing'])->getMock();
        $pranota->method('loadMissing')->willReturnSelf();
        $pranota->setDateFormat('Y-m-d H:i:s');
        $pranota->nomor_pranota = 'PUM-26-11-003';
        $pranota->tanggal_pranota = '2026-11-24';
        $employee = new Karyawan;
        $employee->setRawAttributes(['nik' => '0012', 'nama_lengkap' => 'Budi', 'departemen' => 'HRD', 'akun_bank' => '0012345678']);
        $detail = new PranotaUangMakanDetail(['kehadiran' => '3 Hari', 'nominal_awal' => 150000, 'adjustment' => -10000, 'total_akhir' => 140000, 'catatan' => '=1+1']);
        $detail->setRelation('karyawan', $employee);
        $missing = new PranotaUangMakanDetail(['kehadiran' => '', 'nominal_awal' => 0, 'adjustment' => 0, 'total_akhir' => 0]);
        $missing->setRelation('karyawan', null);
        $pranota->setRelation('details', new Collection($empty ? [] : [$detail, $missing]));

        return new PranotaUangMakanExport($pranota);
    }

    public function test_export_contains_saved_detail_values_and_reconciled_totals(): void
    {
        $rows = $this->export()->array();
        $this->assertSame('Tanggal: 24/11/2026', $rows[2][0]);
        $this->assertSame('Karyawan tidak tersedia', $rows[5][2]);
        $this->assertSame(['0012', 'Budi', 'HRD', '0012345678', 3, 50000.0, 150000.0, -10000.0, 140000.0, '=1+1'], array_slice($rows[6], 1));
        $this->assertSame(['', '', 'TOTAL', '', '', 3, '', 150000.0, -10000.0, 140000.0, ''], $rows[7]);
    }

    public function test_empty_export_and_literal_text_binding(): void
    {
        $export = $this->export(true);
        $this->assertCount(6, $export->array());
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        foreach (['B6' => '0012', 'E6' => '0012345678', 'K6' => '=1+1'] as $address => $value) {
            $export->bindValue($sheet->getCell($address), $value);
            $this->assertSame($value, $sheet->getCell($address)->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell($address)->getDataType());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function test_generated_workbook_header_filter_and_first_employee_stay_aligned(): void
    {
        foreach ([false, true] as $empty) {
            $export = $this->export($empty);
            $workbook = new Spreadsheet;
            $worksheet = $workbook->getActiveSheet();
            $sheet = $this->getMockBuilder(\Maatwebsite\Excel\Sheet::class)
                ->disableOriginalConstructor()->onlyMethods(['append', 'getDelegate'])->getMock();
            $sheet->method('getDelegate')->willReturn($worksheet);
            $sheet->method('append')->willReturnCallback(function ($rows, $startCell, $strict) use ($worksheet) {
                $worksheet->fromArray($rows, null, $startCell ?? 'A1', $strict);
            });
            $originalBinder = \PhpOffice\PhpSpreadsheet\Cell\Cell::getValueBinder();
            $path = tempnam(sys_get_temp_dir(), 'pranota-export-');
            try {
                // Exercise Laravel Excel's actual row flattening, which drops empty arrays.
                $sheet->appendRows($export->array(), $export);
                $export->registerEvents()[\Maatwebsite\Excel\Events\AfterSheet::class](new \Maatwebsite\Excel\Events\AfterSheet($sheet, $export));
                foreach ($export->columnFormats() as $column => $format) {
                    $worksheet->getStyle($column.'1:'.$column.$worksheet->getHighestRow())->getNumberFormat()->setFormatCode($format);
                }
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook))->save($path);
                \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder);
                $reopened = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $result = $reopened->getActiveSheet();
                $this->assertSame('NO', $result->getCell('A5')->getValue());
                $this->assertSame('NAMA KARYAWAN', $result->getCell('C5')->getValue());
                $this->assertSame('FF1E3A5F', $result->getStyle('A5')->getFill()->getStartColor()->getARGB());
                $this->assertSame('A5:K'.($empty ? 5 : 7), $result->getAutoFilter()->getRange());
                $this->assertSame('C6', $result->getFreezePane());
                $this->assertFalse($worksheet->getShowGridlines());
                if (! $empty) {
                    $this->assertSame(1, $result->getCell('A6')->getValue());
                    $this->assertFalse($result->getStyle('A6')->getFont()->getBold());
                    $this->assertSame('0012', $result->getCell('B7')->getValue());
                    $this->assertSame(0.0, (float) $result->getCell('I6')->getValue());
                    $this->assertSame('TOTAL', $result->getCell('C8')->getValue());
                    $this->assertSame(140000.0, (float) $result->getCell('J8')->getValue());
                }
                $reopened->disconnectWorksheets();
            } finally {
                \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder($originalBinder);
                $workbook->disconnectWorksheets();
                unlink($path);
            }
        }
    }
}

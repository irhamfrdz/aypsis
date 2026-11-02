<?php

namespace Tests\Feature;

use App\Http\Controllers\ManifestController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ManifestBulkImportTonnageTest extends TestCase
{
    public function test_bulk_import_converts_kilograms_to_tons_with_comma_decimals(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            foreach ([
                'nomor_bl', 'nomor_manifest', 'nomor_tanda_terima', 'nomor_kontainer', 'no_seal',
                'nama_kapal', 'no_voyage', 'tipe_kontainer', 'size_kontainer', 'nama_barang',
                'pengirim', 'alamat_pengirim', 'penerima', 'alamat_penerima', 'contact_person',
                'term', 'tonnage', 'volume', 'satuan', 'pelabuhan_muat', 'pelabuhan_bongkar',
            ] as $field) {
                $table->string($field)->nullable();
            }
            $table->date('tanggal_berangkat')->nullable();
            $table->integer('kuantitas')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nama Kapal');
        foreach ([['BL-1', 78776], ['BL-2', '78.776'], ['BL-3', 192]] as $index => [$bl, $kg]) {
            $row = $index + 2;
            $sheet->setCellValue("A{$row}", 'KM TEST');
            $sheet->setCellValue("B{$row}", '001');
            $sheet->setCellValue("D{$row}", $bl);
            $sheet->setCellValue("G{$row}", "CONT-{$index}");
            $sheet->setCellValue("J{$row}", '20');
            $sheet->setCellValueExplicit("R{$row}", (string) $kg, $index === 1 ? DataType::TYPE_STRING : DataType::TYPE_NUMERIC);
        }

        $path = tempnam(sys_get_temp_dir(), 'manifest-bulk-');
        try {
            (new Xlsx($spreadsheet))->save($path);
            $file = new UploadedFile($path, 'manifest-bulk.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
            $request = Request::create('/', 'POST', [], [], ['file' => $file]);

            app(ManifestController::class)->bulkImport($request);

            $this->assertSame('78,776', DB::table('manifests')->where('nomor_bl', 'BL-1')->value('tonnage'));
            $this->assertSame('78,776', DB::table('manifests')->where('nomor_bl', 'BL-2')->value('tonnage'));
            $this->assertSame('0,192', DB::table('manifests')->where('nomor_bl', 'BL-3')->value('tonnage'));
        } finally {
            unlink($path);
        }
    }
}

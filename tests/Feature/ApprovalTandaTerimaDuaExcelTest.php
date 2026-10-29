<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use App\Models\TandaTerima;
use App\Services\ApprovalTandaTerimaDuaExcelService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ApprovalTandaTerimaDuaExcelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('users', fn (Blueprint $table) => $table->id());
        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            foreach (['shipper', 'alamat_shipper', 'npwp_shipper', 'consignee', 'alamat_consignee', 'npwp_consignee',
                'notify_party_consignee', 'alamat_notify_party_consignee', 'npwp_notify_party_consignee',
                'delivery_address_contact_person', 'document_ppftz_03', 'condition'] as $field) {
                $table->string($field)->nullable();
            }
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            foreach (['no_surat_jalan', 'no_kontainer', 'no_seal', 'pengirim', 'penerima', 'alamat_pengirim',
                'alamat_penerima', 'notify_party', 'alamat_notify_party', 'tipe_kontainer', 'size', 'jenis_barang'] as $field) {
                $table->string($field)->nullable();
            }
            $table->date('tanggal')->nullable();
            foreach (['dimensi_items', 'dimensi_details', 'nama_barang'] as $field) {
                $table->json($field)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->json('goods');
            $table->json('manifest_goods')->nullable();
            $table->timestamps();
        });
        (require base_path('database/migrations/2026_10_29_130000_create_approval_tanda_terima_2_exports_table.php'))->up();
    }

    public function test_export_keeps_sheet_one_template_and_marks_receipt_exported(): void
    {
        $shipperId = DB::table('shipper_consignees')->insertGetId([
            'shipper' => 'PT Pengirim', 'consignee' => 'PT Penerima', 'npwp_shipper' => '0012345',
        ]);
        $receiptId = DB::table('tanda_terimas')->insertGetId([
            'shipper_jb_id' => $shipperId,
            'no_surat_jalan' => 'JB10036397',
            'tanggal' => '2026-10-29',
            'no_kontainer' => 'AYPU3797127',
            'size' => '20ft',
            'no_seal' => 'SEAL123',
            'dimensi_items' => json_encode([['nama_barang' => 'Barang Asal', 'jumlah' => 3, 'satuan' => 'Koli']]),
        ]);
        DB::table('approval_tanda_terima_2_goods')->insert([
            'source_type' => 'fcl', 'source_id' => $receiptId,
            'goods' => json_encode([['nama_barang' => 'Barang Perincian', 'jumlah' => 4, 'satuan' => 'Karton']]),
            'manifest_goods' => json_encode([['nama_barang' => 'Barang Manifest', 'hs_code' => '1234', 'jumlah' => 5,
                'satuan' => 'Peti', 'tonase' => '17.000', 'meter_kubik' => 3.5]]),
        ]);

        $response = app(ApprovalTandaTerimaDuaController::class)->export(
            'fcl', $receiptId, app(\App\Services\ApprovalTandaTerimaDuaExcelService::class)
        );
        $file = $response->getFile()->getPathname();
        try {
            $template = IOFactory::load(resource_path('templates/approval_tanda_terima_2.xlsx'));
            $export = IOFactory::load($file);
            $sheet = $export->getSheet(0);
            $this->assertSame([$template->getSheet(0)->getTitle()], $export->getSheetNames());
            $this->assertSame($template->getSheet(0)->getMergeCells(), $sheet->getMergeCells());
            $this->assertSame(
                $template->getSheet(0)->getStyle('B7')->getFont()->getBold(),
                $sheet->getStyle('B7')->getFont()->getBold()
            );
            $this->assertSame('1234', $sheet->getCell('C9')->getValue());
            $this->assertSame('AYPU3797127', $sheet->getCell('D9')->getValue());
            $this->assertSame(1, $sheet->getCell('F9')->getValue());
            $this->assertSame('Unit', $sheet->getCell('G9')->getValue());
            $this->assertSame('Container 20 feet stc :', $sheet->getCell('H9')->getValue());
            $this->assertSame(5, $sheet->getCell('I9')->getValue());
            $this->assertSame('Barang Manifest', $sheet->getCell('K9')->getValue());
            $this->assertSame('Barang Perincian', $sheet->getCell('N9')->getValue());
            $this->assertEquals(17000, $sheet->getCell('O9')->getValue());
            $this->assertEquals(3.5, $sheet->getCell('P9')->getValue());
            $this->assertNull($sheet->getCell('AE9')->getValue());
            $this->assertNull($sheet->getCell('AF9')->getValue());
            $this->assertSame('PT Pengirim', $sheet->getCell('Q9')->getValue());
            $this->assertSame('0012345', $sheet->getCell('S9')->getValue());
            $this->assertSame('JB10036397', $sheet->getCell('AC9')->getValue());
            $this->assertSame($template->getSheet(0)->getCell('P402')->getValue(), $sheet->getCell('P402')->getValue());
            $this->assertDatabaseHas('approval_tanda_terima_2_exports', ['source_type' => 'fcl', 'source_id' => $receiptId]);
        } finally {
            @unlink($file);
        }
    }

    public function test_container_description_uses_the_receipt_size(): void
    {
        $receipt = new TandaTerima;
        $receipt->no_surat_jalan = 'TEST-40HC';
        $receipt->size = '40hc';
        $receipt->setRelation('shipperJb', null);

        $file = app(ApprovalTandaTerimaDuaExcelService::class)->create('fcl', $receipt);
        try {
            $sheet = IOFactory::load($file)->getSheet(0);
            $this->assertSame(1, $sheet->getCell('F9')->getValue());
            $this->assertSame('Unit', $sheet->getCell('G9')->getValue());
            $this->assertSame('Container 40 feet HC stc :', $sheet->getCell('H9')->getValue());
        } finally {
            @unlink($file);
        }
    }

    public function test_bulk_export_contains_only_selected_receipts_in_selected_order(): void
    {
        $first = DB::table('tanda_terimas')->insertGetId([
            'no_surat_jalan' => 'FIRST', 'no_kontainer' => 'CONTAINER-A', 'size' => '20ft',
        ]);
        $unused = DB::table('tanda_terimas')->insertGetId([
            'no_surat_jalan' => 'UNUSED', 'no_kontainer' => 'CONTAINER-X', 'size' => '20ft',
        ]);
        $last = DB::table('tanda_terimas')->insertGetId([
            'no_surat_jalan' => 'LAST', 'no_kontainer' => 'CONTAINER-B', 'size' => '40ft',
        ]);

        $response = app(ApprovalTandaTerimaDuaController::class)->exportBulk(
            Request::create('/approval-tanda-terima-2/fcl/export-bulk', 'POST', ['ids' => [$last, $first]]),
            'fcl', app(ApprovalTandaTerimaDuaExcelService::class)
        );
        $file = $response->getFile()->getPathname();
        try {
            $workbook = IOFactory::load($file);
            $sheet = $workbook->getSheet(0);
            $this->assertSame(['MANIFEST & PERINCIAN'], $workbook->getSheetNames());
            $this->assertSame('LAST', $sheet->getCell('AC9')->getValue());
            $this->assertSame('CONTAINER-B', $sheet->getCell('D9')->getValue());
            $this->assertSame('FIRST', $sheet->getCell('AC10')->getValue());
            $this->assertSame('CONTAINER-A', $sheet->getCell('D10')->getValue());
            $this->assertNull($sheet->getCell('AC11')->getValue());
            $this->assertNull($sheet->getCell('AE9')->getValue());
            $this->assertNull($sheet->getCell('AE10')->getValue());
            $this->assertDatabaseHas('approval_tanda_terima_2_exports', ['source_type' => 'fcl', 'source_id' => $first]);
            $this->assertDatabaseHas('approval_tanda_terima_2_exports', ['source_type' => 'fcl', 'source_id' => $last]);
            $this->assertDatabaseMissing('approval_tanda_terima_2_exports', ['source_type' => 'fcl', 'source_id' => $unused]);
        } finally {
            @unlink($file);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use Illuminate\Database\Schema\Blueprint;
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
            $this->assertSame($template->getSheetNames(), $export->getSheetNames());
            $this->assertSame($template->getSheet(0)->getMergeCells(), $sheet->getMergeCells());
            foreach (['B7', 'C9', 'K9', 'N9', 'O9', 'AD9'] as $cell) {
                $this->assertSame($template->getSheet(0)->getCell($cell)->getXfIndex(), $sheet->getCell($cell)->getXfIndex());
            }
            $this->assertSame('1234', $sheet->getCell('C9')->getValue());
            $this->assertSame('AYPU3797127', $sheet->getCell('D9')->getValue());
            $this->assertSame('Barang Manifest', $sheet->getCell('K9')->getValue());
            $this->assertSame('Barang Perincian', $sheet->getCell('N9')->getValue());
            $this->assertEquals(17000, $sheet->getCell('O9')->getValue());
            $this->assertEquals(3.5, $sheet->getCell('P9')->getValue());
            $this->assertSame('PT Pengirim', $sheet->getCell('Q9')->getValue());
            $this->assertSame('0012345', $sheet->getCell('S9')->getValue());
            $this->assertSame('JB10036397', $sheet->getCell('AC9')->getValue());
            $this->assertSame($template->getSheet(0)->getCell('P402')->getValue(), $sheet->getCell('P402')->getValue());
            $this->assertDatabaseHas('approval_tanda_terima_2_exports', ['source_type' => 'fcl', 'source_id' => $receiptId]);
        } finally {
            @unlink($file);
        }
    }
}

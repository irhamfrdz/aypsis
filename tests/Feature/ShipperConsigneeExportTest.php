<?php

namespace Tests\Feature;

use App\Exports\ShipperConsigneeDataExport;
use App\Models\ShipperConsignee;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ShipperConsigneeExportTest extends TestCase
{
    public function test_filtered_excel_contains_full_master_data_and_preserves_phone_as_text(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            $table->string('shipper')->nullable();
            $table->text('alamat_shipper')->nullable();
            $table->string('npwp_shipper')->nullable();
            $table->string('consignee')->nullable();
            $table->text('alamat_consignee')->nullable();
            $table->string('npwp_consignee')->nullable();
            $table->string('notify_party_consignee')->nullable();
            $table->text('alamat_notify_party_consignee')->nullable();
            $table->string('npwp_notify_party_consignee')->nullable();
            $table->text('delivery_address_contact_person')->nullable();
            $table->string('document_ppftz_03')->nullable();
            $table->string('condition')->nullable();
            $table->boolean('status')->nullable();
        });

        DB::table('shipper_consignees')->insert([
            ['shipper' => 'PT Contoh', 'consignee' => 'PT Tujuan', 'npwp_shipper' => '081234567890', 'delivery_address_contact_person' => 'Budi', 'status' => 1],
            ['shipper' => 'PT Lain', 'consignee' => 'PT Tujuan Lain', 'npwp_shipper' => '089999999999', 'delivery_address_contact_person' => 'Sari', 'status' => 0],
        ]);

        $export = new ShipperConsigneeDataExport(ShipperConsignee::query()->where('shipper', 'like', '%Contoh%'));
        File::ensureDirectoryExists(storage_path('framework/testing'));
        $path = tempnam(storage_path('framework/testing'), 'shipper-export-');

        try {
            file_put_contents($path, Excel::raw($export, ExcelFormat::XLSX));
            $sheet = IOFactory::load($path)->getActiveSheet();

            $this->assertSame('SHIPPER', $sheet->getCell('B1')->getValue());
            $this->assertSame('PT Contoh', $sheet->getCell('B2')->getValue());
            $this->assertSame('081234567890', $sheet->getCell('D2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
            $this->assertSame('Budi', $sheet->getCell('K2')->getValue());
            $this->assertSame('Aktif', $sheet->getCell('N2')->getValue());
            $this->assertSame(2, $sheet->getHighestRow());
        } finally {
            unlink($path);
        }
    }
}

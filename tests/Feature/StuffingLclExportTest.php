<?php

namespace Tests\Feature;

use App\Exports\StuffingLclExport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class StuffingLclExportTest extends TestCase
{
    public function test_export_filters_seal_and_location_and_writes_safe_excel(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->integer('tujuan_pengiriman_id');
            $table->string('nomor_tanda_terima');
            $table->softDeletes();
        });
        Schema::create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->integer('tanda_terima_lcl_id');
            $table->string('nomor_seal')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('nomor_kontainer')->default('TEST');
        });
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('no_seal')->nullable();
            $table->string('status');
        });
        DB::table('tanda_terimas_lcl')->insert([
            ['id' => 1, 'tujuan_pengiriman_id' => 10, 'nomor_tanda_terima' => '001'],
            ['id' => 2, 'tujuan_pengiriman_id' => 20, 'nomor_tanda_terima' => '002'],
        ]);
        DB::table('tanda_terima_lcl_kontainer_pivot')->insert([
            ['id' => 1, 'tanda_terima_lcl_id' => 1, 'nomor_seal' => 'S1'],
            ['id' => 2, 'tanda_terima_lcl_id' => 1, 'nomor_seal' => null],
            ['id' => 3, 'tanda_terima_lcl_id' => 2, 'nomor_seal' => 'S2'],
            ['id' => 4, 'tanda_terima_lcl_id' => 2, 'nomor_seal' => ''],
        ]);
        $this->assertEquals([1], (new StuffingLclExport('sealed', 10))->query()->reorder()->pluck('id')->all());
        $this->assertEquals([2, 4], (new StuffingLclExport('unsealed'))->query()->reorder()->pluck('id')->all());
        $this->assertSame(4, (new StuffingLclExport)->query()->count());
        DB::table('tanda_terima_lcl_kontainer_pivot')->insert([
            ['id' => 5, 'tanda_terima_lcl_id' => 1, 'nomor_seal' => null, 'nomor_kontainer' => 'SHIPPED', 'assigned_at' => '2026-11-01 00:00:00'],
            ['id' => 6, 'tanda_terima_lcl_id' => 1, 'nomor_seal' => null, 'nomor_kontainer' => 'OLD', 'assigned_at' => '2026-06-30 23:59:59'],
            ['id' => 7, 'tanda_terima_lcl_id' => 1, 'nomor_seal' => null, 'nomor_kontainer' => 'NEW', 'assigned_at' => '2026-07-01 00:00:00'],
        ]);
        DB::table('prospek')->insert(['nomor_kontainer' => 'SHIPPED', 'no_seal' => null, 'status' => 'sudah_muat']);
        // An old sealed voyage must not hide a new unsealed batch reusing the container.
        DB::table('prospek')->insert(['nomor_kontainer' => 'NEW', 'no_seal' => 'OLD-SEAL', 'status' => 'sudah_muat']);
        $this->assertEquals([2, 4, 7], (new StuffingLclExport('unsealed'))->query()->reorder()->orderBy('id')->pluck('id')->all());

        // Verify the actual XLSX writer preserves leading zeros and treats formulas as text.
        $export = new class extends StuffingLclExport implements \Maatwebsite\Excel\Concerns\FromCollection {
            public function query() { return parent::query()->whereRaw('1 = 0'); }
            public function collection() { return collect([(object) ['nomor_kontainer' => '=1+1', 'size_kontainer' => '001', 'tipe_kontainer' => 'LCL', 'tandaTerima' => null, 'nomor_seal' => null, 'tanggal_seal' => null, 'assigned_at' => null]]); }
        };
        $path = tempnam(sys_get_temp_dir(), 'lcl-export-');
        try {
            file_put_contents($path, Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame('=1+1', $sheet->getCell('A2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
            $this->assertSame('001', $sheet->getCell('B2')->getValue());
        } finally {
            unlink($path);
        }
    }
}

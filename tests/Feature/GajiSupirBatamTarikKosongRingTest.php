<?php

namespace Tests\Feature;

use App\Http\Controllers\GajiSupirBatamController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class GajiSupirBatamTarikKosongRingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_panggilan');
            $table->string('nama_lengkap');
        });
        Schema::create('gaji_supir_batams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
        });
        Schema::create('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->id();
            $table->string('supir');
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan');
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->decimal('uang_jalan')->default(0);
        });
        Schema::create('kelola_bbm', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan');
            $table->integer('tahun');
        });
        Schema::create('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelola_bbm_id')->nullable();
            $table->string('ring');
            $table->string('expedisi');
            $table->string('wilayah');
        });
        foreach (['surat_jalan_batams', 'surat_jalan_bongkaran_batams'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('supir');
                $table->date('tanggal_surat_jalan');
            });
        }
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supir');
            $table->string('kegiatan');
            $table->timestamps();
        });
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('supir');
            $table->date('tanggal');
            $table->softDeletes();
        });
        Schema::create('biaya_bensin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal');
        });

        DB::table('karyawans')->insert(['id' => 1, 'nama_panggilan' => 'Budi', 'nama_lengkap' => 'Budi Santoso']);
        DB::table('gaji_supir_batams')->insert(['id' => 1, 'karyawan_id' => 1, 'tanggal_mulai' => '2026-10-01', 'tanggal_selesai' => '2026-10-03']);
        DB::table('kelola_bbm')->insert([
            ['id' => 1, 'bulan' => 9, 'tahun' => 2026],
            ['id' => 2, 'bulan' => 10, 'tahun' => 2026],
        ]);
        DB::table('pricelist_uang_jalan_batam')->insert([
            ['kelola_bbm_id' => 1, 'ring' => '1', 'expedisi' => 'PB', 'wilayah' => 'Bukit Senyum'],
            ['kelola_bbm_id' => 2, 'ring' => '3', 'expedisi' => 'PB', 'wilayah' => 'Bukit Senyum, Batu Ampar'],
        ]);
        DB::table('surat_jalan_tarik_kosong_batams')->insert([
            ['supir' => 'Budi', 'tanggal_surat_jalan' => '2026-10-02', 'no_surat_jalan' => 'TK-1', 'tujuan_pengambilan' => 'Bukit Senyum (PB)', 'uang_jalan' => 100000],
            ['supir' => 'Budi', 'tanggal_surat_jalan' => '2026-10-02', 'no_surat_jalan' => 'TK-2', 'tujuan_pengambilan' => 'Batu Ampar', 'uang_jalan' => 100000],
            ['supir' => 'Budi', 'tanggal_surat_jalan' => '2026-10-02', 'no_surat_jalan' => 'TK-3', 'tujuan_pengambilan' => 'Lokasi Lain', 'uang_jalan' => 100000],
        ]);
    }

    public function test_print_and_detail_resolve_tarik_kosong_ring_from_active_pricelist(): void
    {
        $controller = new GajiSupirBatamController;

        foreach ([$controller->show(1), $controller->print(1)] as $view) {
            $waybills = collect($view->getData()['waybills'])->keyBy('no_surat_jalan');

            $this->assertSame('3', $waybills['TK-1']['ring']);
            $this->assertSame('3', $waybills['TK-2']['ring']);
            $this->assertSame('-', $waybills['TK-3']['ring']);
        }
    }

    public function test_calculation_uses_the_same_ring_mapping(): void
    {
        $response = (new GajiSupirBatamController)->calculate(Request::create('/calculate', 'GET', [
            'karyawan_id' => 1,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-03',
        ]));

        $waybills = collect($response->getData(true)['waybills'])->keyBy('no_surat_jalan');

        $this->assertSame('3', $waybills['TK-1']['ring']);
        $this->assertSame('-', $waybills['TK-3']['ring']);
    }

    public function test_excel_download_contains_the_same_waybill_details_as_print(): void
    {
        $controller = new GajiSupirBatamController;
        $response = $controller->export(1);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('gaji-supir-batam-1.xlsx', $response->headers->get('Content-Disposition'));

        $sheet = IOFactory::load($response->getFile()->getPathname())->getActiveSheet();
        $this->assertSame('TK-1', $sheet->getCell('C8')->getValue());
        $this->assertSame('3', (string) $sheet->getCell('F8')->getValue());
    }

    public function test_filtered_excel_download_contains_one_slip_sheet_per_matching_salary(): void
    {
        DB::table('karyawans')->insert(['id' => 2, 'nama_panggilan' => 'Doni', 'nama_lengkap' => 'Doni Saputra']);
        DB::table('gaji_supir_batams')->insert([
            ['id' => 2, 'karyawan_id' => 1, 'tanggal_mulai' => '2026-10-04', 'tanggal_selesai' => '2026-10-06'],
            ['id' => 3, 'karyawan_id' => 2, 'tanggal_mulai' => '2026-10-02', 'tanggal_selesai' => '2026-10-05'],
            ['id' => 4, 'karyawan_id' => 1, 'tanggal_mulai' => '2026-11-01', 'tanggal_selesai' => '2026-11-03'],
        ]);

        $response = (new GajiSupirBatamController)->exportFiltered(Request::create('/gaji-supir-batam/export-filtered', 'GET', [
            'karyawan_id' => 1,
            'start_date' => '2026-10-02',
            'end_date' => '2026-10-05',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $workbook = IOFactory::load($response->getFile()->getPathname());
        $this->assertSame(['Gaji 2', 'Gaji 1'], $workbook->getSheetNames());
        $this->assertSame('Budi Santoso', $workbook->getSheetByName('Gaji 1')->getCell('B3')->getValue());
        $this->assertSame('TK-1', $workbook->getSheetByName('Gaji 1')->getCell('C8')->getValue());

        $allDrivers = (new GajiSupirBatamController)->exportFiltered(Request::create('/gaji-supir-batam/export-filtered', 'GET', [
            'karyawan_id' => null,
            'start_date' => '2026-10-02',
            'end_date' => '2026-10-05',
        ]));
        $allWorkbook = IOFactory::load($allDrivers->getFile()->getPathname());
        $this->assertSame(['Gaji 2', 'Gaji 3', 'Gaji 1'], $allWorkbook->getSheetNames());
    }
}

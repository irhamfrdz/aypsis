<?php

namespace Tests\Feature;

use App\Exports\ReportGajiSupirBatamExport;
use App\Http\Controllers\ReportGajiSupirBatamController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ReportGajiSupirBatamFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('cabang');
            $table->string('pekerjaan');
        });
        Schema::create('gaji_supir_batams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status_pembayaran');
        });

        DB::table('karyawans')->insert([
            ['id' => 1, 'nama_lengkap' => 'Supir Satu', 'cabang' => 'BATAM', 'pekerjaan' => 'SUPIR'],
            ['id' => 2, 'nama_lengkap' => 'Supir Dua', 'cabang' => 'BATAM', 'pekerjaan' => 'DRIVER'],
        ]);
        DB::table('gaji_supir_batams')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'tanggal_mulai' => '2026-09-01', 'tanggal_selesai' => '2026-09-15', 'status_pembayaran' => 'PAID'],
            ['id' => 2, 'karyawan_id' => 2, 'tanggal_mulai' => '2026-09-16', 'tanggal_selesai' => '2026-09-30', 'status_pembayaran' => 'PENDING'],
            ['id' => 3, 'karyawan_id' => 2, 'tanggal_mulai' => '2026-10-01', 'tanggal_selesai' => '2026-10-15', 'status_pembayaran' => 'PAID'],
        ]);
    }

    public function test_all_drivers_includes_salary_periods_overlapping_the_date_range(): void
    {
        $request = Request::create('/report-gaji-supir-batam', 'GET', [
            'start_date' => '2026-09-15',
            'end_date' => '2026-09-16',
            'karyawan_id' => null,
        ]);

        $view = (new ReportGajiSupirBatamController)->index($request);

        $this->assertEqualsCanonicalizing([1, 2], $view->getData()['gajiList']->pluck('id')->all());
    }

    public function test_export_uses_the_same_date_and_driver_filters(): void
    {
        Excel::shouldReceive('download')->once()->withArgs(function ($export) {
            $this->assertInstanceOf(ReportGajiSupirBatamExport::class, $export);
            $this->assertSame([2], $export->view()->getData()['gajiList']->pluck('id')->all());

            return true;
        })->andReturn(response('export'));

        $request = Request::create('/report-gaji-supir-batam/export', 'GET', [
            'start_date' => '2026-09-15',
            'end_date' => '2026-09-16',
            'karyawan_id' => '2',
        ]);

        (new ReportGajiSupirBatamController)->export($request);
    }

    public function test_export_all_drivers_accepts_a_null_filter_value(): void
    {
        Excel::shouldReceive('download')->once()->withArgs(function ($export) {
            $this->assertEqualsCanonicalizing([1, 2], $export->view()->getData()['gajiList']->pluck('id')->all());

            return true;
        })->andReturn(response('export'));

        $request = Request::create('/report-gaji-supir-batam/export', 'GET', [
            'start_date' => '2026-09-15',
            'end_date' => '2026-09-16',
            'karyawan_id' => null,
        ]);

        (new ReportGajiSupirBatamController)->export($request);
    }
}

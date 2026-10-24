<?php

namespace Tests\Feature;

use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PranotaUangMakanController;
use App\Models\Karyawan;
use App\Models\PayrollUangMakan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PayrollUangMakanNominalTetapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik');
            $table->string('status');
            $table->string('departemen')->nullable();
            $table->string('grup')->nullable();
            $table->string('cabang')->nullable();
            $table->string('penempatan')->nullable();
            $table->decimal('nominal_uang_makan')->default(0);
        });
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->dateTime('waktu');
            $table->date('tanggal_kerja_manual')->nullable();
            $table->string('tipe');
        });
        Schema::create('uang_makans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
            $table->decimal('nominal');
        });
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_departemen');
            $table->boolean('is_active');
        });
        Schema::create('pranota_uang_makans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->date('tanggal_pranota')->nullable();
            $table->timestamps();
        });
        Schema::create('pranota_uang_makan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_makan_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
        });
        Schema::create('payroll_uang_makans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
            $table->date('periode_start');
            $table->date('periode_end');
            $table->integer('total_kehadiran');
            $table->integer('multiplier');
            $table->decimal('nominal_per_hari');
            $table->decimal('total_payout');
            $table->string('status');
            $table->timestamps();
        });

        foreach ([
            [1, ' jakarta satpam pel ', null, 350000],
            [2, 'JAKARTA KANTOR', null, 25000],
            [3, 'LAINNYA', '["SATPAM PELABUHAN"]', 300000],
        ] as [$id, $departemen, $grup, $nominal]) {
            DB::table('karyawans')->insert([
                'id' => $id, 'nama_lengkap' => 'Karyawan '.$id, 'nik' => 'NIK'.$id,
                'status' => 'active', 'departemen' => $departemen, 'grup' => $grup,
                'penempatan' => 'Pelabuhan 1', 'nominal_uang_makan' => 10000,
            ]);
            DB::table('uang_makans')->insert([
                'karyawan_id' => $id, 'tipe_karyawan' => Karyawan::class, 'nominal' => $nominal, 'tanggal' => '2026-10-12',
            ]);
            foreach (['2026-10-12', '2026-10-13', '2026-10-18'] as $date) {
                DB::table('absensis')->insert([
                    'karyawan_id' => $id, 'waktu' => $date.' 08:00:00', 'tipe' => 'Masuk',
                ]);
            }
        }
    }

    private function parameters(): array
    {
        return ['start_date' => '2026-10-12', 'end_date' => '2026-10-18', 'tipe_karyawan' => 'Karyawan'];
    }

    public function test_preview_uses_fixed_master_amount_without_day_or_location_multiplier(): void
    {
        $view = (new PayrollController)->uangMakan(Request::create('/payroll/uang-makan', 'GET', $this->parameters() + ['generate' => 1]));
        $rows = collect($view->getData()['payrolls'])->keyBy(fn ($row) => $row['karyawan']->id);

        $this->assertEquals(350000, $rows[1]['total_payout']);
        $this->assertSame(1, $rows[1]['multiplier']);
        $this->assertTrue($rows[1]['is_nominal_tetap']);
        $this->assertSame(3, $rows[1]['total_kehadiran']);
        $this->assertEquals(100000, $rows[2]['total_payout']);
        $this->assertEquals(600000, $rows[3]['total_payout']);
    }

    public function test_stored_payroll_matches_preview_fixed_amount(): void
    {
        Excel::shouldReceive('download')->once()->andReturn(response('export'));
        (new PayrollController)->storeUangMakan(Request::create('/payroll/uang-makan', 'POST', $this->parameters()));

        $rows = PayrollUangMakan::all()->keyBy('karyawan_id');
        $this->assertEquals(350000, $rows[1]->total_payout);
        $this->assertEquals(1, $rows[1]->multiplier);
        $this->assertEquals(100000, $rows[2]->total_payout);
        $this->assertEquals(600000, $rows[3]->total_payout);
    }

    public function test_pranota_refresh_keeps_fixed_amount_and_fallback_is_not_divided_by_days(): void
    {
        DB::table('pranota_uang_makans')->insert(['id' => 1, 'nomor_pranota' => 'PUM-TEST']);
        $request = Request::create('/refresh', 'POST', $this->parameters() + [
            'row_items' => [['key' => 'Karyawan_1', 'kehadiran' => '7 Hari', 'nominal_awal' => 350000]],
        ]);
        $controller = new PranotaUangMakanController;
        $row = $controller->refreshAbsensi($request, 1)->getData(true)['data']['Karyawan_1'];
        $this->assertEquals(350000, $row['nominal_awal']);
        $this->assertSame(1, $row['multiplier']);

        DB::table('uang_makans')->where('karyawan_id', 1)->delete();
        DB::table('karyawans')->where('id', 1)->update(['nominal_uang_makan' => 0]);
        $row = $controller->refreshAbsensi($request, 1)->getData(true)['data']['Karyawan_1'];
        $this->assertEquals(350000, $row['nominal_awal']);
        $this->assertEquals(350000, $row['nominal_per_hari']);
    }

    private function seedJakartaSatpam(): void
    {
        DB::table('karyawans')->insert([
            'id' => 4, 'nik' => 'SATPAM4', 'nama_lengkap' => 'Satpam Jakarta', 'status' => 'active',
            'departemen' => ' jakarta satpam ', 'grup' => null, 'penempatan' => 'KANTOR JAKARTA',
            'nominal_uang_makan' => 25000,
        ]);
        DB::table('uang_makans')->insert([
            'karyawan_id' => 4, 'tipe_karyawan' => Karyawan::class, 'nominal' => 25000, 'tanggal' => '2026-10-12',
        ]);
        foreach (['2026-10-12 08:00:00', '2026-10-13 08:00:00', '2026-10-18 08:00:00', '2026-10-18 09:00:00'] as $time) {
            DB::table('absensis')->insert(['karyawan_id' => 4, 'waktu' => $time, 'tipe' => 'Masuk']);
        }
    }

    public function test_jakarta_satpam_preview_counts_sunday_once_and_only_when_present(): void
    {
        $this->seedJakartaSatpam();
        $controller = new PayrollController;
        $parameters = $this->parameters() + ['generate' => 1];
        $view = $controller->uangMakan(Request::create('/', 'GET', $parameters));
        $rows = collect($view->getData()['payrolls'])->keyBy(fn ($row) => $row['karyawan']->id);
        $this->assertSame(3, $rows[4]['total_kehadiran']);
        $this->assertSame(['2026-10-12', '2026-10-13', '2026-10-18'], $rows[4]['dates_kehadiran']);
        $this->assertEquals(75000, $rows[4]['total_payout']);
        $this->assertFalse($rows[4]['is_nominal_tetap']);
        $this->assertSame(2, $rows[2]['total_kehadiran']);

        DB::table('absensis')->where('karyawan_id', 4)->whereDate('waktu', '2026-10-18')->delete();
        $withoutSunday = $controller->uangMakan(Request::create('/', 'GET', $parameters));
        $satpam = collect($withoutSunday->getData()['payrolls'])->first(fn ($row) => $row['karyawan']->id === 4);
        $this->assertSame(2, $satpam['total_kehadiran']);
        $this->assertEquals(50000, $satpam['total_payout']);
    }

    public function test_jakarta_satpam_payroll_persists_sunday_and_handles_a_sunday_only_range(): void
    {
        $this->seedJakartaSatpam();
        Excel::shouldReceive('download')->twice()->andReturn(response('export'));
        $controller = new PayrollController;
        $controller->storeUangMakan(Request::create('/', 'POST', $this->parameters()));
        $row = PayrollUangMakan::where('karyawan_id', 4)->firstOrFail();
        $this->assertEquals(3, $row->total_kehadiran);
        $this->assertEquals(75000, $row->total_payout);
        $this->assertEquals(1, $row->multiplier);
        $controller->storeUangMakan(Request::create('/', 'POST', array_merge($this->parameters(), [
            'start_date' => '2026-10-18', 'end_date' => '2026-10-18',
        ])));
        $sunday = PayrollUangMakan::where('karyawan_id', 4)->whereDate('periode_start', '2026-10-18')->firstOrFail();
        $this->assertEquals(1, $sunday->total_kehadiran);
        $this->assertEquals(25000, $sunday->total_payout);
    }

    public function test_jakarta_satpam_pranota_refresh_includes_sunday_using_the_daily_rate(): void
    {
        $this->seedJakartaSatpam();
        DB::table('pranota_uang_makans')->insert(['id' => 1, 'nomor_pranota' => 'PUM-SATPAM']);
        $request = Request::create('/refresh', 'POST', $this->parameters() + [
            'row_items' => [['key' => 'Karyawan_4', 'kehadiran' => '2 Hari', 'nominal_awal' => 50000]],
        ]);
        $row = (new PranotaUangMakanController)->refreshAbsensi($request, 1)->getData(true)['data']['Karyawan_4'];
        $this->assertSame('3 Hari', $row['kehadiran']);
        $this->assertEquals(75000, $row['nominal_awal']);
        $this->assertEquals(25000, $row['nominal_per_hari']);
    }
}

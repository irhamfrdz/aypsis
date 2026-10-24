<?php

namespace Tests\Feature;

use App\Http\Controllers\PerhitunganLemburController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PerhitunganLemburMidnightTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Cache::flush();

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'grup', 'pekerjaan', 'divisi', 'cabang', 'penempatan'] as $column) {
                $table->string($column)->nullable();
            }
            $table->date('tanggal_berhenti')->nullable();
            $table->integer('nominal_uang_makan')->default(20000);
        });
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->string('nik');
            $table->string('tipe');
            $table->dateTime('waktu');
            $table->date('tanggal_kerja_manual')->nullable();
        });
        Schema::create('uang_makans', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->integer('nominal');
            $table->date('tanggal');
            $table->string('tipe_karyawan')->nullable();
        });
        Schema::create('master_uang_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('sub_group');
            $table->integer('pengali_uang_makan_hari_libur')->default(1);
        });
        Schema::create('uang_lembur_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('uang_lembur_id');
            $table->string('tipe_hari');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->boolean('is_sampai_selesai')->default(false);
            $table->string('satuan');
            $table->integer('nominal');
        });
        Schema::create('hari_liburs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
        });
        Schema::create('pranota_lembur_karyawan_headers', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pranota')->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pranota_lembur_karyawans', function (Blueprint $table) {
            $table->id();
            $table->integer('pranota_lembur_karyawan_header_id');
            $table->integer('karyawan_id');
        });
    }

    public static function midnightCases(): array
    {
        return [
            'renamed employee group' => ['JAKARTA PELABUHAN 1', 'PELABUHAN 1', '06:00:00', 'Hari Biasa', 6, 30000],
            'renamed master group with rounding' => ['PELABUHAN 1', 'JAKARTA PELABUHAN 1', '06:01:00', 'Hari Biasa', 7, 35000],
            'both renamed on holiday' => ['JAKARTA PELABUHAN 1', 'JAKARTA PELABUHAN 1', '06:00:00', 'Hari Libur', 6, 30000],
            'legacy group' => ['PELABUHAN 1', 'PELABUHAN 1', '06:00:00', 'Hari Biasa', 6, 30000],
        ];
    }

    #[DataProvider('midnightCases')]
    public function test_midnight_tariff_in_page_and_pranota_calculation(string $employeeGroup, string $masterGroup, string $end, string $dayType, int $hours, int $amount): void
    {
        DB::table('karyawans')->insert(['id' => 1, 'nik' => '101', 'nama_lengkap' => 'DARMINTO', 'grup' => json_encode(['LEMBUR:'.$employeeGroup])]);
        DB::table('absensis')->insert([
            ['karyawan_id' => 1, 'nik' => '101', 'tipe' => 'lembur masuk', 'waktu' => '2026-10-15 00:00:00'],
            ['karyawan_id' => 1, 'nik' => '101', 'tipe' => 'lembur pulang', 'waktu' => '2026-10-15 '.$end],
        ]);
        DB::table('master_uang_lemburs')->insert(['id' => 1, 'group' => 'LEMBUR', 'sub_group' => $masterGroup]);
        DB::table('uang_lembur_rules')->insert([
            'uang_lembur_id' => 1, 'tipe_hari' => $dayType, 'jam_mulai' => '18:00:00',
            'jam_selesai' => '08:00:00', 'satuan' => 'Jam', 'nominal' => 5000,
        ]);
        if ($dayType === 'Hari Libur') {
            DB::table('hari_liburs')->insert(['tanggal' => '2026-10-15']);
        }

        $view = app(PerhitunganLemburController::class)->index(Request::create('/', 'GET', ['start_date' => '2026-10-15', 'end_date' => '2026-10-15']));
        $page = collect($view->getData()['rekapData'])->first();
        $this->assertNotNull($page);
        $detail = $page['detail'][0];
        $this->assertEquals($hours, $detail['durasi_jam']);
        $this->assertEquals($amount, $detail['nominal']);
        $this->assertEquals($dayType === 'Hari Libur' ? 20000 : 0, $detail['uang_makan_lembur']);

        $pranota = PerhitunganLemburController::calculateAttendanceForKaryawans([1], '2026-10-15', '2026-10-15')[1];
        $this->assertEquals($hours, $pranota['total_jam']);
        $this->assertEquals($amount, $pranota['nominal_lembur']);
        $this->assertEquals($detail['uang_makan_lembur'], $pranota['uang_makan_lembur']);
    }
}

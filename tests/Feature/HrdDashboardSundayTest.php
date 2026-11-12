<?php

namespace Tests\Feature;

use App\Http\Controllers\HrdDashboardController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HrdDashboardSundayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang');
        });
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'status', 'cabang', 'grup', 'departemen', 'divisi', 'penempatan'] as $column) {
                $table->string($column)->nullable();
            }
            $table->date('tanggal_berhenti')->nullable();
        });
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('nik')->nullable();
            $table->string('tipe');
            $table->dateTime('waktu');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('detail_lokasi')->nullable();
        });
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status');
        });
        Schema::create('lokasi_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('radius');
            $table->boolean('is_active');
        });

        foreach ([1, 2, 3] as $id) {
            DB::table('karyawans')->insert([
                'id' => $id, 'nik' => '000'.$id,
                'nama_lengkap' => 'Karyawan '.$id, 'status' => 'active',
                'cabang' => 'JAKARTA', 'grup' => '["UANG MAKAN"]',
            ]);
        }
    }

    private function scan(int $employee, string $date, string $type): void
    {
        DB::table('absensis')->insert([
            'karyawan_id' => $employee, 'waktu' => $date, 'tipe' => $type,
        ]);
    }

    private function dashboard(string $start, string $end): array
    {
        return (new HrdDashboardController)->index(Request::create('/hrd-dashboard', 'GET', [
            'tanggal_dari' => $start, 'tanggal_sampai' => $end,
            'cabang' => 'JAKARTA', 'grup' => 'UANG MAKAN',
        ]))->getData();
    }

    public function test_sunday_does_not_require_clock_in_or_clock_out(): void
    {
        $this->scan(1, '2026-11-08 08:00:00', 'Masuk');
        $this->scan(2, '2026-11-08 17:00:00', 'Pulang');

        $data = $this->dashboard('2026-11-08', '2026-11-08');

        $this->assertCount(0, $data['karyawanBelumAbsen']);
        $this->assertCount(0, $data['karyawanBelumAbsenPulang']);
        $this->assertCount(1, $data['absensiMasuk']);
        $this->assertSame(2, $data['totalPresensiHariIni']);
    }

    public function test_date_range_ignores_sunday_but_keeps_saturday_and_monday_missing_scans(): void
    {
        foreach ([1, 2] as $id) {
            $this->scan($id, '2026-11-07 08:00:00', 'Masuk');
            $this->scan($id, '2026-11-07 13:00:00', 'Pulang');
            $this->scan($id, '2026-11-09 08:00:00', 'Masuk');
            $this->scan($id, '2026-11-09 17:00:00', 'Pulang');
        }
        $this->scan(1, '2026-11-08 17:00:00', 'Pulang');
        $this->scan(2, '2026-11-08 08:00:00', 'Masuk');
        $this->scan(3, '2026-11-07 13:00:00', 'Pulang');
        $this->scan(3, '2026-11-09 08:00:00', 'Masuk');

        $data = $this->dashboard('2026-11-07', '2026-11-09');

        $this->assertSame([3], $data['karyawanBelumAbsen']->pluck('id')->all());
        $this->assertSame(['2026-11-07'], $data['karyawanBelumAbsen']->first()->tanggal_belum_masuk);
        $this->assertSame([3], $data['karyawanBelumAbsenPulang']->pluck('id')->all());
        $this->assertSame(['2026-11-09'], $data['karyawanBelumAbsenPulang']->first()->tanggal_belum_pulang);
    }

    public function test_weekday_still_counts_employees_without_any_scans(): void
    {
        $data = $this->dashboard('2026-11-09', '2026-11-09');

        $this->assertCount(3, $data['karyawanBelumAbsen']);
    }
}

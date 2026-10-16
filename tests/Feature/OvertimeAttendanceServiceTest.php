<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Services\OvertimeAttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OvertimeAttendanceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nik')->nullable();
            $table->string('tipe');
            $table->dateTime('waktu');
        });
    }

    public function test_groups_overnight_overtime_without_mixing_employees_or_days(): void
    {
        $this->log(1, '101', 'lembur_masuk', '2026-10-15 22:00:00');
        $this->log(1, '101', 'lembur_pulang', '2026-10-16 02:00:00');
        $this->log(1, '101', 'lembur masuk', '2026-10-16 21:00:00');
        $this->log(1, '101', 'lembur pulang', '2026-10-17 02:00:00');
        $this->log(2, '202', 'lembur masuk', '2026-10-16 18:00:00');
        $this->log(2, '202', 'lembur pulang', '2026-10-16 20:00:00');

        $employees = collect([
            new Karyawan(['id' => 1, 'nik' => '101']),
            new Karyawan(['id' => 2, 'nik' => '202']),
        ]);
        // Eloquent does not fill the primary key via mass assignment.
        $employees[0]->id = 1;
        $employees[1]->id = 2;

        $daily = app(OvertimeAttendanceService::class)->dailyForEmployees(
            $employees,
            Carbon::parse('2026-10-16'),
            Carbon::parse('2026-10-16')
        );

        $this->assertCount(1, $daily->get(1));
        $this->assertSame('2026-10-16 21:00:00', $daily->get(1)->first()->waktu_lembur_masuk);
        $this->assertSame('2026-10-17 02:00:00', $daily->get(1)->first()->waktu_lembur_pulang);
        $this->assertSame('2026-10-16 18:00:00', $daily->get(2)->first()->waktu_lembur_masuk);
        $this->assertSame('2026-10-16 20:00:00', $daily->get(2)->first()->waktu_lembur_pulang);
    }

    public function test_matching_by_nik_and_same_time_end_follow_existing_work_date_rules(): void
    {
        $this->log(null, '101', 'lembur masuk', '2026-10-16 22:00:00');
        $this->log(1, '101', 'lembur pulang', '2026-10-17 01:00:00');
        $this->log(1, '101', 'lembur masuk', '2026-10-17 23:00:00');
        $this->log(1, '101', 'lembur pulang', '2026-10-17 23:00:00');
        $this->log(1, '101', 'lembur pulang', '2026-10-18 01:00:00');

        $employee = new Karyawan(['nik' => '101']);
        $employee->id = 1;
        $daily = app(OvertimeAttendanceService::class)->dailyForEmployees(
            collect([$employee]),
            Carbon::parse('2026-10-16'),
            Carbon::parse('2026-10-18')
        );

        $this->assertNull($daily->get(1)->first()->waktu_lembur_masuk);
        $this->assertSame('2026-10-17 01:00:00', $daily->get(1)->first()->waktu_lembur_pulang);
        $this->assertSame('2026-10-17 23:00:00', $daily->get(1)[1]->waktu_lembur_pulang);
        $this->assertNull($daily->get(1)[2]->waktu_lembur_masuk);
        $this->assertSame('2026-10-18 01:00:00', $daily->get(1)[2]->waktu_lembur_pulang);
    }

    private function log(?int $employeeId, string $nik, string $type, string $time): void
    {
        DB::table('absensis')->insert([
            'karyawan_id' => $employeeId,
            'nik' => $nik,
            'tipe' => $type,
            'waktu' => $time,
        ]);
    }
}

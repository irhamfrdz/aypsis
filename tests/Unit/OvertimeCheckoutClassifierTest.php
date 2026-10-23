<?php

namespace Tests\Unit;

use App\Helpers\AttendanceEventType;
use App\Helpers\AttendanceWorkDate;
use App\Services\OvertimeCheckoutClassifier;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class OvertimeCheckoutClassifierTest extends TestCase
{
    private Manager $capsule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->capsule = new Manager;
        $this->capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        DB::swap($this->capsule->getDatabaseManager());
        $this->capsule->getConnection()->getSchemaBuilder()->create('absensis', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->dateTime('waktu');
            $table->string('tipe');
        });
    }

    protected function tearDown(): void
    {
        $this->capsule->getDatabaseManager()->disconnect();
        DB::clearResolvedInstance('db');
        parent::tearDown();
    }

    private function log(string $time, string $type, string $nik = '1001'): void
    {
        DB::table('absensis')->insert(['nik' => $nik, 'waktu' => $time, 'tipe' => $type]);
    }

    public function test_machine_codes_keep_regular_break_and_overtime_types_separate(): void
    {
        foreach (['Masuk', 'Pulang', 'istirahat_keluar', 'istirahat_masuk', 'lembur_masuk', 'lembur_pulang'] as $code => $type) {
            $this->assertSame($type, AttendanceEventType::fromMachineCode($code));
        }
        $this->assertSame('lembur_pulang', AttendanceEventType::normalize('Lembur_Pulang'));
        $this->assertSame('istirahat_masuk', AttendanceEventType::fromMachineCode('b'));
        $this->assertNull(AttendanceEventType::fromMachineCode('unknown'));
    }

    public function test_overnight_overtime_does_not_replace_regular_checkout_in_daily_summary(): void
    {
        $this->log('2026-10-22 08:00:00', 'Masuk');
        $this->log('2026-10-22 17:00:00', 'Pulang');
        $this->log('2026-10-22 18:00:00', AttendanceEventType::fromMachineCode(4));
        $classifier = new OvertimeCheckoutClassifier;
        $type = $classifier->resolve('1001', '2026-10-23 04:00:00', 'Pulang');
        $this->assertSame('lembur_pulang', $type);
        $this->log('2026-10-23 04:00:00', $type);
        $dateSql = AttendanceWorkDate::sql('sqlite');
        $summary = DB::table('absensis')->whereRaw("($dateSql) = ?", ['2026-10-22'])
            ->selectRaw("MAX(CASE WHEN LOWER(tipe) IN ('pulang', 'keluar') THEN waktu END) as pulang,
                MAX(CASE WHEN LOWER(tipe) IN ('lembur_pulang', 'lembur pulang') THEN waktu END) as pulang_lembur")->first();
        $this->assertSame('2026-10-22 17:00:00', $summary->pulang);
        $this->assertSame('2026-10-23 04:00:00', $summary->pulang_lembur);
        $this->assertSame('lembur_pulang', $classifier->resolve('1001', '2026-10-23 04:00:00', 'Pulang'));
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-23 05:00:00', 'Pulang'));
        $this->assertSame(1, DB::table('absensis')->where('tipe', 'Pulang')->count());
    }

    public function test_regular_night_shift_and_other_employees_are_not_reclassified(): void
    {
        $this->log('2026-10-22 20:00:00', 'Masuk');
        $classifier = new OvertimeCheckoutClassifier;
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-23 04:00:00', 'Pulang'));
        $this->log('2026-10-22 17:00:00', 'Pulang', '2002');
        $this->log('2026-10-22 18:00:00', 'lembur_masuk', '2002');
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-23 04:00:00', 'Pulang'));
        $this->assertSame('lembur_pulang', $classifier->resolve('1001', '2026-10-23 04:00:00', 'lembur_pulang'));
    }

    public function test_a_new_regular_shift_or_closed_session_does_not_reuse_an_old_overtime_start(): void
    {
        $this->log('2026-10-22 17:00:00', 'Pulang');
        $this->log('2026-10-22 18:00:00', 'lembur_masuk');
        $this->log('2026-10-23 08:00:00', 'Masuk');
        $classifier = new OvertimeCheckoutClassifier;
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-23 17:00:00', 'Pulang'));
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-24 04:00:00', 'Pulang'));
        $this->log('2026-10-22 23:00:00', 'lembur_pulang');
        $this->assertSame('Pulang', $classifier->resolve('1001', '2026-10-23 04:00:00', 'Pulang'));
    }
}

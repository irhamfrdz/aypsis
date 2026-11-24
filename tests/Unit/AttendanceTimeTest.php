<?php

namespace Tests\Unit;

use App\Helpers\AttendanceTime;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AttendanceTimeTest extends TestCase
{
    public function test_check_in_within_grace_period_is_saved_at_nine(): void
    {
        foreach (['09:01:00', '09:02:30', '09:03:00', '09:03:59'] as $time) {
            foreach (['Masuk', 'masuk', 'check in'] as $type) {
                $this->assertSame('2026-11-24 09:00:00', AttendanceTime::normalize('2026-11-24 '.$time, $type)->format('Y-m-d H:i:s'));
            }
        }
    }

    public function test_times_outside_grace_period_are_preserved(): void
    {
        foreach (['08:59:59', '09:00:59', '09:04:00', '10:02:00', '21:02:00'] as $time) {
            $this->assertSame('2026-11-24 '.$time, AttendanceTime::normalize('2026-11-24 '.$time, 'Masuk')->format('Y-m-d H:i:s'));
        }
    }

    public function test_other_attendance_types_are_preserved(): void
    {
        foreach (['Pulang', 'istirahat_keluar', 'istirahat_masuk', 'lembur_masuk', 'lembur_pulang'] as $type) {
            $this->assertSame('2026-11-24 09:02:30', AttendanceTime::normalize('2026-11-24 09:02:30', $type)->format('Y-m-d H:i:s'));
        }
    }

    public function test_input_date_and_timezone_are_preserved_without_mutating_original(): void
    {
        $original = Carbon::parse('2026-11-24 09:02:30', 'Asia/Jakarta');
        $result = AttendanceTime::normalize($original, 'Masuk');
        $this->assertSame('2026-11-24 09:00:00 +07:00', $result->format('Y-m-d H:i:s P'));
        $this->assertSame('09:02:30', $original->format('H:i:s'));
    }
}

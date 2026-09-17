<?php

namespace Tests\Unit;

use App\Helpers\AttendanceWorkDate;
use PDO;
use PHPUnit\Framework\TestCase;

class AttendanceWorkDateTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec('CREATE TABLE absensis (id INTEGER PRIMARY KEY, nik TEXT, karyawan_id INTEGER, waktu TEXT, tipe TEXT)');
    }

    private function log(string $time, string $type, string $nik = '1593'): int
    {
        $this->db->prepare('INSERT INTO absensis (nik, waktu, tipe) VALUES (?, ?, ?)')->execute([$nik, $time, $type]);

        return (int) $this->db->lastInsertId();
    }

    private function dates(int $offset = 6): array
    {
        $sql = AttendanceWorkDate::sql('sqlite', 'absensis', $offset);

        return $this->db->query("SELECT id, $sql AS tanggal FROM absensis ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function test_1593_overnight_return_is_grouped_on_start_date_with_original_timestamp(): void
    {
        $start = $this->log('2026-09-16 22:00:00', 'lembur_masuk');
        $end = $this->log('2026-09-17 10:10:43', 'lembur_pulang');
        $regular = $this->log('2026-09-17 08:00:00', 'Masuk');
        $this->assertSame([$start => '2026-09-16', $end => '2026-09-16', $regular => '2026-09-17'], $this->dates());
        $this->assertSame($this->dates(), $this->dates(0)); // PWA calendar dates use the same overtime session.
        $sql = AttendanceWorkDate::sql('sqlite');
        $row = $this->db->query("SELECT MIN(waktu) AS masuk, MAX(waktu) AS pulang FROM absensis WHERE ($sql) = '2026-09-16' GROUP BY ($sql)")->fetch(PDO::FETCH_ASSOC);
        $this->assertSame(['masuk' => '2026-09-16 22:00:00', 'pulang' => '2026-09-17 10:10:43'], $row);
    }

    public function test_closed_session_is_not_reused_and_other_employees_are_isolated(): void
    {
        $this->log('2026-09-16 22:00:00', 'Mulai Lembur');
        $first = $this->log('2026-09-17 01:00:00', 'Selesai Lembur');
        $orphan = $this->log('2026-09-17 10:10:43', 'Selesai Lembur');
        $other = $this->log('2026-09-17 02:00:00', 'Selesai Lembur', '2000');
        $dates = $this->dates();
        $this->assertSame('2026-09-16', $dates[$first]);
        $this->assertSame('2026-09-17', $dates[$orphan]);
        $this->assertSame('2026-09-17', $dates[$other]);
    }

    public function test_latest_session_crosses_month_and_noon_without_using_stale_start(): void
    {
        $this->log('2026-09-30 17:00:00', 'Mulai Lembur');
        $this->log('2026-09-30 18:00:00', 'Selesai Lembur');
        $this->log('2026-09-30 22:00:00', 'Lembur_Masuk');
        $end = $this->log('2026-10-01 13:10:43', 'Lembur_Pulang');
        $this->assertSame('2026-09-30', $this->dates()[$end]);
        $this->log('2026-10-02 22:00:00', 'Mulai Lembur');
        $stale = $this->log('2026-10-04 01:00:00', 'Selesai Lembur');
        $this->assertSame('2026-10-04', $this->dates()[$stale]);
    }

    public function test_early_morning_overtime_uses_start_calendar_date_and_regular_shift_keeps_cutoff(): void
    {
        $start = $this->log('2026-09-17 02:00:00', 'Mulai Lembur');
        $end = $this->log('2026-09-17 07:00:00', 'Selesai Lembur');
        $regular = $this->log('2026-09-17 05:00:00', 'Pulang');
        $this->assertSame([$start => '2026-09-17', $end => '2026-09-17', $regular => '2026-09-16'], $this->dates());
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class RolloverConditionTest extends TestCase
{
    public function test_show_rollover_for_old_date()
    {
        // simulate a tagihan object with tanggal_harga_awal 2 months ago
        $tagihan = new \stdClass();
        $tagihan->tanggal_harga_awal = Carbon::now()->subMonths(2);

        $showRollover = false;
        try {
            if (!empty($tagihan->tanggal_harga_awal)) {
                $dt = (method_exists($tagihan->tanggal_harga_awal, 'lt') ? $tagihan->tanggal_harga_awal : Carbon::parse($tagihan->tanggal_harga_awal));
                $showRollover = $dt->lessThan(Carbon::now()->subMonth());
            }
        } catch (\Exception $e) {
            $showRollover = false;
        }

        $this->assertTrue($showRollover, 'Expected rollover to be shown for a date older than one month');
    }

    public function test_no_rollover_for_recent_date()
    {
        // simulate a tagihan object with tanggal_harga_awal 10 days ago
        $tagihan = new \stdClass();
        $tagihan->tanggal_harga_awal = Carbon::now()->subDays(10);

        $showRollover = false;
        try {
            if (!empty($tagihan->tanggal_harga_awal)) {
                $dt = (method_exists($tagihan->tanggal_harga_awal, 'lt') ? $tagihan->tanggal_harga_awal : Carbon::parse($tagihan->tanggal_harga_awal));
                $showRollover = $dt->lessThan(Carbon::now()->subMonth());
            }
        } catch (\Exception $e) {
            $showRollover = false;
        }

        $this->assertFalse($showRollover, 'Expected rollover NOT to be shown for a recent date');
    }
}

<?php

namespace Tests\Unit;

use App\Models\PembayaranAktivitasLain;
use PHPUnit\Framework\TestCase;

class PembayaranAktivitasLainPphTest extends TestCase
{
    public function test_grand_total_deducts_selected_pph_from_vehicle_payment(): void
    {
        $this->assertSame(
            ['pph_nominal' => 2500, 'grand_total' => 97500],
            PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kendaraan', 2.5)
        );
    }

    public function test_no_pph_keeps_grand_total_equal_to_jumlah(): void
    {
        $this->assertSame(
            ['pph_nominal' => 0, 'grand_total' => 100000],
            PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kendaraan', 0)
        );
    }

    public function test_two_percent_pph_is_deducted(): void
    {
        $this->assertSame(
            ['pph_nominal' => 2000, 'grand_total' => 98000],
            PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kendaraan', 2)
        );
    }

    public function test_other_activities_do_not_deduct_pph(): void
    {
        $this->assertSame(
            ['pph_nominal' => 0, 'grand_total' => 100000],
            PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kapal', 2.5)
        );
    }

    public function test_manual_pph_nominal_overrides_percentage(): void
    {
        $this->assertSame(
            ['pph_nominal' => 1500, 'grand_total' => 98500],
            PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kendaraan', 2, 1500)
        );
    }

    public function test_manual_pph_nominal_cannot_exceed_jumlah(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PembayaranAktivitasLain::calculateTotals(100000, 'Pembayaran Kendaraan', 2, 100001);
    }
}

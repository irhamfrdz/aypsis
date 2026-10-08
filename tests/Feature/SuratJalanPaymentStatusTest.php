<?php

namespace Tests\Feature;

use App\Http\Controllers\SuratJalanController;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Tests\TestCase;

class SuratJalanPaymentStatusTest extends TestCase
{
    public function test_reset_paid_status_updates_the_overall_badge_and_preserves_amounts(): void
    {
        $suratJalan = $this->createPartialMock(SuratJalan::class, ['save']);
        $suratJalan->forceFill([
            'id' => 1, 'status' => 'sudah_dibayar', 'status_pembayaran' => 'sudah_dibayar',
            'status_pembayaran_uang_jalan' => 'dibayar', 'jumlah_terbayar' => 100000, 'total_tarif' => 100000,
        ]);
        $suratJalan->expects($this->once())->method('save')->willReturn(true);

        $response = (new SuratJalanController)->updateStatus(Request::create('/', 'POST', ['status' => 'belum_dibayar']), $suratJalan);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame('belum_dibayar', $suratJalan->overall_status_pembayaran);
        $this->assertSame('belum_dibayar', $suratJalan->status_pembayaran);
        $this->assertSame('active', $suratJalan->status);
        $this->assertEquals(100000, $suratJalan->jumlah_terbayar);
        $this->assertEquals(100000, $suratJalan->total_tarif);
    }

    public function test_reset_payment_preserves_an_existing_operational_status(): void
    {
        foreach (['active', 'completed', 'cancelled'] as $status) {
            $suratJalan = $this->createPartialMock(SuratJalan::class, ['save']);
            $suratJalan->forceFill([
                'id' => 1, 'status' => $status, 'status_pembayaran' => 'sudah_dibayar',
                'status_pembayaran_uang_jalan' => 'dibayar',
            ]);
            $suratJalan->expects($this->once())->method('save')->willReturn(true);
            $response = (new SuratJalanController)->updateStatus(Request::create('/', 'POST', ['status' => 'belum_dibayar']), $suratJalan);

            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame($status, $suratJalan->status);
            $this->assertSame('belum_dibayar', $suratJalan->overall_status_pembayaran);
        }
    }
}

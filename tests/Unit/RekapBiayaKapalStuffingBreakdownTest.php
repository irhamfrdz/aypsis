<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalStuffing;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclKontainerPivot;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBiayaKapalStuffingBreakdownTest extends TestCase
{
    public function test_lcl_container_numbers_from_pivot_are_grouped_and_amounts_are_preserved(): void
    {
        $receipts = collect([
            (new TandaTerima)->forceFill(['id' => 1, 'no_surat_jalan' => 'SJ-01', 'no_kontainer' => 'CONT-A']),
            $this->lclReceipt(2, 'LS-02'),
            $this->lclReceipt(3, 'LS-03'),
            (new TandaTerima)->forceFill(['id' => 4, 'no_surat_jalan' => 'SJ-04', 'nomor_kontainer' => 'CARGO-01', 'nama_barang' => 'Cargo']),
            (new TandaTerima)->forceFill(['id' => 5, 'no_surat_jalan' => 'SJ-05', 'nama_barang' => 'Belum stuffing']),
        ]);
        $detail = $this->getMockBuilder(BiayaKapalStuffing::class)->onlyMethods(['getTandaTerimas'])->getMock();
        $detail->forceFill(['id' => 1, 'kapal' => 'KM ALKEN PESONA', 'voyage' => 'AS17JP26',
            'subtotal' => 600, 'pph' => 30, 'total_biaya' => 570]);
        $detail->method('getTandaTerimas')->willReturn($receipts);
        $otherVoyage = (new BiayaKapalStuffing)->forceFill(['kapal' => 'KM ALKEN PESONA', 'voyage' => 'OTHER', 'total_biaya' => 999]);
        $invoice = (new BiayaKapal)->setRelation('stuffingDetails', collect([$detail, $otherVoyage]));

        $rows = (new ReflectionMethod(RekapBiayaKapalController::class, 'allocateStuffingRows'))
            ->invoke(new RekapBiayaKapalController, $invoice, 'KM ALKEN PESONA', 'AS17JP26');

        $this->assertSame(['CONT-A', 'CONT-B'], $rows->where('jenis', 'kontainer')->pluck('label')->all());
        $shared = $rows->firstWhere('label', 'CONT-B');
        $this->assertSame('LS-02, LS-03', $shared['receipts']);
        $this->assertEquals(228, $shared['total']);
        $this->assertCount(1, $rows->where('jenis', 'cargo'));
        $this->assertCount(1, $rows->where('jenis', 'lainnya'));
        $this->assertEqualsWithDelta(600, $rows->sum('subtotal'), 0.001);
        $this->assertEqualsWithDelta(30, $rows->sum('pph'), 0.001);
        $this->assertEqualsWithDelta(570, $rows->sum('total'), 0.001);
    }

    private function lclReceipt(int $id, string $receipt): TandaTerimaLcl
    {
        $tt = (new TandaTerimaLcl)->forceFill(['id' => $id, 'no_surat_jalan' => $receipt]);
        $tt->setRelation('kontainerPivot', collect([
            (new TandaTerimaLclKontainerPivot)->forceFill(['nomor_kontainer' => 'CONT-B']),
        ]));
        $tt->setRelation('items', collect());

        return $tt;
    }
}

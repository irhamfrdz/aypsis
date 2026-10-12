<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\Manifest;
use App\Models\PranotaOb;
use App\Models\PranotaObMuatTemas;
use App\Services\RekapBlService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBiayaKapalObBreakdownTest extends TestCase
{
    public function test_regular_ob_breakdown_keeps_only_selected_containers_and_allocates_shared_bl_cost(): void
    {
        $record = (new PranotaOb)->forceFill(['id' => 318, 'is_pranota_ob' => true,
            'rekap_ob_items' => [
                ['nomor_kontainer' => 'CONT-A', 'biaya' => 80000],
                ['nomor_kontainer' => 'CONT-B', 'biaya' => 50000],
                ['nomor_kontainer' => '-', 'biaya' => 30000],
            ],
        ]);
        [$selected, $common] = $this->split($record);
        $this->assertSame([['nomor_kontainer' => 'CONT-A', 'biaya' => 40000.0]], $selected->rekap_ob_items);
        $this->assertEquals(40000, $selected->apportioned['total_biaya']);
        $this->assertSame([['nomor_kontainer' => '-', 'biaya' => 30000.0]], $common->rekap_ob_items);
        $this->assertEquals(30000, $common->apportioned['total_biaya']);
        $this->assertCount(3, $record->rekap_ob_items);
        $this->assertEquals(80000, $record->rekap_ob_items[0]['biaya']);
    }

    public function test_muat_temas_breakdown_uses_the_same_bl_share_as_the_transaction_total(): void
    {
        $record = (new PranotaObMuatTemas)->forceFill(['is_pranota_ob' => true,
            'rekap_ob_temas_items' => [
                ['nomor_kontainer' => 'CONT-A', 'biaya' => 90000],
                ['nomor_kontainer' => 'CONT-B', 'biaya' => 50000],
            ],
        ]);
        [$selected, $common] = $this->split($record);
        $this->assertNull($common);
        $this->assertSame([['nomor_kontainer' => 'CONT-A', 'biaya' => 45000.0]], $selected->rekap_ob_temas_items);
        $this->assertEquals(45000, $selected->apportioned['total_biaya']);
        $this->assertEquals(90000, $record->rekap_ob_temas_items[0]['biaya']);
    }

    private function split($record): array
    {
        $resolver = new RekapBlService(collect(), collect([
            (new Manifest)->forceFill(['nomor_bl' => '01, 02', 'nomor_kontainer' => 'CONT-A']),
            (new Manifest)->forceFill(['nomor_bl' => '03', 'nomor_kontainer' => 'CONT-B']),
        ]));

        return (new ReflectionMethod(RekapBiayaKapalController::class, 'splitCostForBl'))
            ->invoke(new RekapBiayaKapalController, $record, $resolver, 'KM TEST', 'V01', ['01']);
    }
}

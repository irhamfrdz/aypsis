<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\Manifest;
use App\Models\PranotaOb;
use App\Services\RekapBlService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class RekapBlGroupingTest extends TestCase
{
    public function test_numeric_bl_letter_suffixes_use_one_shared_normalization(): void
    {
        $method = new ReflectionMethod(RekapBiayaKapalController::class, 'normalizeBl');
        foreach (['01A' => '01', '01B' => '01', ' 01c ' => '01', '01-A' => '01', '01A-2' => '01', '02B' => '02', 'ABC123A' => 'ABC123A'] as $input => $expected) {
            $this->assertSame($expected, RekapBlService::normalize($input));
            $this->assertSame($expected, $method->invoke(new RekapBiayaKapalController, $input));
        }
    }

    public function test_bl_options_container_counts_and_cost_filter_combine_all_letter_parts(): void
    {
        $resolver = new RekapBlService(collect(), collect([
            (new Manifest)->forceFill(['nomor_bl' => '01A', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20ft']),
            (new Manifest)->forceFill(['nomor_bl' => '01B', 'nomor_kontainer' => 'CONT-B', 'size_kontainer' => '40ft']),
            (new Manifest)->forceFill(['nomor_bl' => '02A', 'nomor_kontainer' => 'CONT-C', 'size_kontainer' => '20ft']),
        ]));
        $this->assertSame(['01', '02'], $resolver->available()->all());
        $this->assertSame(['01' => 2, '02' => 1], $resolver->containerCounts());
        $this->assertSame(['CONT-A', 'CONT-B'], $resolver->containers(['01'])->all());
        $this->assertSame(1.0, $resolver->ratio(['nomor_bl' => '01A, 01B'], '01'));
        $record = (new PranotaOb)->forceFill(['is_pranota_ob' => true, 'rekap_ob_items' => [
            ['nomor_kontainer' => 'CONT-A', 'biaya' => 40000],
            ['nomor_kontainer' => 'CONT-B', 'biaya' => 60000],
            ['nomor_kontainer' => 'CONT-C', 'biaya' => 50000],
        ]]);
        [$selected, $common] = (new ReflectionMethod(RekapBiayaKapalController::class, 'splitCostForBl'))
            ->invoke(new RekapBiayaKapalController, $record, $resolver, 'KM TEST', 'V01', ['01']);
        $this->assertNull($common);
        $this->assertSame(['CONT-A', 'CONT-B'], array_column($selected->rekap_ob_items, 'nomor_kontainer'));
        $this->assertEquals(100000, $selected->apportioned['total_biaya']);
    }
}

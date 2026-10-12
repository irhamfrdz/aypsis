<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalTrucking;
use App\Models\Manifest;
use App\Services\RekapBlService;
use App\Services\RekapTruckingService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class RekapTruckingServiceTest extends TestCase
{
    public function test_size_totals_adjustments_and_tax_are_allocated_without_changing_invoice_totals(): void
    {
        $detail = (new BiayaKapalTrucking)->forceFill(['no_bl' => [1, 2, 3, 4], 'nama_vendor' => 'Vendor',
            'total_biaya_20ft' => 300, 'total_biaya_40ft' => 400, 'container_adjustments' => [2 => -100],
            'subtotal' => 800, 'adjustment' => -40, 'pph' => 15.2, 'total_biaya' => 744.8]);
        $rows = (new RekapTruckingService)->rows($detail, $this->manifests());
        $this->assertSame(['CONT-A', 'CONT-B', 'CONT-C', 'Cargo - Beras'], $rows->pluck('label')->all());
        $this->assertEquals(200, $rows[0]['subtotal']);
        $this->assertEquals(100, $rows[1]['subtotal']);
        $this->assertEquals(400, $rows[2]['subtotal']);
        $this->assertEquals(100, $rows[3]['subtotal']);
        foreach (['subtotal' => 800, 'adjustment' => -40, 'pph' => 15.2, 'total' => 744.8] as $field => $amount) {
            $this->assertEqualsWithDelta($amount, $rows->sum($field), 0.001);
        }
    }

    public function test_missing_manifest_and_legacy_size_totals_preserve_rounding(): void
    {
        $detail = (new BiayaKapalTrucking)->forceFill(['no_bl' => [1, 99, 99], 'subtotal' => 1.01, 'adjustment' => -0.01, 'pph' => 0.03, 'total_biaya' => 0.97]);
        $rows = (new RekapTruckingService)->rows($detail, $this->manifests());
        $this->assertCount(2, $rows);
        $this->assertSame('Tanpa nomor kontainer (manifest 99)', $rows[1]['label']);
        $this->assertEquals(1.01, $rows->sum('subtotal'));
        $this->assertEquals(0.51, $rows[0]['subtotal']);
        $this->assertEquals(0.97, $rows->sum('total'));
    }

    public function test_bl_filter_selects_container_costs_using_manifest_bl_instead_of_manifest_id(): void
    {
        $detail = (new BiayaKapalTrucking)->forceFill(['kapal' => 'KM TEST', 'voyage' => 'V01', 'no_bl' => [1, 2, 3, 4],
            'total_biaya_20ft' => 300, 'total_biaya_40ft' => 400, 'container_adjustments' => [2 => -100],
            'subtotal' => 800, 'adjustment' => -40, 'pph' => 15.2, 'total_biaya' => 744.8]);
        $detail->rekap_trucking_rows = (new RekapTruckingService)->rows($detail, $this->manifests());
        $record = (new BiayaKapal)->forceFill(['nominal' => 800, 'ppn' => 0]);
        $controller = new RekapBiayaKapalController;
        foreach ((new ReflectionProperty($controller, 'relations'))->getValue($controller) as $relation) {
            $record->setRelation($relation, collect());
        }
        $record->setRelation('truckingDetails', collect([$detail]));
        [$selected, $common] = (new ReflectionMethod($controller, 'splitCostForBl'))
            ->invoke($controller, $record, new RekapBlService(collect(), $this->manifests()), 'KM TEST', 'V01', ['01']);
        $this->assertNull($common);
        $this->assertEqualsWithDelta(186.2, $selected->apportioned['total_biaya'], 0.001);
        $this->assertSame(['CONT-A'], $selected->truckingDetails[0]->rekap_trucking_rows->pluck('label')->all());
    }

    private function manifests()
    {
        return collect([
            (new Manifest)->forceFill(['id' => 1, 'nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20ft']),
            (new Manifest)->forceFill(['id' => 2, 'nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B', 'size_kontainer' => '20ft']),
            (new Manifest)->forceFill(['id' => 3, 'nomor_bl' => '03', 'nomor_kontainer' => 'CONT-C', 'size_kontainer' => '40ft']),
            (new Manifest)->forceFill(['id' => 4, 'nomor_bl' => '04', 'nomor_kontainer' => 'CARGO', 'tipe_kontainer' => 'Cargo', 'nama_barang' => 'Beras']),
        ]);
    }
}

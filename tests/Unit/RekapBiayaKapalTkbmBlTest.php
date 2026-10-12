<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalTkbm;
use App\Models\Manifest;
use App\Models\PricelistTkbm;
use App\Services\RekapBlService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class RekapBiayaKapalTkbmBlTest extends TestCase
{
    public function test_tkbm_without_explicit_bl_is_allocated_by_selected_containers_and_tariffs(): void
    {
        $controller = new RekapBiayaKapalController;
        $invoice = (new BiayaKapal)->forceFill(['nominal' => 400, 'ppn' => 0, 'pph' => 0]);
        foreach ((new ReflectionProperty($controller, 'relations'))->getValue($controller) as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $details = collect();
        foreach ([['20FT FULL', 2, 100], ['40FT FULL', 1, 200]] as [$name, $quantity, $tariff]) {
            $detail = (new BiayaKapalTkbm)->forceFill([
                'kapal' => 'KM TEST', 'voyage' => 'V01', 'no_referensi' => 'KTKBM-01',
                'jumlah' => $quantity, 'tarif' => $tariff, 'subtotal' => $quantity * $tariff,
                'total_nominal' => 400, 'pph' => 0, 'grand_total' => 400,
            ]);
            $detail->setRelation('pricelistTkbm', (new PricelistTkbm)->forceFill(['nama_barang' => $name]));
            $details->push($detail);
        }
        $invoice->setRelation('tkbmDetails', $details);
        $invoice->apportioned = $controller->getApportionedCostForRecord($invoice, 'KM TEST', 'V01');

        $manifests = collect([
            (new Manifest)->forceFill(['nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20', 'nama_barang' => 'FULL']),
            (new Manifest)->forceFill(['nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B', 'size_kontainer' => '20', 'nama_barang' => 'FULL']),
            (new Manifest)->forceFill(['nomor_bl' => '02', 'nomor_kontainer' => 'CONT-C', 'size_kontainer' => '40', 'nama_barang' => 'FULL']),
        ]);
        $resolver = new RekapBlService(collect(), $manifests);

        [$selected, $common] = (new ReflectionMethod($controller, 'splitCostForBl'))
            ->invoke($controller, $invoice, $resolver, 'KM TEST', 'V01', ['01']);

        $this->assertNull($common);
        $this->assertNotNull($selected);
        $this->assertEqualsWithDelta(100, $selected->apportioned['total_biaya'], 0.001);
        $this->assertCount(2, $selected->tkbmDetails);
    }
}

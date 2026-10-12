<?php

namespace Tests\Unit;

use App\Http\Controllers\RekapBiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalBarang;
use App\Models\Manifest;
use App\Models\PricelistBuruh;
use App\Services\RekapBlService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class RekapBiayaKapalBuruhBreakdownTest extends TestCase
{
    public function test_buruh_costs_are_broken_down_by_container_with_cargo_separate(): void
    {
        $controller = new RekapBiayaKapalController;
        $invoice = (new BiayaKapal)->forceFill([
            'nominal' => 240000, 'ppn' => 24000, 'pph' => 4800, 'total_biaya' => 259200,
        ]);
        foreach ((new ReflectionProperty($controller, 'relations'))->getValue($controller) as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $details = collect();
        foreach ([
            ['KONTAINER 20 FULL', '20', 'Full', 2, 50000],
            ['KONTAINER 40 FULL', '40', 'Full', 1, 50000],
            ['CARGO', null, null, 10, 8000],
        ] as [$name, $size, $type, $quantity, $tariff]) {
            $detail = (new BiayaKapalBarang)->forceFill([
                'kapal' => 'KM TEST', 'voyage' => 'V01', 'jumlah' => $quantity,
                'tarif' => $tariff, 'subtotal' => $quantity * $tariff, 'adjustment' => 10000,
            ]);
            $detail->setRelation('pricelistBuruh', (new PricelistBuruh)->forceFill([
                'barang' => $name, 'size' => $size, 'tipe' => $type,
            ]));
            $details->push($detail);
        }
        $invoice->setRelation('barangDetails', $details);
        $invoice->apportioned = $controller->getApportionedCostForRecord($invoice, 'KM TEST', 'V01');

        $resolver = new RekapBlService(collect(), collect([
            (new Manifest)->forceFill(['nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20', 'nama_barang' => 'FULL']),
            (new Manifest)->forceFill(['nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B', 'size_kontainer' => '20', 'nama_barang' => 'FULL']),
            (new Manifest)->forceFill(['nomor_bl' => '02', 'nomor_kontainer' => 'CONT-C', 'size_kontainer' => '40', 'nama_barang' => 'FULL']),
        ]));
        [$selected, $common] = (new ReflectionMethod($controller, 'splitCostForBl'))
            ->invoke($controller, $invoice, $resolver, 'KM TEST', 'V01', ['01']);

        $this->assertNull($common);
        $this->assertNotNull($selected);
        $this->assertEqualsWithDelta(86400, $selected->apportioned['total_biaya'], 0.01);

        $rows = (new ReflectionMethod($controller, 'allocateBuruhRows'))
            ->invoke($controller, $selected, $resolver->containerDetails(['01']), $resolver->containerDetails(), 'KM TEST', 'V01');

        $this->assertSame(['CONT-A'], $rows->where('jenis', 'kontainer')->pluck('label')->all());
        $this->assertSame('CARGO', $rows->firstWhere('jenis', 'barang')['label']);
        $this->assertEqualsWithDelta($selected->apportioned['total_biaya'], $rows->sum('total'), 0.01);
        $this->assertEqualsWithDelta($selected->apportioned['ppn'], $rows->sum('ppn'), 0.01);
        $this->assertEqualsWithDelta($selected->apportioned['pph'], $rows->sum('pph'), 0.01);
    }
}

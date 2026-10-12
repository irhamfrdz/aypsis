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
    public function test_cargo_only_bl_receives_its_labour_cost_without_container_costs(): void
    {
        $controller = new RekapBiayaKapalController;
        $invoice = (new BiayaKapal)->forceFill(['nominal' => 140000, 'ppn' => 0, 'pph' => 0]);
        foreach ((new ReflectionProperty($controller, 'relations'))->getValue($controller) as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $details = collect();
        foreach ([['KONTAINER 20 FULL', '20', 'Full', 1, 50000], ['CARGO', null, null, 8, 10000], ['FUSO', null, null, 1, 10000]] as [$name, $size, $type, $quantity, $tariff]) {
            $detail = (new BiayaKapalBarang)->forceFill(['kapal' => 'KM TEST', 'voyage' => 'V01',
                'jumlah' => $quantity, 'tarif' => $tariff, 'subtotal' => $quantity * $tariff]);
            $detail->setRelation('pricelistBuruh', (new PricelistBuruh)->forceFill(['barang' => $name, 'size' => $size, 'tipe' => $type]));
            $details->push($detail);
        }
        $invoice->setRelation('barangDetails', $details);
        $resolver = new RekapBlService(collect(), collect([
            (new Manifest)->forceFill(['nomor_bl' => '01', 'tipe_kontainer' => 'Cargo', 'kuantitas' => 2]),
            (new Manifest)->forceFill(['nomor_bl' => '02', 'tipe_kontainer' => 'Cargo', 'kuantitas' => 6]),
            (new Manifest)->forceFill(['nomor_bl' => '03', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20']),
        ]));
        $this->assertSame(0.25, $resolver->cargoRatio(['01']));
        [$selected, $common] = (new ReflectionMethod($controller, 'splitCostForBl'))
            ->invoke($controller, $invoice, $resolver, 'KM TEST', 'V01', ['01']);
        $this->assertNull($common);
        $this->assertEquals(20000, $selected->apportioned['total_biaya']);
        $rows = (new ReflectionMethod($controller, 'allocateBuruhRows'))->invoke($controller, $selected,
            $resolver->containerDetails(['01']), $resolver->containerDetails(), 'KM TEST', 'V01', $resolver->cargoRatio(['01']));
        $this->assertEquals(20000, $rows->firstWhere('label', 'CARGO')['total']);
        $this->assertEquals(2, $rows->firstWhere('label', 'CARGO')['jumlah']);
        $this->assertCount(0, $rows->where('jenis', 'kontainer'));
        $this->assertEquals(20000, $rows->sum('total'));
    }

    public function test_empty_packaging_does_not_make_a_full_container_look_empty(): void
    {
        $resolver = new RekapBlService(collect(), collect([
            (new Manifest)->forceFill([
                'nomor_bl' => '16', 'nomor_kontainer' => 'AYPU7501922',
                'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL Booking',
                'nama_barang' => 'SUSU MILANO 1000gr, KARDUS KOSONG',
            ]),
            (new Manifest)->forceFill([
                'nomor_bl' => '17', 'nomor_kontainer' => 'AXIU2933779',
                'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL Booking',
                'nama_barang' => 'SUSU MILANO 1000gr, DUS KOSONG',
            ]),
            (new Manifest)->forceFill([
                'nomor_bl' => '18', 'nomor_kontainer' => 'EMPTY-01',
                'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL',
                'nama_barang' => 'EMPTY CONTAINER',
            ]),
        ]));

        $this->assertSame(['AXIU2933779', 'AYPU7501922'],
            $resolver->containerDetails()->where('status', 'FULL')->pluck('nomor_kontainer')->all());
        $this->assertSame(['EMPTY-01'],
            $resolver->containerDetails()->where('status', 'EMPTY')->pluck('nomor_kontainer')->all());
    }

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

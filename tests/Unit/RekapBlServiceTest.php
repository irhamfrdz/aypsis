<?php

namespace Tests\Unit;

use App\Models\Bl;
use App\Models\Manifest;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Services\RekapBlService;
use PHPUnit\Framework\TestCase;

class RekapBlServiceTest extends TestCase
{
    private function resolver(): RekapBlService
    {
        return new RekapBlService(collect([
            (new Bl)->forceFill(['id' => 1, 'nomor_bl' => '01-1', 'nomor_kontainer' => 'CONT-A']),
            (new Bl)->forceFill(['id' => 2, 'nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B']),
        ]), collect([
            (new Manifest)->forceFill(['id' => 1, 'nomor_bl' => '03', 'nomor_kontainer' => 'CONT-C', 'prospek_id' => 7]),
        ]));
    }

    public function test_catalog_retains_bl_and_manifest_with_the_same_id(): void
    {
        $this->assertSame(['01', '02', '03'], $this->resolver()->available()->all());
    }

    public function test_containers_are_unique_and_follow_selected_bls(): void
    {
        $resolver = new RekapBlService(collect([
            (new Bl)->forceFill(['nomor_bl' => '01-1', 'nomor_kontainer' => 'CONT-A, CONT-B', 'size_kontainer' => '20ft', 'nama_barang' => 'Barang']),
            (new Bl)->forceFill(['nomor_bl' => '02', 'nomor_kontainer' => 'CONT-C', 'size_kontainer' => '40ft', 'nama_barang' => 'EMPTY']),
        ]), collect([
            (new Manifest)->forceFill(['nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20ft', 'nama_barang' => 'Container Full']),
        ]));

        $this->assertSame(['CONT-A', 'CONT-B', 'CONT-C'], $resolver->containers()->all());
        $this->assertSame(['CONT-A', 'CONT-B'], $resolver->containers(['01'])->all());
        $this->assertSame(['FULL', 'FULL', 'EMPTY'], $resolver->containerDetails()->pluck('status')->all());
        $this->assertSame(['20', '20', '40'], $resolver->containerDetails()->pluck('size')->all());
    }

    public function test_opslag_container_catalog_excludes_lcl_and_fcl_booking(): void
    {
        $manifests = collect([
            new Manifest(['nama_kapal' => 'KM TEST', 'no_voyage' => 'V01', 'nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A', 'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL']),
            new Manifest(['nama_kapal' => 'KM TEST', 'no_voyage' => 'V01', 'nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B', 'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL']),
            new Manifest(['nama_kapal' => 'KM TEST', 'no_voyage' => 'V01', 'nomor_bl' => '03', 'nomor_kontainer' => 'CONT-L', 'size_kontainer' => '20ft', 'tipe_kontainer' => 'LCL']),
            new Manifest(['nama_kapal' => 'KM TEST', 'no_voyage' => 'V01', 'nomor_bl' => '04', 'nomor_kontainer' => 'CONT-F', 'size_kontainer' => '20ft', 'tipe_kontainer' => 'FCL']),
        ]);
        $prospects = collect([
            new Prospek(['nama_kapal' => 'KM TEST', 'no_voyage' => 'V01', 'tipe' => 'FCL Booking', 'nomor_kontainer' => 'CONT-F']),
        ]);
        $rows = (new RekapBlService(collect(), $manifests))->opslagContainerDetails($prospects);
        $this->assertSame(['CONT-A', 'CONT-B'], $rows->pluck('nomor_kontainer')->all());
        $this->assertSame(['01', '02'], $rows->pluck('nomor_bl')->all());
    }

    public function test_tkbm_invoice_adjustment_is_counted_once_and_evenly_allocated(): void
    {
        $invoice = (new \App\Models\BiayaKapal)->forceFill(['nominal' => 1100, 'ppn' => 0]);
        foreach (['buruhBatamDetails', 'buruhBongkarDetails', 'barangDetails', 'tenagaKerjaDetails', 'airDetails'] as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $details = collect();
        foreach ([['KONTAINER 20FT FULL', 2, 400], ['KONTAINER 40FT EMPTY', 1, 200]] as [$name, $quantity, $rate]) {
            $detail = (new \App\Models\BiayaKapalTkbm)->forceFill([
                'kapal' => 'KM TEST', 'voyage' => 'V01', 'no_referensi' => 'REF-01',
                'jumlah' => $quantity, 'tarif' => $rate, 'subtotal' => $quantity * $rate,
                'total_nominal' => 1100, 'adjustment' => 100,
                'pph' => 22, 'grand_total' => 1078,
            ]);
            $detail->setRelation('pricelistTkbm', (new \App\Models\PricelistTkbm)->forceFill(['nama_barang' => $name]));
            $details->push($detail);
        }
        $invoice->setRelation('tkbmDetails', $details);
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        $full = $controller->getApportionedCostForRecord($invoice, 'KM TEST', 'V01');
        $this->assertEqualsWithDelta(1078, $full['total_biaya'], 0.001);
        $invoice->apportioned = $full;

        $allocate = new \ReflectionMethod($controller, 'allocateTkbmRows');
        $containers = collect([
            ['nomor_kontainer' => 'CONT-A', 'size' => '20', 'status' => 'FULL'],
            ['nomor_kontainer' => 'CONT-B', 'size' => '20', 'status' => 'FULL'],
            ['nomor_kontainer' => 'CONT-C', 'size' => '40', 'status' => 'EMPTY'],
        ]);
        $rows = $allocate->invoke($controller, $invoice, $containers, 'KM TEST', 'V01');
        $this->assertCount(3, $rows);
        $this->assertSame('EMPTY', $rows->last()['status']);
        $this->assertEqualsWithDelta(100, $rows->sum('adjustment'), 0.001);
        $this->assertEqualsWithDelta(22, $rows->sum('pph'), 0.001);
        $this->assertEqualsWithDelta(1078, $rows->sum('total'), 0.001);
        $this->assertEqualsWithDelta(33.34, $rows->last()['adjustment'], 0.001);

        $invoice->apportioned = ['nominal' => 550, 'ppn' => 0, 'pph' => 11, 'total_biaya' => 539];
        $selectedRows = $allocate->invoke($controller, $invoice, $containers->take(2), 'KM TEST', 'V01');
        $this->assertEqualsWithDelta(50, $selectedRows->sum('adjustment'), 0.001);
        $this->assertEqualsWithDelta(539, $selectedRows->sum('total'), 0.001);
    }

    public function test_tkbm_container_cost_uses_its_tariff_and_separates_cargo(): void
    {
        $invoice = (new \App\Models\BiayaKapal)->forceFill(['nominal' => 3951700, 'ppn' => 0]);
        foreach (['buruhBatamDetails', 'buruhBongkarDetails', 'barangDetails', 'tenagaKerjaDetails', 'airDetails'] as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $details = collect();
        foreach ([
            ['KONTAINER 20FT FULL', 65, 39000],
            ['KONTAINER 40FT FULL', 14, 80000],
            ['CARGO', 129, 2300],
        ] as [$name, $quantity, $tariff]) {
            $detail = (new \App\Models\BiayaKapalTkbm)->forceFill([
                'kapal' => 'KM ALKEN PESONA', 'voyage' => 'AS14JP26', 'no_referensi' => '98/08JUP/2026',
                'jumlah' => $quantity, 'tarif' => $tariff, 'subtotal' => $quantity * $tariff,
                'total_nominal' => 3951700, 'adjustment' => 0, 'pph' => 79034, 'grand_total' => 3872666,
            ]);
            $detail->setRelation('pricelistTkbm', (new \App\Models\PricelistTkbm)->forceFill(['nama_barang' => $name]));
            $details->push($detail);
        }
        $invoice->setRelation('tkbmDetails', $details);
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        $invoice->apportioned = $controller->getApportionedCostForRecord($invoice, 'KM ALKEN PESONA', 'AS14JP26');
        $containers = collect();
        for ($index = 0; $index < 65; $index++) {
            $containers->push(['nomor_kontainer' => '20-'.$index, 'size' => '20', 'status' => 'FULL']);
        }
        for ($index = 0; $index < 14; $index++) {
            $containers->push(['nomor_kontainer' => '40-'.$index, 'size' => '40', 'status' => 'FULL']);
        }

        $rows = (new \ReflectionMethod($controller, 'allocateTkbmRows'))
            ->invoke($controller, $invoice, $containers, 'KM ALKEN PESONA', 'AS14JP26');
        $this->assertCount(80, $rows);
        $this->assertEqualsWithDelta(39000, $rows->first()['biaya'], 0.001);
        $this->assertEqualsWithDelta(780, $rows->first()['pph'], 0.001);
        $this->assertEqualsWithDelta(80000, $rows[78]['biaya'], 0.001);
        $this->assertSame('CARGO', $rows->last()['label']);
        $this->assertEqualsWithDelta(129, $rows->last()['jumlah'], 0.001);
        $this->assertEqualsWithDelta(2300, $rows->last()['tarif'], 0.001);
        $this->assertEqualsWithDelta(296700, $rows->last()['biaya'], 0.001);
        $this->assertEqualsWithDelta(5934, $rows->last()['pph'], 0.001);
        $this->assertEqualsWithDelta(79034, $rows->sum('pph'), 0.001);
        $this->assertEqualsWithDelta(3872666, $rows->sum('total'), 0.001);
    }

    public function test_opp_opt_breakdown_uses_linked_manifests_and_respects_bl_filter(): void
    {
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        $invoice = new \App\Models\BiayaKapal;
        foreach ((new \ReflectionProperty($controller, 'relations'))->getValue($controller) as $relation) {
            $invoice->setRelation($relation, collect());
        }
        foreach (['buruhBatamDetails', 'buruhBongkarDetails', 'tenagaKerjaDetails'] as $relation) {
            $invoice->setRelation($relation, collect());
        }
        $first = (new \App\Models\BiayaKapalOppOpt)->forceFill([
            'kapal' => 'KM TEST', 'voyage' => 'V01', 'klasifikasi' => 'OPP',
            'tarif' => 100, 'subtotal' => 200,
        ]);
        $first->setRelation('manifests', collect([
            (new Manifest)->forceFill(['id' => 1, 'nomor_bl' => '01', 'nomor_kontainer' => 'CONT-A']),
            (new Manifest)->forceFill(['id' => 2, 'nomor_bl' => '02', 'nomor_kontainer' => 'CONT-B']),
        ]));
        $second = (new \App\Models\BiayaKapalOppOpt)->forceFill([
            'kapal' => 'KM TEST', 'voyage' => 'V01', 'klasifikasi' => 'OPT',
            'tarif' => 50, 'subtotal' => 50,
        ]);
        $second->setRelation('manifests', collect([$first->manifests->first()]));
        $invoice->setRelation('oppOptDetails', collect([$first, $second]));
        $invoice->apportioned = ['nominal' => 250, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 250];

        $allocate = new \ReflectionMethod($controller, 'allocateOppOptRows');
        $rows = $allocate->invoke($controller, $invoice, collect(), 'KM TEST', 'V01');
        $this->assertSame(['CONT-A', 'CONT-B', 'CONT-A'], $rows->pluck('kontainer')->all());
        $this->assertEqualsWithDelta(250, $rows->sum('biaya'), 0.001);

        [$selected, $common] = (new \ReflectionMethod($controller, 'splitCostForBl'))
            ->invoke($controller, $invoice, $this->resolver(), 'KM TEST', 'V01', '01');
        $this->assertNull($common);
        $this->assertEqualsWithDelta(150, $selected->apportioned['total_biaya'], 0.001);
        $selectedRows = $allocate->invoke($controller, $selected, collect(), 'KM TEST', 'V01');
        $this->assertSame(['CONT-A', 'CONT-A'], $selectedRows->pluck('kontainer')->all());
        $this->assertEqualsWithDelta(150, $selectedRows->sum('biaya'), 0.001);
    }

    public function test_opslag_uses_voyage_container_numbers_only_when_category_count_matches(): void
    {
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        $invoice = new \App\Models\BiayaKapal;
        $invoice->setRelation('oppOptDetails', collect([
            (new \App\Models\BiayaKapalOppOpt)->forceFill([
                'kapal' => 'KM TEST', 'voyage' => 'V01', 'klasifikasi' => 'opslag',
                'jenis_ukuran' => '20ft Full', 'jumlah' => 2, 'tarif' => 3000, 'subtotal' => 6000,
            ]),
            (new \App\Models\BiayaKapalOppOpt)->forceFill([
                'kapal' => 'KM TEST', 'voyage' => 'V01', 'klasifikasi' => 'opslag',
                'jenis_ukuran' => 'Mobil', 'jumlah' => 3, 'tarif' => 1000, 'subtotal' => 3000,
            ]),
        ]));
        foreach ($invoice->oppOptDetails as $detail) {
            $detail->setRelation('manifests', collect());
        }
        $containers = collect([
            ['nomor_kontainer' => 'CONT-A', 'size' => '20', 'status' => 'FULL'],
            ['nomor_kontainer' => 'CONT-B', 'size' => '20', 'status' => 'FULL'],
        ]);
        $allocate = new \ReflectionMethod($controller, 'allocateOppOptRows');
        $rows = $allocate->invoke($controller, $invoice, $containers, 'KM TEST', 'V01');
        $this->assertSame(['CONT-A', 'CONT-B', 'Mobil'], $rows->pluck('kontainer')->all());
        $this->assertSame([true, true, false], $rows->pluck('per_kontainer')->all());
        $this->assertSame(3.0, $rows->last()['jumlah']);
        $this->assertEqualsWithDelta(9000, $rows->sum('biaya'), 0.001);

        $rows = $allocate->invoke($controller, $invoice, $containers->take(1), 'KM TEST', 'V01');
        $this->assertSame('20ft Full', $rows->first()['kontainer']);
        $this->assertFalse($rows->first()['per_kontainer']);
        $this->assertSame(2.0, $rows->first()['jumlah']);
    }

    public function test_direct_bl_container_and_unlinked_costs(): void
    {
        $resolver = $this->resolver();
        $this->assertSame(1.0, $resolver->ratio(['nomor_bl' => '01-2'], '01'));
        $this->assertSame(0.0, $resolver->ratio(['no_bl' => '02'], '01'));
        $this->assertSame(1.0, $resolver->ratio(['no_kontainer' => 'CONT-A'], '01'));
        $this->assertSame(0.5, $resolver->ratio(['no_bl' => ['01', '02']], '01'));
        $this->assertNull($resolver->ratio(['jenis_biaya' => 'Air'], '01'));
    }

    public function test_claim_and_labor_use_actual_container_amounts(): void
    {
        $resolver = $this->resolver();
        foreach (['biaya_klaim', 'nominal'] as $field) {
            $row = ['kontainer_ids' => [
                ['bl_id' => 1, $field => 100000],
                ['bl_id' => 2, $field => 300000],
            ]];
            $this->assertSame(0.25, $resolver->ratio($row, '01'));
            $this->assertSame(0.75, $resolver->ratio($row, '02'));
            $row['kontainer_ids'] = [4 => $row['kontainer_ids'][0], 9 => $row['kontainer_ids'][1]];
            $this->assertSame(0.25, $resolver->ratio($row, '01'));
        }
        $this->assertSame(0.5, $resolver->ratio(['kontainer_ids' => [['bl_id' => 1], ['bl_id' => 2]]], '01'));
    }

    public function test_multiple_selected_bls_combine_their_costs_without_duplicate_allocation(): void
    {
        $resolver = $this->resolver();
        $row = ['kontainer_ids' => [
            ['bl_id' => 1, 'biaya_klaim' => 100000],
            ['bl_id' => 2, 'biaya_klaim' => 300000],
        ]];
        $this->assertSame(1.0, $resolver->ratio($row, ['01', '02', '01-1']));
        $this->assertSame(0.25, $resolver->ratio($row, ['01', '03']));
        $this->assertSame(1.0, $resolver->ratio(['no_bl' => ['01', '02']], ['01', '02']));
        $this->assertSame(0.0, $resolver->ratio(['nomor_bl' => '03'], ['01', '02']));
        $this->assertNull($resolver->ratio(['jenis_biaya' => 'Air'], ['01', '02']));
        $this->assertSame(1.0, $resolver->transportRatio(new \App\Models\SuratJalanBongkaran(['no_bl' => '01-1']), 'KM JALESMAS', 'V01', ['01', '02', '01-2']));
    }

    public function test_transport_uses_manifest_prospek_and_selected_voyage(): void
    {
        $sj = new SuratJalan;
        $target = new Prospek(['nama_kapal' => 'KM JALESMAS', 'no_voyage' => 'V01']);
        $target->id = 7;
        $other = new Prospek(['nama_kapal' => 'KM JALESMAS', 'no_voyage' => 'V02', 'nomor_kontainer' => 'CONT-A']);
        $sj->setRelation('prospeks', collect([$target, $other]));
        $resolver = $this->resolver();
        $this->assertSame(1.0, $resolver->transportRatio($sj, 'KM JALESMAS', 'V01', '03'));
        $this->assertSame(0.0, $resolver->transportRatio($sj, 'KM JALESMAS', 'V01', '01'));
        $this->assertSame(1.0, $resolver->transportRatio(new \App\Models\SuratJalanBongkaran(['no_bl' => '01-1']), 'KM JALESMAS', 'V01', '01'));
    }

    public function test_controller_filters_uang_jalan_and_vendor_costs_and_separates_unlinked_costs(): void
    {
        $sj = new SuratJalan;
        $prospek = new Prospek(['nama_kapal' => 'KM JALESMAS', 'no_voyage' => 'V01']);
        $prospek->id = 7;
        $sj->setRelation('prospeks', collect([$prospek]));
        $method = new \ReflectionMethod(\App\Http\Controllers\RekapBiayaKapalController::class, 'splitCostForBl');
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        foreach ([new \App\Models\UangJalan, new \App\Models\TagihanSupirVendor] as $record) {
            $record->is_uang_jalan = $record instanceof \App\Models\UangJalan;
            $record->is_tagihan_vendor = $record instanceof \App\Models\TagihanSupirVendor;
            $record->apportioned = ['nominal' => 125000, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 125000];
            $record->setRelation('suratJalan', $sj);
            [$selected, $common] = $method->invoke($controller, $record, $this->resolver(), 'KM JALESMAS', 'V01', '03');
            $this->assertSame(125000.0, $selected->apportioned['total_biaya']);
            $this->assertNull($common);
            [$selected, $common] = $method->invoke($controller, $record, $this->resolver(), 'KM JALESMAS', 'V01', '01');
            $this->assertNull($selected);
            $this->assertNull($common);
        }
        $usage = new \App\Models\StockAmprahanUsage;
        $usage->apportioned = ['nominal' => 9000, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 9000];
        [$selected, $common] = $method->invoke($controller, $usage, $this->resolver(), 'KM JALESMAS', 'V01', '03');
        $this->assertNull($selected);
        $this->assertSame(9000.0, $common->apportioned['total_biaya']);
    }

    public function test_location_filter_distinguishes_transport_sources_and_stock_location(): void
    {
        $method = new \ReflectionMethod(\App\Http\Controllers\RekapBiayaKapalController::class, 'filterRecordLocation');
        $controller = new \App\Http\Controllers\RekapBiayaKapalController;
        foreach (['surat_jalan_id' => 'jakarta', 'surat_jalan_bongkaran_id' => 'jakarta', 'surat_jalan_bongkaran_batam_id' => 'batam'] as $field => $location) {
            $record = new \App\Models\UangJalan([$field => 1]);
            $record->is_uang_jalan = true;
            $this->assertTrue($method->invoke($controller, $record, $location, 'KM JALESMAS', 'V01'));
            $this->assertFalse($method->invoke($controller, $record, $location === 'jakarta' ? 'batam' : 'jakarta', 'KM JALESMAS', 'V01'));
        }
        $usage = new \App\Models\StockAmprahanUsage;
        $usage->is_amprahan = true;
        $usage->setRelation('stockAmprahan', new \App\Models\StockAmprahan(['lokasi' => 'Gudang Batam']));
        $this->assertTrue($method->invoke($controller, $usage, 'batam', 'KM JALESMAS', 'V01'));
        $this->assertFalse($method->invoke($controller, $usage, 'jakarta', 'KM JALESMAS', 'V01'));
        $unknown = new \App\Models\PranotaOb;
        $this->assertTrue($method->invoke($controller, $unknown, '', 'KM JALESMAS', 'V01'));
        $this->assertFalse($method->invoke($controller, $unknown, 'batam', 'KM JALESMAS', 'V01'));
    }
}

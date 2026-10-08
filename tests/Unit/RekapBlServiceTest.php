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
}

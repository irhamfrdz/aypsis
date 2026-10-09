<?php

namespace Tests\Unit;

use App\Models\PranotaUangRit;
use App\Models\PranotaUangRitKenek;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Services\RekapBlService;
use App\Services\RekapUangRitService;
use PHPUnit\Framework\TestCase;

class RekapUangRitServiceTest extends TestCase
{
    public function test_bl_filter_splits_rit_costs_and_keeps_unlinked_costs_separate(): void
    {
        $record = (new PranotaUangRit)->forceFill([
            'is_pranota_uang_rit' => true,
            'rekap_rit_items' => [
                ['nomor' => 'SJ-01', 'biaya' => 80000, 'surat_jalan' => (new SuratJalanBongkaran)->forceFill(['no_bl' => '01'])],
                ['nomor' => 'SJ-02', 'biaya' => 90000, 'surat_jalan' => (new SuratJalanBongkaran)->forceFill(['no_bl' => '02'])],
                ['nomor' => 'SJ-UMUM', 'biaya' => 10000, 'surat_jalan' => new SuratJalanBongkaran],
            ],
        ]);
        $method = new \ReflectionMethod(\App\Http\Controllers\RekapBiayaKapalController::class, 'splitCostForBl');
        [$selected, $common] = $method->invoke(new \App\Http\Controllers\RekapBiayaKapalController,
            $record, new RekapBlService(collect(), collect()), 'Kapal A', 'V01', ['01']);
        $this->assertEquals(80000, $selected->apportioned['total_biaya']);
        $this->assertEquals(10000, $common->apportioned['total_biaya']);
        $this->assertSame(['SJ-01'], collect($selected->rekap_rit_items)->pluck('nomor')->all());
        $this->assertSame(['SJ-UMUM'], collect($common->rekap_rit_items)->pluck('nomor')->all());
    }

    public function test_combined_pranota_allocates_only_matching_surat_jalan_and_excludes_payment_deductions(): void
    {
        $pranota = (new PranotaUangRit)->forceFill([
            'no_surat_jalan' => 'SJ-OTHER, SJ-MUAT, SJ-BONGKAR (Bongkaran)',
            'total_uang' => 300000, 'total_adjustment' => 30000,
            'uang_jalan' => 900000, 'total_hutang' => 50000, 'grand_total_bersih' => 280000,
        ]);
        $muat = (new SuratJalan)->setRelation('prospeks', collect([
            (new Prospek)->forceFill(['nama_kapal' => 'KM Kapal A', 'no_voyage' => 'V01']),
            (new Prospek)->forceFill(['nama_kapal' => 'Kapal B', 'no_voyage' => 'V02']),
        ]));
        $other = (new SuratJalan)->setRelation('prospeks', collect([
            (new Prospek)->forceFill(['nama_kapal' => 'Kapal B', 'no_voyage' => 'V02']),
        ]));
        $bongkar = (new SuratJalanBongkaran)->forceFill([
            'nama_kapal' => 'Kapal A', 'no_voyage' => 'V01', 'no_bl' => '01',
        ]);
        $entries = (new RekapUangRitService)->entries($pranota,
            collect(['SJ-OTHER' => $other, 'SJ-MUAT' => $muat]), collect(['SJ-BONGKAR' => $bongkar]),
            'Kapal A', 'V01', 'jakarta');

        $this->assertSame(['SJ-MUAT', 'SJ-BONGKAR'], $entries->pluck('nomor')->all());
        $this->assertEquals(165000, $entries->sum('biaya'));
        $resolver = new RekapBlService(collect(), collect());
        $this->assertSame(1.0, $resolver->transportRatio($bongkar, 'Kapal A', 'V01', '01'));
        $this->assertSame(0.0, $resolver->transportRatio($bongkar, 'Kapal A', 'V01', '02'));
    }

    public function test_kenek_uses_its_own_rit_amount_and_jakarta_location(): void
    {
        $pranota = (new PranotaUangRitKenek)->forceFill([
            'no_surat_jalan' => 'SJ-1 (Bongkaran)', 'uang_rit_kenek' => 50000,
        ]);
        $bongkar = collect(['SJ-1' => (new SuratJalanBongkaran)->forceFill([
            'nama_kapal' => 'Kapal A', 'no_voyage' => 'V01',
        ])]);
        $service = new RekapUangRitService;
        $this->assertEquals(50000, $service->entries($pranota, collect(), $bongkar, 'Kapal A', 'V01', '')->sum('biaya'));
        $this->assertTrue($service->entries($pranota, collect(), $bongkar, 'Kapal A', 'V01', 'batam')->isEmpty());
        $this->assertTrue($service->entries($pranota, collect(), $bongkar, 'Kapal A', 'V02', '')->isEmpty());
    }

    public function test_legacy_pranota_resolves_both_foreign_key_references(): void
    {
        $pranota = (new PranotaUangRit)->setRelation('suratJalan',
            (new SuratJalan)->forceFill(['no_surat_jalan' => 'SJ-1']))
            ->setRelation('suratJalanBongkaran',
                (new SuratJalanBongkaran)->forceFill(['nomor_surat_jalan' => 'SJB-1']));
        $this->assertSame([
            ['number' => 'SJ-1', 'bongkaran' => false],
            ['number' => 'SJB-1', 'bongkaran' => true],
        ], (new RekapUangRitService)->references($pranota)->all());
    }
}

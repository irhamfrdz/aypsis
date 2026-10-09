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
    public function test_driver_breakdown_preserves_pranota_links_and_filters_each_surat_jalan_by_bl(): void
    {
        $first = (new SuratJalanBongkaran)->forceFill([
            'nomor_surat_jalan' => 'SJ-01', 'no_bl' => '01', 'supir' => 'Supir A',
            'pengirim' => 'Pengirim A', 'no_kontainer' => 'CONT-A',
        ]);
        $second = (new SuratJalanBongkaran)->forceFill([
            'nomor_surat_jalan' => 'SJ-02', 'no_bl' => '02', 'supir' => 'Supir B',
        ]);
        $pranota = (new PranotaUangRit)->forceFill([
            'id' => 42, 'nomor_invoice' => 'PUR-42', 'supir_nama' => 'Supir A, Supir B',
            'is_pranota_uang_rit' => true, 'is_pranota_uang_rit_kenek' => false,
            'rekap_rit_items' => [
                ['nomor' => 'SJ-01', 'biaya' => 80000, 'surat_jalan' => $first],
                ['nomor' => 'SJ-02', 'biaya' => 90000, 'surat_jalan' => $second],
            ],
        ]);
        $rows = (new RekapUangRitService)->rowsForSuratJalan($pranota);

        $this->assertCount(2, $rows);
        $this->assertEquals(170000, $rows->sum(fn ($row) => $row->apportioned['total_biaya']));
        $this->assertSame([42, 42], $rows->pluck('id')->all());
        $this->assertSame(['Supir A', 'Supir B'], $rows->pluck('supir_nama')->all());
        $this->assertSame($first, $rows[0]->rekapSuratJalan);
        $this->assertSame('CONT-A', $rows[0]->rekapSuratJalan->no_kontainer);
        $this->assertCount(2, $pranota->rekap_rit_items);

        $method = new \ReflectionMethod(\App\Http\Controllers\RekapBiayaKapalController::class, 'splitCostForBl');
        $resolver = new RekapBlService(collect(), collect());
        [$selected, $common] = $method->invoke(new \App\Http\Controllers\RekapBiayaKapalController,
            $rows[0], $resolver, 'Kapal A', 'V01', ['01']);
        $this->assertEquals(80000, $selected->apportioned['total_biaya']);
        $this->assertNull($common);
        $this->assertSame($first, $selected->rekapSuratJalan);
        $this->assertSame([null, null], $method->invoke(new \App\Http\Controllers\RekapBiayaKapalController,
            $rows[1], $resolver, 'Kapal A', 'V01', ['01']));
    }

    public function test_kenek_breakdown_preserves_individual_names_amounts_and_bl_links(): void
    {
        $first = (new SuratJalanBongkaran)->forceFill([
            'nomor_surat_jalan' => 'SJ-01', 'no_bl' => '01', 'kenek' => 'Kenek A',
            'pengirim' => 'Pengirim A', 'no_kontainer' => 'CONT-A',
        ]);
        $second = (new SuratJalanBongkaran)->forceFill([
            'nomor_surat_jalan' => 'SJ-02', 'no_bl' => '02', 'kenek' => 'Kenek B',
        ]);
        $pranota = (new PranotaUangRitKenek)->forceFill([
            'id' => 43, 'nomor_invoice' => 'PNK-43', 'kenek_nama' => 'Kenek A, Kenek B',
            'is_pranota_uang_rit' => true, 'is_pranota_uang_rit_kenek' => true,
            'rekap_rit_items' => [
                ['nomor' => 'SJ-01', 'biaya' => 40000, 'surat_jalan' => $first],
                ['nomor' => 'SJ-02', 'biaya' => 50000, 'surat_jalan' => $second],
            ],
        ]);
        $rows = (new RekapUangRitService)->rowsForSuratJalan($pranota);
        $this->assertCount(2, $rows);
        $this->assertSame(['Kenek A', 'Kenek B'], $rows->pluck('kenek_nama')->all());
        $this->assertSame([43, 43], $rows->pluck('id')->all());
        $this->assertEquals(90000, $rows->sum(fn ($row) => $row->apportioned['total_biaya']));
        $this->assertSame($first, $rows[0]->rekapSuratJalan);
        $this->assertSame('CONT-A', $rows[0]->rekapSuratJalan->no_kontainer);
        $this->assertTrue($rows[0]->is_rit_detail);
        $this->assertCount(2, $pranota->rekap_rit_items);

        $method = new \ReflectionMethod(\App\Http\Controllers\RekapBiayaKapalController::class, 'splitCostForBl');
        $resolver = new RekapBlService(collect(), collect());
        [$selected, $common] = $method->invoke(new \App\Http\Controllers\RekapBiayaKapalController,
            $rows[0], $resolver, 'Kapal A', 'V01', ['01']);
        $this->assertEquals(40000, $selected->apportioned['total_biaya']);
        $this->assertNull($common);
        $this->assertSame('Kenek A', $selected->kenek_nama);
        $this->assertSame([null, null], $method->invoke(new \App\Http\Controllers\RekapBiayaKapalController,
            $rows[1], $resolver, 'Kapal A', 'V01', ['01']));
    }

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

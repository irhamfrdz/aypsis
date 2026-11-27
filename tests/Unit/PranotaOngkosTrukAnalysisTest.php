<?php

namespace Tests\Unit;

use App\Models\PranotaOngkosTruk;
use App\Models\PranotaOngkosTrukItem;
use App\Models\SuratJalan;
use App\Models\UangJalan;
use App\Services\PranotaOngkosTrukAnalysisService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class PranotaOngkosTrukAnalysisTest extends TestCase
{
    private function analyze(array $amounts): array
    {
        $items = [];
        foreach ($amounts as $index => $amount) {
            $sj = new SuratJalan(['no_plat' => $index ? 'B 9385 UIV' : 'B9385UIV', 'tujuan_pengambilan' => 'CIBITUNG', 'size' => '20']);
            foreach (['tujuanPengambilanRelation', 'tandaTerima', 'supirKaryawan', 'kenekKaryawan'] as $relation) {
                $sj->setRelation($relation, null);
            }
            $uj = $amount === null ? null : new UangJalan([
                'nomor_uang_jalan' => 'UJ-'.$index, 'jumlah_uang_jalan' => 545000,
                'jumlah_pelancar' => $amount, 'jumlah_total' => 545000 + $amount,
            ]);
            $uj?->setRelation('pranotaUangJalan', new Collection);
            $sj->setRelation('uangJalan', $uj);
            $item = new PranotaOngkosTrukItem(['type' => 'SuratJalan', 'no_surat_jalan' => 'SJ-'.$index, 'nominal' => 100]);
            $item->setRelation('suratJalan', $sj);
            $items[] = $item;
        }
        $pranota = Mockery::mock(PranotaOngkosTruk::class)->makePartial();
        $pranota->shouldReceive('load')->once()->andReturnSelf();
        $pranota->setRelation('items', new Collection($items));

        return (new PranotaOngkosTrukAnalysisService)->analyze($pranota);
    }

    public function test_compares_normalized_plates_and_identifies_pelancar_difference(): void
    {
        $result = $this->analyze([230000, 50000]);
        $this->assertCount(1, $result['summaries']);
        $this->assertSame(180000.0, $result['summaries'][0]['difference']);
        $this->assertStringContainsString('Pelancar', $result['summaries'][0]['changes']);
        $this->assertStringContainsString('180.000 terhadap no. 2', $result['rows'][0]['keterangan']);
    }

    public function test_equal_totals_are_not_reported_as_differences(): void
    {
        $this->assertCount(0, $this->analyze([50000, 50000])['summaries']);
    }

    public function test_missing_uang_jalan_is_explicitly_distinguished_from_zero_cost(): void
    {
        $result = $this->analyze([null, 50000]);
        $this->assertStringContainsString('belum tersedia', $result['rows'][0]['keterangan']);
        $this->assertStringContainsString('tanpa data UJ', $result['rows'][1]['keterangan']);
    }
}

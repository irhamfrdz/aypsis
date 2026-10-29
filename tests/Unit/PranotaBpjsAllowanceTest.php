<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\PranotaBpjsDetail;
use PHPUnit\Framework\TestCase;

class PranotaBpjsAllowanceTest extends TestCase
{
    public function test_burdens_use_saved_components_and_classify_non_employee_memberships(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'tipe_karyawan' => Karyawan::class, 'bpjs_kesehatan' => 200000,
            'bpjs_ketenagakerjaan' => 50000,
            'rincian_bpjs' => json_encode(['jht_biaya' => 185000.25, 'jht_hutang' => 100000,
                'jp_biaya' => 100000, 'jp_hutang' => 50000, 'jkk_hutang' => 20000,
                'noncrew_jht_hutang' => 10000, 'jkk_tunjangan' => 12000, 'jkm_tunjangan' => 15000]),
        ]);
        $employee = new Karyawan;
        $detail->setRelation('karyawan', $employee);
        $this->assertSame(180000.0, $detail->summaryBurden('kary_jht'));
        $this->assertSame(50000.0, $detail->summaryBurden('kary_jkn'));
        $this->assertSame(485000.25, $detail->summaryBurden('pt'));
        $employee->tipe_jkn = 'NON KARY';
        $this->assertSame(250000.0, $detail->summaryBurden('non_kary'));
        $this->assertSame(285000.25, $detail->summaryBurden('pt'));
        $employee->is_special_case = true;
        $this->assertSame(742000.25, $detail->summaryBurden('non_kary'));
        $detail->tunjangan_manual = ['non_kary' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
    }

    public function test_ppu_allowance_is_jkk_plus_jkm_for_ppu_only(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['jkk_tunjangan' => '13751.71', 'jkm_tunjangan' => '17189.64'], true);
        $employee = new Karyawan(['group_bp_jamsostek' => 'PPU']);
        $detail->setRelation('karyawan', $employee);
        $this->assertEqualsWithDelta(30941.35, $detail->tunjangan_ppu, 0.001);
        $detail->rincian_bpjs = ['jkk_tunjangan' => 12000.25, 'jkm_tunjangan' => 15000.50];
        $this->assertEqualsWithDelta(27000.75, $detail->tunjangan_ppu, 0.001);
        $employee->group_bp_jamsostek = 'BPU-CREW';
        $this->assertSame(0.0, $detail->tunjangan_ppu);
    }
}

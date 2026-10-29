<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\PranotaBpjsDetail;
use PHPUnit\Framework\TestCase;

class PranotaBpjsAllowanceTest extends TestCase
{
    public function test_non_kary_umkm_is_distinct_from_exact_non_kary_for_employee_burdens(): void
    {
        $employee = new Karyawan;
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['bpjs_ketenagakerjaan' => 50000,
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => 36000.25, 'jp_hutang' => 2000]),
        ]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM', ' non kary - umkm ', 'NON_KARY_UMKM', 'NON KARY-UMKM non PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $employee->tipe_jkn = $type;
            $this->assertSame(38000.25, $detail->summaryBurden('kary_jht'));
            $this->assertSame(50000.0, $detail->summaryBurden('kary_jkn'));
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
        }
        $employee->tipe_bp_jamsostek = 'NON KARY';
        $employee->tipe_jkn = 'NON KARY';
        $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
        $this->assertSame(0.0, $detail->summaryBurden('kary_jkn'));
        $this->assertSame(88000.25, $detail->summaryBurden('non_kary'));
    }

    public function test_umkm_non_pp_has_no_bpu_allowance_even_with_saved_manual_amount(): void
    {
        $employee = new Karyawan;
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['rincian_bpjs' => json_encode([
            'noncrew_jht_biaya' => 19000, 'noncrew_jkk_tunjangan' => 22250,
            'noncrew_jkm_tunjangan' => 3400,
        ])]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM non PP', ' non kary - umkm NON pp ', 'NON_KARY_UMKM_NON_PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(0.0, $detail->tunjangan_bpu);
            $this->assertSame(44650.0, $detail->summaryBurden('pt'));
        }
        $detail->tunjangan_manual = ['bpu' => 25650];
        $this->assertSame(0.0, $detail->tunjangan_bpu);
        $employee->tipe_bp_jamsostek = 'CREW';
        $this->assertSame(25650.0, $detail->tunjangan_bpu);
        $detail->tunjangan_manual = null;
        $this->assertSame(25650.0, $detail->tunjangan_bpu);
    }

    public function test_terminated_special_case_crew_retains_normal_employee_burdens(): void
    {
        foreach ([Karyawan::class, \App\Models\KaryawanTidakTetap::class] as $type) {
            $employee = new $type;
            $employee->setRawAttributes(['is_special_case' => true, 'tanggal_berhenti' => '2026-10-01',
                'tipe_bp_jamsostek' => 'CREW', 'tipe_jkn' => 'KARYAWAN']);
            $detail = new PranotaBpjsDetail;
            $detail->setRawAttributes([
                'tipe_karyawan' => $type, 'bpjs_ketenagakerjaan' => 50000,
                'jht_hutang' => 5720,
                'rincian_bpjs' => json_encode(['jht_hutang' => 0, 'jkk_hutang' => 5720,
                    'bpu_jkk_tunjangan' => 16530, 'bpu_jkm' => 3400]),
            ]);
            $detail->setRelation('karyawan', $employee);
            $this->assertSame(5720.0, $detail->summaryBurden('kary_jht'));
            $this->assertSame(50000.0, $detail->summaryBurden('kary_jkn'));
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
            $detail->rincian_bpjs = null;
            $this->assertSame(5720.0, $detail->summaryBurden('kary_jht'));
            $detail->tunjangan_manual = ['kary_jht' => 0];
            $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
        }
    }

    public function test_non_kary_employer_burden_uses_matching_jkn_and_ppu_totals(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'bpjs_kesehatan' => 229195.20, 'bpjs_ketenagakerjaan' => 57298.80,
            'rincian_bpjs' => json_encode(['jht_biaya' => 100000.8, 'jht_hutang' => 20000,
                'jkk_tunjangan' => 1000, 'jkm_tunjangan' => 2000, 'jp_biaya' => 10000,
                'jp_hutang' => 11000, 'noncrew_jht_biaya' => 6000]),
        ]);
        $employee = new Karyawan;
        $detail->setRelation('karyawan', $employee);
        $employee->group_jkn = ' jkn - kis ';
        $employee->tipe_jkn = ' non kary ';
        $this->assertSame(286494.0, $detail->summaryBurden('pt'));
        $employee->group_bp_jamsostek = 'ppu';
        $employee->tipe_bp_jamsostek = 'NON_KARY';
        $this->assertSame(430494.8, $detail->summaryBurden('pt'));
        $employee->group_jkn = 'LAIN';
        $this->assertSame(144000.8, $detail->summaryBurden('pt'));
        $employee->tipe_bp_jamsostek = 'KARYAWAN';
        $this->assertSame(116000.8, $detail->summaryBurden('pt'));
        $employee->group_jkn = 'JKN-KIS';
        $employee->is_special_case = true;
        $this->assertSame(286494.0, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = ['pt' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('pt'));
    }

    public function test_umkm_non_pp_uses_noncrew_jht_biaya_jkk_and_jkm(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'rincian_bpjs' => json_encode(['noncrew_jht_biaya' => 19000,
                'noncrew_jkk_tunjangan' => 9500.25, 'noncrew_jkm_tunjangan' => 6800,
                'jht_biaya' => 100000, 'jp_biaya' => 20000, 'noncrew_jht_hutang' => 50000]),
        ]);
        $employee = new Karyawan;
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM Non PP', ' non kary - umkm  non pp ', 'NON_KARY_UMKM_NON_PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(35300.25, $detail->summaryBurden('pt'));
        }
        $employee->tipe_bp_jamsostek = 'PPU';
        $this->assertSame(139000.0, $detail->summaryBurden('pt'));
        $employee->tipe_bp_jamsostek = 'NON KARY-UMKM Non PP';
        $detail->rincian_bpjs = null;
        $detail->jht_biaya = 19000;
        $detail->jkk_tunjangan = 9500.25;
        $detail->jkm_tunjangan = 6800;
        $this->assertSame(35300.25, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = ['pt' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('pt'));
    }

    public function test_employer_burden_sums_only_ppu_jht_ppu_jp_and_noncrew_jht_biaya(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'bpjs_kesehatan' => 214319.20,
            'rincian_bpjs' => json_encode(['jht_biaya' => 198320, 'jp_biaya' => 107200,
                'noncrew_jht_biaya' => 49000.25, 'jht_hutang' => 107200, 'jp_hutang' => 53600]),
        ]);
        $detail->setRelation('karyawan', new Karyawan);
        $this->assertSame(354520.25, $detail->summaryBurden('pt'));
        // Older rows store company JHT as an aggregate of PPU and noncrew biaya.
        $detail->rincian_bpjs = null;
        $detail->jht_biaya = 247320.25;
        $detail->jp_biaya = 107200;
        $this->assertSame(354520.25, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = ['pt' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('pt'));
    }

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
        $this->assertSame(285000.25, $detail->summaryBurden('pt'));
        $employee->tipe_jkn = 'NON KARY';
        $this->assertSame(250000.0, $detail->summaryBurden('non_kary'));
        $this->assertSame(285000.25, $detail->summaryBurden('pt'));
        $employee->is_special_case = true;
        $this->assertSame(250000.0, $detail->summaryBurden('non_kary'));
        $this->assertSame(180000.0, $detail->summaryBurden('kary_jht'));
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

<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\PranotaBpjsDetail;
use PHPUnit\Framework\TestCase;

class PranotaBpjsAllowanceTest extends TestCase
{
    public function test_non_kary_ppu_uses_total_regardless_of_jkn_group(): void
    {
        $employee = new Karyawan;
        $employee->group_jkn = 'JKN-KIS';
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['bpjs_kesehatan' => 229195.2, 'bpjs_ketenagakerjaan' => 57298.8,
            'rincian_bpjs' => json_encode(['jht_biaya' => 100000.8, 'jht_hutang' => 20000,
                'jkk_tunjangan' => 1000, 'jkm_tunjangan' => 2000, 'jp_biaya' => 10000,
                'jp_hutang' => 11000, 'noncrew_jht_biaya' => 50000, 'bpu_jkm' => 3400]),
        ]);
        $detail->setRelation('karyawan', $employee);
        foreach ([['PPU', 'NON KARY'], [' ppu ', ' non kary '], ['PPU', 'NON_KARY']] as [$group, $type]) {
            $employee->group_bp_jamsostek = $group;
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(144000.8, $detail->summaryBurden('pt'));
        }
        $employee->tipe_bp_jamsostek = 'KARYAWAN';
        $this->assertSame(160000.8, $detail->summaryBurden('pt'));
        $employee->group_bp_jamsostek = 'BPU';
        $employee->tipe_bp_jamsostek = 'NON KARY';
        $this->assertSame(160000.8, $detail->summaryBurden('pt'));
    }

    public function test_ppu_allowance_excludes_exact_non_kary_even_with_saved_manual_amount(): void
    {
        $employee = new Karyawan(['group_bp_jamsostek' => 'PPU']);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['jkk_tunjangan' => 13752, 'jkm_tunjangan' => 17190]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY', ' non kary ', 'NON_KARY'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(0.0, $detail->tunjangan_ppu);
        }
        $detail->tunjangan_manual = ['ppu' => 30942];
        $this->assertSame(0.0, $detail->tunjangan_ppu);
        $detail->tunjangan_manual = null;
        foreach (['KARYAWAN', 'NON KARY-UMKM', 'NON KARY-UMKM NON PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(30942.0, $detail->tunjangan_ppu);
        }
    }

    public function test_jkn_group_does_not_exclude_jamsostek_employer_costs_or_manual_amounts(): void
    {
        $employee = new Karyawan(['group_bp_jamsostek' => 'BPU-SUPIR']);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['rincian_bpjs' => json_encode(['noncrew_jht_biaya' => 8000])]);
        $detail->setRelation('karyawan', $employee);
        foreach (['JKN-KIS', ' jkn - kis ', 'JKN_KIS', '', 'LAIN'] as $group) {
            $employee->group_jkn = $group;
            $this->assertSame(8000.0, $detail->summaryBurden('pt'));
        }
        $detail->tunjangan_manual = ['pt' => 9000];
        $this->assertSame(9000.0, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = null;
        $detail->rincian_bpjs = null;
        $detail->jht_biaya = 8000;
        $this->assertSame(8000.0, $detail->summaryBurden('pt'));
    }

    public function test_jkn_allowance_excludes_only_exact_non_kary_jkn_type(): void
    {
        $employee = new Karyawan;
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['tipe_karyawan' => Karyawan::class, 'bpjs_kesehatan' => 286494]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY', ' non kary ', 'NON_KARY'] as $type) {
            $employee->tipe_jkn = $type;
            $this->assertSame(0.0, $detail->tunjangan_jkn);
            $this->assertSame(286494.0, $detail->kis_tunjangan);
        }
        $detail->tunjangan_manual = ['jkn' => 99999];
        $this->assertSame(0.0, $detail->tunjangan_jkn);
        $employee->tipe_jkn = 'KARYAWAN';
        $this->assertSame(99999.0, $detail->tunjangan_jkn);
        $detail->tunjangan_manual = null;
        foreach (['KARYAWAN', 'NON KARY-UMKM', 'NON KARY-UMKM NON PP'] as $type) {
            $employee->tipe_jkn = $type;
            $this->assertSame(286494.0, $detail->tunjangan_jkn);
        }
    }

    public function test_umkm_non_pp_non_employee_burden_uses_jht_debt(): void
    {
        $employee = new Karyawan(['group_bp_jamsostek' => 'CILANDAK-HL']);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['jht_hutang' => 99999,
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => 70000.25, 'noncrew_jht_biaya' => 19000,
                'noncrew_jkk_tunjangan' => 9500, 'noncrew_jkm_tunjangan' => 6800, 'jp_hutang' => 2000]),
        ]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM NON PP', ' non kary - umkm non pp ', 'NON_KARY_UMKM_NON_PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(70000.25, $detail->summaryBurden('non_kary'));
            $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
        }
        $employee->tipe_bp_jamsostek = 'NON KARY-UMKM';
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
        $employee->tipe_bp_jamsostek = 'NON KARY-UMKM NON PP';
        $detail->rincian_bpjs = null;
        $detail->jht_hutang = 75000.5;
        $this->assertSame(75000.5, $detail->summaryBurden('non_kary'));
        $detail->tunjangan_manual = ['non_kary' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
    }

    public function test_umkm_non_pp_is_excluded_from_employee_jht_even_with_manual_amount(): void
    {
        $employee = new Karyawan;
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['bpjs_ketenagakerjaan' => 50000,
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => 70000, 'jp_hutang' => 2000]),
        ]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM NON PP', ' non kary - umkm non pp ', 'NON_KARY_UMKM_NON_PP'] as $type) {
            $employee->tipe_bp_jamsostek = $type;
            $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
            $this->assertSame(50000.0, $detail->summaryBurden('kary_jkn'));
        }
        $detail->tunjangan_manual = ['kary_jht' => 70000];
        $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
        $employee->tipe_bp_jamsostek = 'NON KARY';
        $this->assertSame(0.0, $detail->summaryBurden('kary_jht'));
        $detail->tunjangan_manual = null;
        $employee->tipe_bp_jamsostek = 'NON KARY-UMKM';
        $this->assertSame(72000.0, $detail->summaryBurden('kary_jht'));
    }

    public function test_non_kary_umkm_is_distinct_from_exact_non_kary_for_employee_burdens(): void
    {
        $employee = new Karyawan;
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['bpjs_ketenagakerjaan' => 50000,
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => 36000.25, 'jp_hutang' => 2000]),
        ]);
        $detail->setRelation('karyawan', $employee);
        foreach (['NON KARY-UMKM', ' non kary - umkm ', 'NON_KARY_UMKM'] as $type) {
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
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
    }

    public function test_umkm_non_pp_has_no_bpu_allowance_even_with_saved_manual_amount(): void
    {
        $employee = new Karyawan(['group_bp_jamsostek' => 'CILANDAK-HL']);
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

    public function test_employer_burden_uses_jamsostek_costs_and_ppu_totals_regardless_of_jkn_group(): void
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
        $this->assertSame(116000.8, $detail->summaryBurden('pt'));
        $employee->group_bp_jamsostek = 'ppu';
        $employee->tipe_bp_jamsostek = 'NON_KARY';
        $this->assertSame(144000.8, $detail->summaryBurden('pt'));
        $employee->group_jkn = 'LAIN';
        $this->assertSame(144000.8, $detail->summaryBurden('pt'));
        $employee->tipe_bp_jamsostek = 'KARYAWAN';
        $this->assertSame(116000.8, $detail->summaryBurden('pt'));
        $employee->group_jkn = 'JKN-KIS';
        $employee->is_special_case = true;
        $this->assertSame(116000.8, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = ['pt' => 0];
        $this->assertSame(0.0, $detail->summaryBurden('pt'));
    }

    public function test_cilandak_umkm_non_pp_sums_costs_and_preserves_manual_amount(): void
    {
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'rincian_bpjs' => json_encode(['noncrew_jht_biaya' => 19000,
                'noncrew_jkk_tunjangan' => 9500.25, 'noncrew_jkm_tunjangan' => 6800,
                'jht_biaya' => 100000, 'jp_biaya' => 20000, 'noncrew_jht_hutang' => 50000]),
        ]);
        $employee = new Karyawan(['group_bp_jamsostek' => 'CILANDAK-HL']);
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
        $detail->tunjangan_manual = ['pt' => 44650];
        $this->assertSame(44650.0, $detail->summaryBurden('pt'));
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
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
        $this->assertSame(285000.25, $detail->summaryBurden('pt'));
        $employee->is_special_case = true;
        $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
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
    public function test_bpu_supir_umkm_non_pp_sums_jht_jkk_and_jkm_with_or_without_saved_breakdown(): void
    {
        $employee = new Karyawan(['group_jkn' => 'JKN-KIS', 'tipe_bp_jamsostek' => 'Non Kary-UMKM non PP']);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['rincian_bpjs' => json_encode([
            'noncrew_jht_biaya' => 19000, 'noncrew_jkk_tunjangan' => 22250, 'noncrew_jkm_tunjangan' => 3400,
        ])]);
        $detail->setRelation('karyawan', $employee);
        foreach (['BPU-SUPIR', ' bpu - supir ', 'BPU_SUPIR', 'BPU-SUPIR JKT PLUIT', ' bpu-supir  jkt pluit ', "BPU-SUPIR\nJKT PLUIT", 'CILANDAK-HL', ' cilandak - hl '] as $group) {
            $employee->group_bp_jamsostek = $group;
            $this->assertSame(44650.0, $detail->summaryBurden('pt'));
        }
        $detail->rincian_bpjs = null;
        $detail->jht_biaya = 19000;
        $detail->jkk_tunjangan = 22250;
        $detail->jkm_tunjangan = 3400;
        $this->assertSame(44650.0, $detail->summaryBurden('pt'));
        $detail->tunjangan_manual = ['pt' => 9000];
        $this->assertSame(9000.0, $detail->summaryBurden('pt'));
        $employee->group_bp_jamsostek = ' cilandak - hl ';
        $this->assertSame(9000.0, $detail->summaryBurden('pt'));
    }

    public function test_erwin_jkn_kis_non_kary_moves_kis_biaya_to_employer_burden(): void
    {
        foreach ([Karyawan::class, \App\Models\KaryawanTidakTetap::class] as $model) {
            $employee = new $model;
            $employee->setRawAttributes(['group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'NON KARY',
                'is_special_case' => true, 'tanggal_berhenti' => '2026-10-01']);
            $detail = new PranotaBpjsDetail;
            $detail->setRawAttributes(['tipe_karyawan' => $model, 'bpjs_kesehatan' => 229195.2,
                'bpjs_ketenagakerjaan' => 57298.8]);
            $detail->setRelation('karyawan', $employee);
            $this->assertSame(229195.2, $detail->summaryBurden('pt'));
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
            $this->assertSame(0.0, $detail->tunjangan_jkn);
            $detail->jht_biaya = 8000;
            $this->assertSame(237195.2, $detail->summaryBurden('pt'));
            $detail->jht_biaya = 0;
            $employee->is_special_case = false;
            $this->assertSame(0.0, $detail->summaryBurden('pt'));
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
        }
    }

    public function test_non_employee_burden_excludes_other_memberships_even_with_manual_amounts(): void
    {
        $employee = new Karyawan(['tipe_bp_jamsostek' => 'NON KARY-UMKM non PP', 'tipe_jkn' => 'NON KARY']);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes(['bpjs_kesehatan' => 200000, 'bpjs_ketenagakerjaan' => 50000,
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => 70000.25, 'jp_hutang' => 2000,
                'noncrew_jht_biaya' => 19000, 'noncrew_jkk_tunjangan' => 22250, 'noncrew_jkm_tunjangan' => 3400])]);
        $detail->setRelation('karyawan', $employee);
        foreach (['CILANDAK-HL', ' cilandak - hl ', 'BPU-SUPIR JKT PLUIT', ' bpu_supir jkt pluit '] as $group) {
            $employee->group_bp_jamsostek = $group;
            $this->assertSame(70000.25, $detail->summaryBurden('non_kary'));
            $employee->tipe_bp_jamsostek = 'HARIAN';
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
            $employee->tipe_bp_jamsostek = 'NON KARY-UMKM non PP';
        }
        $detail->tunjangan_manual = ['non_kary' => 99999];
        foreach (['', 'PPU', 'BPU-SUPIR', 'BPU-CREW', 'LAIN'] as $group) {
            $employee->group_bp_jamsostek = $group;
            $this->assertSame(0.0, $detail->summaryBurden('non_kary'));
        }
    }

}

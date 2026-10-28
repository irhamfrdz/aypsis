<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\PranotaBpjsDetail;
use PHPUnit\Framework\TestCase;

class PranotaBpjsAllowanceTest extends TestCase
{
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

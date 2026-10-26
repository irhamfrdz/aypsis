<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use App\Models\PranotaBpjsDetail;
use PHPUnit\Framework\TestCase;

class PranotaBpjsJknTest extends TestCase
{
    public function test_existing_saved_amount_is_classified_by_employee_type_without_changing_total(): void
    {
        foreach ([Karyawan::class, KaryawanTidakTetap::class] as $type) {
            $detail = new PranotaBpjsDetail;
            $detail->setRawAttributes([
                'tipe_karyawan' => $type, 'bpjs_kesehatan' => '214319.00',
                'bpjs_ketenagakerjaan' => '53580.00', 'total' => '267899.00',
            ], true);
            $detail->setRelation('karyawan', new $type);
            $this->assertSame($type === KaryawanTidakTetap::class ? 214319.0 : 0.0, $detail->kis_biaya);
            $this->assertSame($type === Karyawan::class ? 214319.0 : 0.0, $detail->kis_tunjangan);
            $this->assertSame(267899.0, $detail->kis_biaya + $detail->kis_tunjangan + (float) $detail->bpjs_ketenagakerjaan);
            $this->assertFalse($detail->isDirty());
        }
    }

    public function test_special_case_temporary_employee_uses_tunjangan_and_preserves_debt_and_total(): void
    {
        $employee = new KaryawanTidakTetap(['is_special_case' => true]);
        $detail = new PranotaBpjsDetail;
        $detail->setRawAttributes([
            'tipe_karyawan' => KaryawanTidakTetap::class, 'bpjs_kesehatan' => '214319.00',
            'bpjs_ketenagakerjaan' => '53580.00', 'total' => '267899.00',
        ], true);
        $detail->setRelation('karyawan', $employee);
        $this->assertSame(0.0, $detail->kis_biaya);
        $this->assertSame(214319.0, $detail->kis_tunjangan);
        $this->assertSame(267899.0, $detail->kis_tunjangan + (float) $detail->bpjs_ketenagakerjaan);
        $employee->is_special_case = false;
        $this->assertSame(214319.0, $detail->kis_biaya);
        $this->assertSame(0.0, $detail->kis_tunjangan);
    }
}

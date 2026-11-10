<?php

namespace Tests\Unit;

use App\Models\PranotaBpjsDetail;
use App\Services\BpjsJournalAmounts;
use PHPUnit\Framework\TestCase;

class BpjsPaymentDetailHydrationTest extends TestCase
{
    public function test_database_json_preserves_payment_breakdown_amounts(): void
    {
        $detail = (new PranotaBpjsDetail)->newFromBuilder([
            'snapshot_bpjs' => json_encode(['karyawan' => ['tipe_bp_jamsostek' => 'KANTOR', 'group_bp_jamsostek' => 'PPU']]),
            'rincian_bpjs' => json_encode(['jkk_tunjangan' => 12000, 'jht_biaya' => 24000, 'jht_hutang' => 6000]),
        ]);

        $this->assertIsArray($detail->rincian_bpjs);
        $this->assertEquals([12000, 24000, 6000], (new BpjsJournalAmounts)->forDetail($detail, 'jamsostek'));
    }
}

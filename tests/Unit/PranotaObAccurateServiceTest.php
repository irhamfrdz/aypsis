<?php

namespace Tests\Unit;

use App\Services\PranotaObAccurateService;
use PHPUnit\Framework\TestCase;

class PranotaObAccurateServiceTest extends TestCase
{
    public function test_numbers_follow_pranota_references_and_the_correct_driver(): void
    {
        $service = new PranotaObAccurateService;
        $item = (object) ['nik' => '0007', 'supir' => 'ALI', 'no_voyage' => 'V01'];
        $drivers = collect([
            (object) ['id' => 7, 'nik' => '0007', 'nama_panggilan' => 'ALI', 'nama_lengkap' => 'ALI A'],
            (object) ['id' => 8, 'nik' => '0008', 'nama_panggilan' => 'ALI', 'nama_lengkap' => 'ALI B'],
        ]);
        $payments = collect([
            (object) ['pranota_ob_ids' => '[10,11]', 'pembayaran_ob_ids' => '[1,2,3]', 'nomor_accurate' => 'PEL-01'],
            (object) ['pranota_ob_ids' => json_encode('[10]'), 'pembayaran_ob_id' => 4, 'nomor_accurate' => 'PEL-02'],
            (object) ['pranota_ob_ids' => '[99]', 'pembayaran_ob_ids' => '[5]', 'nomor_accurate' => 'OTHER-PEL'],
        ]);
        $dps = collect([
            (object) ['id' => 1, 'supir_ids' => '[7]', 'nomor_voyage' => 'V01', 'nomor_accurate' => 'DP-01'],
            (object) ['id' => 2, 'supir_ids' => '[8]', 'nomor_voyage' => 'V01', 'nomor_accurate' => 'OTHER-DRIVER'],
            (object) ['id' => 3, 'supir_ids' => '[7]', 'nomor_voyage' => 'V01', 'nomor_accurate' => 'DP-01'],
            (object) ['id' => 4, 'supir_ids' => '[7]', 'nomor_voyage' => 'V02', 'nomor_accurate' => 'DP-02'],
            (object) ['id' => 5, 'supir_ids' => '[7]', 'nomor_voyage' => 'V01', 'nomor_accurate' => 'UNRELATED-DP'],
        ]);
        $this->assertSame([['DP-01', 'DP-02'], ['PEL-01', 'PEL-02']], $service->numbersForRow($item, [10], $payments, $dps, $drivers));
    }

    public function test_unpaid_pranota_matches_dp_by_voyage_and_driver_name(): void
    {
        $service = new PranotaObAccurateService;
        $item = (object) ['nik' => null, 'supir' => ' Ali A ', 'no_voyage' => 'V01'];
        $drivers = collect([(object) ['id' => 7, 'nik' => '0007', 'nama_panggilan' => 'ALI', 'nama_lengkap' => 'ALI A']]);
        $dps = collect([
            (object) ['id' => 1, 'supir_ids' => '[7]', 'nomor_voyage' => 'V01', 'nomor_accurate' => 'DP-01'],
            (object) ['id' => 2, 'supir_ids' => '[7]', 'nomor_voyage' => 'V02', 'nomor_accurate' => 'OTHER-VOYAGE'],
            (object) ['id' => 3, 'supir_ids' => '[7]', 'nomor_voyage' => 'V01', 'nomor_accurate' => ' '],
        ]);
        $this->assertSame([['DP-01'], []], $service->numbersForRow($item, [10], collect(), $dps, $drivers));
        $this->assertSame([[], []], $service->numbersForRow($item, [10], collect(), collect(), $drivers));
    }
}

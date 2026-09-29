<?php

namespace Tests\Feature;

use Tests\TestCase;

class BiayaKapalPrintTruckingTest extends TestCase
{
    public function test_print_groups_vendor_costs_under_one_ship_total(): void
    {
        $biayaKapal = (object) [
            'truckingDetails' => collect([
                (object) [
                    'id' => 1, 'kapal' => 'Kapal A', 'voyage' => 'V1', 'nama_vendor' => 'Vendor A',
                    'total_biaya_20ft' => 100, 'total_biaya_40ft' => 0, 'subtotal' => 100,
                    'adjustment' => 0, 'notes_adjustment' => null, 'pph' => 2, 'total_biaya' => 98,
                ],
                (object) [
                    'id' => 2, 'kapal' => 'Kapal A', 'voyage' => 'V1', 'nama_vendor' => 'Vendor B',
                    'total_biaya_20ft' => 0, 'total_biaya_40ft' => 200, 'subtotal' => 200,
                    'adjustment' => 0, 'notes_adjustment' => null, 'pph' => 4, 'total_biaya' => 196,
                ],
                (object) [
                    'id' => 3, 'kapal' => 'Kapal A', 'voyage' => 'V1', 'nama_vendor' => 'CARGO',
                    'no_bl' => [], 'total_biaya_20ft' => 0, 'total_biaya_40ft' => 0, 'subtotal' => 50,
                    'adjustment' => 0, 'notes_adjustment' => null, 'pph' => 1, 'total_biaya' => 49,
                ],
            ]),
        ];

        $html = view('biaya-kapal.print-trucking-detail', compact('biayaKapal'))->render();

        $this->assertStringContainsString('Vendor A', $html);
        $this->assertStringContainsString('Vendor B', $html);
        $this->assertStringContainsString('Cargo: Rp 50', $html);
        $this->assertSame(1, substr_count($html, 'Rincian biaya Kapal A / V1'));
        $this->assertSame(1, substr_count($html, 'Total Biaya: Rp 343'));
    }
}

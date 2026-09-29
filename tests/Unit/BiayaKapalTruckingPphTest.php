<?php

namespace Tests\Unit;

use App\Http\Controllers\BiayaKapalController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BiayaKapalTruckingPphTest extends TestCase
{
    public function test_pph_is_rounded_once_per_ship_group(): void
    {
        $sections = [
            1 => ['group_index' => 1, 'kapal' => 'Kapal A', 'nama_vendor' => 'Vendor A', 'subtotal' => 26, 'pph_percent' => 2],
            2 => ['group_index' => 1, 'kapal' => 'Kapal A', 'nama_vendor' => 'Vendor B', 'subtotal' => 26, 'pph_percent' => 2],
            3 => ['group_index' => 3, 'kapal' => 'Kapal B', 'nama_vendor' => 'Vendor C', 'subtotal' => 100, 'pph_percent' => 2],
        ];

        $method = new ReflectionMethod(BiayaKapalController::class, 'calculateTruckingPphAllocations');
        $allocations = $method->invoke(new BiayaKapalController, $sections);

        $this->assertSame(1.0, (float) ($allocations[1]['pph'] + $allocations[2]['pph']));
        $this->assertSame(2.0, (float) $allocations[3]['pph']);
        $this->assertSame(2.0, $allocations[2]['percent']);
    }
}

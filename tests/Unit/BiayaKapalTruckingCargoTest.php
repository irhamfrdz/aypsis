<?php

namespace Tests\Unit;

use App\Http\Controllers\BiayaKapalController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BiayaKapalTruckingCargoTest extends TestCase
{
    public function test_manual_cargo_cost_does_not_create_container_size_totals(): void
    {
        $method = new ReflectionMethod(BiayaKapalController::class, 'calculateTruckingContainerTotals');

        $totals = $method->invoke(new BiayaKapalController, [
            'nama_vendor' => 'CARGO',
            'no_bl' => [1, 2],
            'subtotal' => 1500000,
        ]);

        $this->assertSame(['20ft' => 0, '40ft' => 0], $totals);
    }
}

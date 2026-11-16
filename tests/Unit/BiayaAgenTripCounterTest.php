<?php

namespace Tests\Unit;

use App\Services\BiayaAgenTripCounter;
use PHPUnit\Framework\TestCase;

class BiayaAgenTripCounterTest extends TestCase
{
    public function test_opposite_voyage_directions_count_as_one_trip(): void
    {
        $rows = collect([
            (object) ['voyage' => 'SA01JB26'],
            (object) ['voyage' => 'SA01BJ26'],
            (object) ['voyage' => 'SA01JB26'],
            (object) ['voyage' => 'AP03JB'],
            (object) ['voyage' => 'AP03BJ'],
            (object) ['voyage' => 'AS05JP'],
            (object) ['voyage' => 'AS05PJ'],
            (object) ['voyage' => null],
        ]);

        $this->assertSame(3, (new BiayaAgenTripCounter)->count($rows));
    }

    public function test_different_trip_numbers_and_years_remain_separate(): void
    {
        $rows = collect([
            (object) ['voyage' => 'SA01JB25'],
            (object) ['voyage' => 'SA01BJ26'],
            (object) ['voyage' => 'SA02JB26'],
        ]);

        $this->assertSame(3, (new BiayaAgenTripCounter)->count($rows));
    }
}

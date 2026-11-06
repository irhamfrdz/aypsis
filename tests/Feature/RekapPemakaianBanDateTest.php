<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapPemakaianBanController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RekapPemakaianBanDateTest extends TestCase
{
    public function test_initial_view_waits_for_date_and_unit_selection(): void
    {
        $data = (new RekapPemakaianBanController)->index(Request::create('/', 'GET'))->getData();
        $this->assertNull($data['items']);
        $this->assertTrue($data['units']->isEmpty());
    }

    public function test_invalid_range_or_unit_without_dates_is_rejected(): void
    {
        foreach ([['unit' => 1], ['start_date' => '2026-11-10', 'end_date' => '2026-11-01']] as $filters) {
            try {
                (new RekapPemakaianBanController)->index(Request::create('/', 'GET', $filters));
                $this->fail('Expected validation failure.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }
}

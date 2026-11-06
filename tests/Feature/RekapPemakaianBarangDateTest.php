<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapPemakaianBarangController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RekapPemakaianBarangDateTest extends TestCase
{
    public function test_initial_page_has_no_results_until_dates_are_selected(): void
    {
        $view = (new RekapPemakaianBarangController)->index(Request::create('/', 'GET'));
        $this->assertSame('rekap-pemakaian-barang.index', $view->name());
        $this->assertNull($view->getData()['startDate']);
        $this->assertTrue($view->getData()['results']->isEmpty());
    }

    public function test_invalid_and_incomplete_date_ranges_are_rejected(): void
    {
        foreach ([['start_date' => '2026-11-01'], ['start_date' => '2026-11-10', 'end_date' => '2026-11-01']] as $query) {
            try {
                (new RekapPemakaianBarangController)->index(Request::create('/', 'GET', $query));
                $this->fail('Expected date validation failure.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }
}

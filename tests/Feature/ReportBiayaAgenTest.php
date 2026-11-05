<?php

namespace Tests\Feature;

use App\Http\Controllers\Report\BiayaAgenController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportBiayaAgenTest extends TestCase
{
    public function test_report_filters_agency_costs_by_ship_date_or_both(): void
    {
        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_invoice');
            $table->softDeletes();
        });
        Schema::create('biaya_kapal_air', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->string('kapal');
            $table->date('tanggal_invoice_vendor')->nullable();
            $table->decimal('sub_total', 15, 2);
            $table->decimal('pph', 15, 2);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
        });

        DB::table('biaya_kapals')->insert([
            ['id' => 1, 'tanggal' => '2026-10-01', 'nomor_invoice' => 'AGEN-001', 'deleted_at' => null],
            ['id' => 2, 'tanggal' => '2026-10-05', 'nomor_invoice' => 'AGEN-002', 'deleted_at' => null],
            ['id' => 3, 'tanggal' => '2026-10-03', 'nomor_invoice' => 'AGEN-003', 'deleted_at' => '2026-10-06'],
            ['id' => 4, 'tanggal' => '2026-10-02', 'nomor_invoice' => 'AGEN-004', 'deleted_at' => null],
        ]);
        DB::table('biaya_kapal_air')->insert([
            ['id' => 1, 'biaya_kapal_id' => 1, 'kapal' => 'Kapal A', 'tanggal_invoice_vendor' => '2026-10-02', 'sub_total' => 100, 'pph' => 2, 'adjustment' => 0, 'grand_total' => 98],
            ['id' => 2, 'biaya_kapal_id' => 2, 'kapal' => 'Kapal A', 'tanggal_invoice_vendor' => null, 'sub_total' => 200, 'pph' => 4, 'adjustment' => 10, 'grand_total' => 206],
            ['id' => 3, 'biaya_kapal_id' => 3, 'kapal' => 'Kapal B', 'tanggal_invoice_vendor' => null, 'sub_total' => 300, 'pph' => 6, 'adjustment' => 0, 'grand_total' => 294],
            ['id' => 4, 'biaya_kapal_id' => 4, 'kapal' => 'Kapal B', 'tanggal_invoice_vendor' => null, 'sub_total' => 300, 'pph' => 6, 'adjustment' => 0, 'grand_total' => 294],
        ]);

        $controller = app(BiayaAgenController::class);
        $this->assertSame(['Kapal A', 'Kapal B'], $controller->index()->getData()['kapals']->all());

        $byShip = $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
            'mode' => 'kapal', 'kapal' => ' kapal a ',
        ]))->getData();
        $this->assertSame([1, 2], $byShip['rows']->pluck('id')->all());
        $this->assertEquals(10, $byShip['summary']['adjustment']);
        $this->assertEquals(304, $byShip['summary']['grand_total']);

        $byDate = $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
            'mode' => 'tanggal', 'start_date' => '2026-10-02', 'end_date' => '2026-10-05',
        ]))->getData();
        $this->assertSame([1, 4, 2], $byDate['rows']->pluck('id')->all());
        $this->assertEquals(600, $byDate['summary']['subtotal']);

        $combined = $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
            'mode' => 'gabungan', 'kapal' => ' kapal a ',
            'start_date' => '2026-10-02', 'end_date' => '2026-10-02',
        ]))->getData();
        $this->assertSame([1], $combined['rows']->pluck('id')->all());
        $this->assertEquals(98, $combined['summary']['grand_total']);

        $combinedFallback = $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
            'mode' => 'gabungan', 'kapal' => 'Kapal A',
            'start_date' => '2026-10-05', 'end_date' => '2026-10-05',
        ]))->getData();
        $this->assertSame([2], $combinedFallback['rows']->pluck('id')->all());

        try {
            $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
                'mode' => 'gabungan', 'kapal' => 'Kapal A',
                'start_date' => '2026-10-02',
            ]));
            $this->fail('Filter gabungan harus memiliki tanggal akhir.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('end_date', $exception->errors());
        }

        try {
            $controller->show(Request::create('/report-biaya-agen/show', 'GET', [
                'mode' => 'tanggal', 'start_date' => '2026-10-06', 'end_date' => '2026-10-01',
            ]));
            $this->fail('Rentang tanggal terbalik harus ditolak.');
        } catch (ValidationException $exception) {
            $this->assertSame(route('report.biaya-agen.index'), $exception->redirectTo);
        }
    }
}

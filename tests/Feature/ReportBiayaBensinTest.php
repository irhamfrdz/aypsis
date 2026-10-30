<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportBiayaBensinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportBiayaBensinTest extends TestCase
{
    public function test_report_filters_transactions_and_totals_from_biaya_bensin(): void
    {
        Schema::create('biaya_bensin', function ($table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_kartu')->nullable();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('status');
            $table->decimal('liter', 10, 2);
            $table->decimal('biaya', 15, 2);
        });
        Schema::create('mobils', function ($table) {
            $table->id();
            $table->string('nomor_polisi');
        });
        Schema::create('alat_berats', function ($table) {
            $table->id();
            $table->string('nama');
        });
        Schema::create('karyawans', function ($table) {
            $table->id();
            $table->string('nama_panggilan');
            $table->string('nama_lengkap');
        });

        DB::table('biaya_bensin')->insert([
            ['tanggal' => '2026-10-03', 'nomor_kartu' => 'CARD-01', 'status' => 'approved', 'liter' => 10, 'biaya' => 100000],
            ['tanggal' => '2026-10-04', 'nomor_kartu' => 'CARD-02', 'status' => 'approved', 'liter' => 20, 'biaya' => 200000],
            ['tanggal' => '2026-10-05', 'nomor_kartu' => 'CARD-03', 'status' => 'pending', 'liter' => 30, 'biaya' => 300000],
            ['tanggal' => '2026-09-30', 'nomor_kartu' => 'CARD-04', 'status' => 'approved', 'liter' => 40, 'biaya' => 400000],
        ]);

        $request = Request::create('/report/biaya-bensin', 'GET', [
            'tanggal_dari' => '2026-10-01',
            'tanggal_sampai' => '2026-10-31',
            'status' => 'approved',
        ]);
        $view = (new ReportBiayaBensinController)->index($request);

        $this->assertSame(2, $view->getData()['items']->total());
        $this->assertEquals(2, $view->getData()['summary']->jumlah_transaksi);
        $this->assertEquals(30, $view->getData()['summary']->total_liter);
        $this->assertEquals(300000, $view->getData()['summary']->total_biaya);

        $searchRequest = Request::create('/report/biaya-bensin', 'GET', [
            'tanggal_dari' => '2026-10-01',
            'tanggal_sampai' => '2026-10-31',
            'search' => 'CARD-03',
        ]);
        $searchView = (new ReportBiayaBensinController)->index($searchRequest);

        $this->assertSame(1, $searchView->getData()['items']->total());
        $this->assertEquals(300000, $searchView->getData()['summary']->total_biaya);
    }
}

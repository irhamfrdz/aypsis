<?php

namespace Tests\Feature;

use App\Exports\ReportUangJalanExport;
use App\Http\Controllers\ReportUangJalanController;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportUangJalanRefundTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            foreach (['nomor', 'nomor_accurate', 'debit_kredit', 'keterangan', 'penerima', 'nomor_polisi'] as $column) {
                $table->string($column)->nullable();
            }
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
        });
        DB::table('pembayaran_aktivitas_lains')->insert([
            ['id' => 1, 'nomor' => 'PAL/2026/09/0081', 'nomor_accurate' => 'BTJ26 0900044', 'tanggal' => '2026-09-03', 'debit_kredit' => 'debit', 'jumlah' => 600000, 'keterangan' => 'Pengembalian uang jalan'],
            ['id' => 2, 'nomor' => 'PAL/2026/09/0082', 'nomor_accurate' => 'BTJ26 0900045', 'tanggal' => '2026-09-04', 'debit_kredit' => 'kredit', 'jumlah' => 200000, 'keterangan' => 'Pembayaran uang jalan'],
            ['id' => 3, 'nomor' => 'PAL/2026/11/0001', 'nomor_accurate' => 'BTJ26 1100001', 'tanggal' => '2026-11-01', 'debit_kredit' => 'debit', 'jumlah' => 100000, 'keterangan' => 'Pengembalian uang jalan'],
        ]);
    }

    private function reportRows(?string $search = null): array
    {
        $rows = collect();
        $adjustments = collect();
        $method = new \ReflectionMethod(ReportUangJalanController::class, 'appendStandalonePembayaranAktivitasLain');
        $arguments = [&$rows, &$adjustments, Carbon::parse('2026-01-01'), Carbon::parse('2026-10-31'), $search];
        $method->invokeArgs(new ReportUangJalanController, $arguments);
        foreach ($rows as $row) {
            $row->suratJalan->setRelation('supirKaryawan', null);
        }

        return [$rows, $adjustments];
    }

    public function test_refund_is_negative_in_report_and_excel_and_reduces_totals(): void
    {
        [$rows, $adjustments] = $this->reportRows();
        $this->assertCount(2, $rows);
        $refund = $rows->firstWhere('id', 'pal_1');
        $this->assertEquals(-600000, $refund->jumlah_uang_jalan);
        $this->assertEquals(-600000, $refund->jumlah_total);
        $this->assertEquals(200000, $rows->firstWhere('id', 'pal_2')->jumlah_total);
        $this->assertEquals(-400000, $rows->sum('jumlah_total'));
        $this->assertEquals(600000, DB::table('pembayaran_aktivitas_lains')->where('id', 1)->value('jumlah'));

        $export = new ReportUangJalanExport($rows, Carbon::parse('2026-01-01'), Carbon::parse('2026-10-31'), $adjustments);
        $exportRefund = collect($export->array())->first(fn ($row) => ($row[3] ?? null) === 'BTJ26 0900044');
        $this->assertEquals(-600000, $exportRefund[11]);
        $this->assertEquals(-600000, $exportRefund[18]);
    }

    public function test_accurate_search_and_existing_negative_refund_keep_the_correct_sign(): void
    {
        DB::table('pembayaran_aktivitas_lains')->where('id', 1)->update(['jumlah' => -600000]);
        [$rows] = $this->reportRows('BTJ26 0900044');
        $this->assertCount(1, $rows);
        $this->assertEquals(-600000, $rows->first()->jumlah_total);
    }
}

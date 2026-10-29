<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportBiayaPerijinanController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportBiayaPerijinanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal');
            $table->string('jenis_aktivitas');
            $table->string('sub_jenis_kendaraan')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->json('kendaraan_details')->nullable();
            $table->string('penerima')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('jumlah');
            $table->integer('pph_nominal')->nullable();
            $table->integer('grand_total')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('pembayaran_aktivitas_lains')->insert([
            $this->payment(1, 'KIR', '2026-10-10', 300000, 30000, null, '[{"nomor_polisi":"BP 1234 AA","nominal":100000},{"nomor_polisi":"BP 5678 BB","nominal":200000}]'),
            $this->payment(2, 'KIR', '2026-10-20', 200000, 0, null, '[{"nomor_polisi":"BP 9999 CC","nominal":200000}]'),
            $this->payment(3, 'Pajak', '2026-10-10', 900000, 0, null, null),
            $this->payment(4, 'KIR', '2026-10-10', 400000, 0, '2026-10-11 00:00:00', null),
        ]);
    }

    public function test_report_only_counts_active_kir_payments_once_per_transaction(): void
    {
        $view = (new ReportBiayaPerijinanController)->view(Request::create('/report/biaya-perijinan/view', 'GET', [
            'tanggal_dari' => '2026-10-01',
            'tanggal_sampai' => '2026-10-31',
        ]));
        $data = $view->getData();

        $this->assertEqualsCanonicalizing([1, 2], $data['pembayarans']->pluck('id')->all());
        $this->assertSame(2, (int) $data['summary']->total_transaksi);
        $this->assertSame(500000, (int) $data['summary']->total_biaya);
        $this->assertSame(30000, (int) $data['summary']->total_pph);
        $this->assertSame(470000, (int) $data['summary']->total_dibayar);
    }

    public function test_report_filters_by_date_and_vehicle_in_details(): void
    {
        $request = Request::create('/report/biaya-perijinan/view', 'GET', [
            'tanggal_dari' => '2026-10-01',
            'tanggal_sampai' => '2026-10-15',
            'search' => 'BP 5678 BB',
        ]);

        $data = (new ReportBiayaPerijinanController)->view($request)->getData();

        $this->assertSame([1], $data['pembayarans']->pluck('id')->all());
        $this->assertSame(300000, (int) $data['summary']->total_biaya);
    }

    public function test_first_page_is_date_selection(): void
    {
        $this->assertSame('report.biaya-perijinan.select-date', (new ReportBiayaPerijinanController)->index()->name());
    }

    public function test_report_requires_a_valid_date_range(): void
    {
        $this->expectException(ValidationException::class);

        (new ReportBiayaPerijinanController)->view(Request::create('/report/biaya-perijinan/view', 'GET', [
            'tanggal_dari' => '2026-10-20',
            'tanggal_sampai' => '2026-10-10',
        ]));
    }

    private function payment(int $id, string $subJenis, string $tanggal, int $jumlah, int $pph, ?string $deletedAt, ?string $details): array
    {
        return [
            'id' => $id,
            'nomor' => "PAL/{$id}",
            'tanggal' => $tanggal,
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'sub_jenis_kendaraan' => $subJenis,
            'kendaraan_details' => $details,
            'jumlah' => $jumlah,
            'pph_nominal' => $pph,
            'grand_total' => $jumlah - $pph,
            'deleted_at' => $deletedAt,
        ];
    }
}

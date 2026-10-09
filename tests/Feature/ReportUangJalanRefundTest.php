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
            foreach (['nomor', 'nomor_accurate', 'debit_kredit', 'keterangan', 'penerima', 'nomor_polisi', 'invoice_ids', 'jenis_aktivitas', 'no_surat_jalan'] as $column) {
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
        $refund = $rows->firstWhere('id', -3000000001);
        $this->assertEquals(-600000, $refund->jumlah_uang_jalan);
        $this->assertEquals(-600000, $refund->jumlah_total);
        $this->assertEquals(200000, $rows->firstWhere('id', -3000000002)->jumlah_total);
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

    public function test_invoice_with_colliding_surat_jalan_ids_is_reported_once_without_standalone_payment(): void
    {
        Schema::create('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('surat_jalan_source')->nullable();
            $table->string('jenis_aktivitas');
            $table->string('jenis_penyesuaian');
            $table->string('penerima');
            $table->string('nomor_invoice');
            $table->date('tanggal_invoice');
            $table->decimal('total', 15, 2);
            $table->decimal('grand_total', 15, 2)->nullable();
        });
        Schema::create('pembayaran_invoice_aktivitas_lain', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_accurate')->nullable();
            $table->softDeletes();
        });
        Schema::create('invoice_aktivitas_lain_pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_aktivitas_lain_id');
            $table->unsignedBigInteger('pembayaran_invoice_aktivitas_lain_id');
            $table->decimal('jumlah_dibayar', 15, 2);
            $table->timestamps();
        });
        Schema::create('pembayaran_invoice_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('pembayaran_id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('jumlah_dibayar', 15, 2);
        });
        Schema::create('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
        });
        DB::table('invoice_aktivitas_lain')->insert([
            'id' => 1033, 'surat_jalan_id' => 959, 'penerima' => 'ADITIA GUNA PRATAMA',
            'nomor_invoice' => 'IAL-09-26-000001', 'tanggal_invoice' => '2026-09-01',
            'jenis_aktivitas' => 'Pembayaran Adjustment Uang Jalan', 'jenis_penyesuaian' => 'penambahan', 'total' => 175000,
        ]);
        DB::table('pembayaran_aktivitas_lains')->insert([
            'id' => 1184, 'nomor' => 'PAL/2026/09/0006', 'nomor_accurate' => 'BTJ26 0900009',
            'tanggal' => '2026-09-01', 'jumlah' => 175000, 'debit_kredit' => 'kredit',
            'keterangan' => 'Tambahan uang jalan', 'invoice_ids' => '1033',
        ]);
        DB::table('pembayaran_invoice_pivot')->insert(['pembayaran_id' => 1184, 'invoice_id' => 1033, 'jumlah_dibayar' => 175000]);
        $rows = collect();
        foreach ([[964, 'regular', 'JUH', 'JUH UJI'], [6028, 'bongkar', 'ADITIA', 'ADITIA GUNA PRATAMA']] as [$id, $source, $nickname, $fullName]) {
            $row = new \App\Models\UangJalan;
            $row->id = $id;
            $row->tanggal_uang_jalan = '2026-08-27';
            $row->{$source === 'regular' ? 'surat_jalan_id' : 'surat_jalan_bongkaran_id'} = 959;
            $sj = $source === 'regular' ? new \App\Models\SuratJalan : new \App\Models\SuratJalanBongkaran;
            $sj->supir = $nickname;
            $sj->{$source === 'regular' ? 'no_surat_jalan' : 'nomor_surat_jalan'} = $source === 'regular' ? 'JB0031148' : 'SS0003524';
            $driver = new \App\Models\Karyawan;
            $driver->nama_lengkap = $fullName;
            $driver->nama_panggilan = $nickname;
            $sj->setRelation('supirKaryawan', $driver);
            $row->setRelation('suratJalan', $source === 'regular' ? $sj : null);
            $row->setRelation('suratJalanBongkaran', $source === 'bongkar' ? $sj : null);
            $rows->push($row);
        }
        $controller = new ReportUangJalanController;
        $fetch = new \ReflectionMethod($controller, 'fetchAdjustments');
        $adjustments = $fetch->invoke($controller, $rows);
        $this->assertFalse($adjustments->has(964));
        $this->assertCount(1, $adjustments[6028]);
        $this->assertEquals('BTJ26 0900009', $adjustments[6028]->first()->_resolved_nomor_bukti);
        $append = new \ReflectionMethod($controller, 'appendStandalonePembayaranAktivitasLain');
        $arguments = [&$rows, &$adjustments, Carbon::parse('2026-01-01'), Carbon::parse('2026-10-31'), 'BTJ26 0900009'];
        $append->invokeArgs($controller, $arguments);
        $this->assertCount(2, $rows);
        $this->assertEquals(175000, $adjustments->flatten()->sum('total'));
    }
}

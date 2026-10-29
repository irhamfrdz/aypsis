<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaStockController;
use App\Http\Controllers\Report\KasTruckController;
use App\Models\PembayaranPranotaStock;
use App\Services\CoaTransactionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PembayaranPranotaStockAkunItemsTest extends TestCase
{
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun')->nullable();
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->nullable();
            $table->string('metode_pembatalan')->nullable();
        });
        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->string('nomor_accurate')->nullable();
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->unsignedBigInteger('akun_bank_id')->nullable();
            $table->json('akun_details')->nullable();
            $table->string('debit_kredit')->nullable();
            $table->decimal('pph_nominal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->softDeletes();
        });
        Schema::create('nomor_terakhir', function (Blueprint $table) {
            $table->id();
            $table->string('modul')->unique();
            $table->integer('nomor_terakhir');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_amprahans', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('pranota_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->date('tanggal_pranota');
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->json('items');
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->string('jenis_transaksi');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_pembayaran');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_stock_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_stock_id');
            $table->unsignedBigInteger('pranota_stock_id');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_20_160000_create_pembayaran_pranota_stock_akun_items_table.php'))->up();

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '1001', 'nama_akun' => 'BANK TEST', 'saldo' => 0],
            ['id' => 2, 'nomor_akun' => '5001', 'nama_akun' => 'Biaya A', 'saldo' => 0],
            ['id' => 3, 'nomor_akun' => '5002', 'nama_akun' => 'Biaya B', 'saldo' => 0],
        ]);
        DB::table('pranota_stocks')->insert([
            'id' => 1,
            'nomor_pranota' => 'PS-001',
            'tanggal_pranota' => '2026-10-20',
            'items' => json_encode([['harga' => 100, 'jumlah' => 1, 'adjustment' => 0]]),
            'status' => 'approved',
        ]);
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    private function controller(): PembayaranPranotaStockController
    {
        return new PembayaranPranotaStockController(app(CoaTransactionService::class));
    }

    private function paymentInput(): array
    {
        return [
            'pranota_stock_ids' => [1],
            'nomor_pembayaran' => 'SIS-TEST',
            'tanggal_pembayaran' => '2026-10-20',
            'jenis_transaksi' => 'Kredit',
            'bank' => 'BANK TEST',
            'akun_items' => [
                ['akun_coa_id' => 2, 'nominal' => 60, 'keterangan' => 'Bagian A'],
                ['akun_coa_id' => 3, 'nominal' => 40, 'keterangan' => 'Bagian B'],
            ],
            'total_pembayaran' => 100,
            'total_tagihan_penyesuaian' => 0,
            'total_tagihan_setelah_penyesuaian' => 100,
        ];
    }

    public function test_store_and_update_split_payment_across_accounts_without_unbalanced_journal(): void
    {
        $this->controller()->store(Request::create('/pembayaran-pranota-stock', 'POST', $this->paymentInput()));

        $payment = PembayaranPranotaStock::firstOrFail();
        $this->assertCount(2, $payment->akunItems);
        $this->assertEquals(2, $payment->akun_coa_id);
        $this->assertEquals(100, DB::table('coa_transactions')->sum('debit'));
        $this->assertEquals(100, DB::table('coa_transactions')->sum('kredit'));
        $this->assertEquals(60, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(40, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(-100, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 2, 'keterangan' => 'Pembayaran Pranota Stock - '.$payment->nomor_pembayaran.' - Bagian A']);

        $input = $this->paymentInput();
        $input['nomor_pembayaran'] = $payment->nomor_pembayaran;
        $input['akun_items'] = [['akun_coa_id' => 3, 'nominal' => 100, 'keterangan' => 'Dipindah']];
        $this->controller()->update(Request::create('/pembayaran-pranota-stock/'.$payment->id, 'PUT', $input), $payment->id);

        $this->assertDatabaseCount('pembayaran_pranota_stock_akun_items', 1);
        $this->assertEquals(0, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(-100, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertDatabaseCount('coa_transactions', 2);
    }

    public function test_account_total_must_match_payment_before_any_records_are_written(): void
    {
        $input = $this->paymentInput();
        $input['akun_items'][1]['nominal'] = 30;

        try {
            $this->controller()->store(Request::create('/pembayaran-pranota-stock', 'POST', $input));
            $this->fail('An invalid account total was accepted.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('akun_items', $e->errors());
        }

        $this->assertDatabaseCount('pembayaran_pranota_stocks', 0);
        $this->assertDatabaseCount('coa_transactions', 0);
        $this->assertEquals('approved', DB::table('pranota_stocks')->where('id', 1)->value('status'));
    }

    public function test_migration_recovers_a_table_left_by_a_failed_foreign_key_creation(): void
    {
        Schema::drop('pembayaran_pranota_stock_akun_items');
        Schema::create('pembayaran_pranota_stock_akun_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_stock_id');
            $table->unsignedBigInteger('akun_coa_id');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
        DB::table('pembayaran_pranota_stocks')->insert([
            'id' => 1,
            'nomor_pembayaran' => 'SIS-EXISTING',
            'tanggal_pembayaran' => '2026-10-20',
            'bank' => 'BANK TEST',
            'jenis_transaksi' => 'Kredit',
            'total_pembayaran' => 100,
            'total_tagihan_setelah_penyesuaian' => 100,
            'status_pembayaran' => 'paid',
        ]);
        DB::table('pembayaran_pranota_stock_akun_items')->insert([
            'pembayaran_pranota_stock_id' => 1,
            'akun_coa_id' => 2,
            'nominal' => 100,
        ]);

        $migration = require database_path('migrations/2026_10_20_160000_create_pembayaran_pranota_stock_akun_items_table.php');
        $migration->up();
        $migration->up();

        $this->assertDatabaseCount('pembayaran_pranota_stock_akun_items', 1);
        $columns = collect(Schema::getForeignKeys('pembayaran_pranota_stock_akun_items'))
            ->flatMap(fn ($key) => $key['columns'])->all();
        $this->assertContains('pembayaran_pranota_stock_id', $columns);
        $this->assertContains('akun_coa_id', $columns);
    }

    public function test_kas_truck_report_exposes_every_account_line_for_a_stock_payment(): void
    {
        $bankName = 'Bank BCA Trucking  - 168 2889 955';
        DB::table('akun_coa')->where('id', 1)->update(['nama_akun' => $bankName]);
        $input = $this->paymentInput();
        $input['bank'] = $bankName;
        $this->controller()->store(Request::create('/pembayaran-pranota-stock', 'POST', $input));

        $view = (new KasTruckController)->view(Request::create('/report/kas-truck/view', 'GET'));
        $transactions = $view->getData()['transactions'];
        $this->assertCount(1, $transactions);
        $transaction = $transactions->first();
        $this->assertEquals(100, $transaction->kredit);
        $this->assertSame([
            ['nama_akun' => 'Biaya A', 'nominal' => 60],
            ['nama_akun' => 'Biaya B', 'nominal' => 40],
        ], array_map(fn ($line) => [
            'nama_akun' => $line['nama_akun'],
            'nominal' => (int) $line['nominal'],
        ], $transaction->rincian_akun_pembayaran));
    }

    public function test_kas_truck_report_exposes_every_account_line_for_an_activity_payment(): void
    {
        DB::table('akun_coa')->where('id', 1)->update(['nama_akun' => 'Bank BCA Trucking  - 168 2889 955']);
        DB::table('pembayaran_aktivitas_lains')->insert([
            'nomor' => 'PAL-TEST',
            'nomor_accurate' => 'BTJ26 1000169',
            'akun_coa_id' => 2,
            'akun_bank_id' => 1,
            'debit_kredit' => 'kredit',
            'akun_details' => json_encode([
                ['akun_coa_id' => 2, 'nominal' => 60, 'memo' => 'Bahan bakar'],
                ['akun_coa_id' => 3, 'debit_kredit' => 'kredit', 'nominal' => 30, 'memo' => 'Tol'],
                ['akun_coa_id' => 2, 'nominal' => 10, 'memo' => 'Tambahan'],
            ]),
        ]);
        DB::table('coa_transactions')->insert([
            'coa_id' => 1,
            'tanggal_transaksi' => '2026-10-14',
            'nomor_referensi' => 'PAL-TEST',
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain',
            'keterangan' => 'Pembayaran Aktivitas Lain',
            'debit' => 0,
            'kredit' => 100,
        ]);

        $view = (new KasTruckController)->view(Request::create('/report/kas-truck/view', 'GET'));
        $transactions = $view->getData()['transactions'];
        $this->assertCount(1, $transactions);
        $transaction = $transactions->first();
        $this->assertEquals(100, $transaction->kredit);
        $this->assertSame('BTJ26 1000169', $transaction->nomor_accurate);
        $this->assertSame([
            ['nomor_akun' => '5001', 'nama_akun' => 'Biaya A', 'nominal' => 60, 'memo' => 'Bahan bakar', 'debit_kredit' => 'debit'],
            ['nomor_akun' => '5002', 'nama_akun' => 'Biaya B', 'nominal' => 30, 'memo' => 'Tol', 'debit_kredit' => 'kredit'],
            ['nomor_akun' => '5001', 'nama_akun' => 'Biaya A', 'nominal' => 10, 'memo' => 'Tambahan', 'debit_kredit' => 'debit'],
        ], array_map(fn ($line) => [
            'nomor_akun' => $line['nomor_akun'],
            'nama_akun' => $line['nama_akun'],
            'nominal' => (int) $line['nominal'],
            'memo' => $line['memo'],
            'debit_kredit' => $line['debit_kredit'],
        ], $transaction->rincian_akun_pembayaran));
    }
}

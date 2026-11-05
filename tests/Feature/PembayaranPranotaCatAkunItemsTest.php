<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaCatController;
use App\Models\PembayaranPranotaCat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PembayaranPranotaCatAkunItemsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });
        Schema::create('pranota_tagihan_cat', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->string('status');
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_cat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_cetakan')->nullable();
            $table->string('nomor_accurate')->nullable();
            $table->string('bank');
            $table->foreignId('akun_coa_id')->nullable();
            $table->string('jenis_transaksi');
            $table->date('tanggal_kas');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('penyesuaian', 15, 2)->default(0);
            $table->decimal('total_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_cat_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_cat_id');
            $table->foreignId('pranota_perbaikan_kontainer_id')->nullable();
            $table->foreignId('pranota_tagihan_cat_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_11_05_000001_create_pembayaran_pranota_cat_akun_items_table.php');
        $migration->up();

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Bank', 'saldo' => 1000],
            ['id' => 2, 'nama_akun' => 'Biaya CAT', 'saldo' => 0],
            ['id' => 3, 'nama_akun' => 'Biaya Perbaikan', 'saldo' => 0],
        ]);
        DB::table('pranota_tagihan_cat')->insert([
            'id' => 1, 'no_invoice' => 'CAT-001', 'status' => 'unpaid', 'total_amount' => 100,
        ]);
        Gate::shouldReceive('allows')->with('pembayaran-pranota-cat-create')->andReturn(true);
        Gate::shouldReceive('allows')->with('pembayaran-pranota-cat-update')->andReturn(true);
    }

    public function test_create_saves_account_lines_and_balanced_journal(): void
    {
        $response = Model::withoutEvents(fn () => app(PembayaranPranotaCatController::class)->store(
            Request::create('/pembayaran-pranota-cat', 'POST', $this->payload())
        ));

        $this->assertSame(route('pembayaran-pranota-cat.index'), $response->getTargetUrl());
        $payment = PembayaranPranotaCat::firstOrFail();
        $this->assertSame(2, $payment->akunItems()->count());
        $this->assertSame('Cat dasar', $payment->akunItems()->first()->memo);
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 1, 'kredit' => 100]);
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 2, 'debit' => 60]);
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 3, 'debit' => 40]);
        $this->assertSame('paid', DB::table('pranota_tagihan_cat')->value('status'));
    }

    public function test_create_rejects_account_total_that_differs_from_payment(): void
    {
        $payload = $this->payload();
        $payload['akun_items'][1]['nominal'] = 30;

        try {
            Model::withoutEvents(fn () => app(PembayaranPranotaCatController::class)->store(
                Request::create('/pembayaran-pranota-cat', 'POST', $payload)
            ));
            $this->fail('Jumlah akun yang tidak seimbang diterima.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('akun_items', $e->errors());
        }

        $this->assertSame(0, PembayaranPranotaCat::count());
    }

    public function test_edit_replaces_account_lines_and_journal(): void
    {
        Model::withoutEvents(fn () => app(PembayaranPranotaCatController::class)->store(
            Request::create('/pembayaran-pranota-cat', 'POST', $this->payload())
        ));
        $payment = PembayaranPranotaCat::firstOrFail();

        $response = Model::withoutEvents(fn () => app(PembayaranPranotaCatController::class)->update(
            Request::create('/pembayaran-pranota-cat/'.$payment->id, 'PUT', [
                'bank' => 'Bank',
                'jenis_transaksi' => 'credit',
                'tanggal_kas' => '2026-11-06',
                'penyesuaian' => 0,
                'akun_items' => [
                    ['akun_coa_id' => 2, 'nominal' => 25, 'memo' => 'Cat akhir'],
                    ['akun_coa_id' => 3, 'nominal' => 75, 'memo' => 'Perbaikan akhir'],
                ],
            ]),
            $payment->id
        ));

        $this->assertSame(route('pembayaran-pranota-cat.index'), $response->getTargetUrl());
        $this->assertSame(2, $payment->fresh()->akunItems()->count());
        $this->assertSame('Cat akhir', $payment->fresh()->akunItems()->first()->memo);
        $this->assertSame(3, DB::table('coa_transactions')->count());
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 2, 'debit' => 25]);
        $this->assertDatabaseHas('coa_transactions', ['coa_id' => 3, 'debit' => 75]);
        $this->assertEquals(900, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }

    private function payload(): array
    {
        return [
            'nomor_pembayaran' => 'PAY-CAT-001',
            'bank' => 'Bank',
            'jenis_transaksi' => 'credit',
            'tanggal_kas' => '2026-11-05',
            'pranota_tagihan_ids' => [1],
            'total_tagihan_penyesuaian' => '0',
            'akun_items' => [
                ['akun_coa_id' => 2, 'nominal' => 60, 'memo' => 'Cat dasar'],
                ['akun_coa_id' => 3, 'nominal' => 40, 'memo' => 'Perbaikan'],
            ],
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaUangJalanController;
use App\Models\PembayaranPranotaUangJalan;
use App\Models\PranotaUangJalan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaUangJalanRemovePranotaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->string('status_pembayaran');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('jumlah_uang_jalan')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->string('status_pembayaran');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_uang_jalan_id');
            $table->unsignedBigInteger('pranota_uang_jalan_id');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
        Schema::create('pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_jalan_id');
            $table->unsignedBigInteger('uang_jalan_id');
            $table->timestamps();
        });
        Schema::create('uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->string('status');
            $table->decimal('jumlah_total', 15, 2)->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('status_pembayaran_uang_jalan');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
            $table->string('keterangan');
            $table->timestamps();
        });
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->string('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'ip_address', 'user_agent', 'url'] as $column) {
                $table->string($column)->nullable();
            }
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        DB::table('pranota_uang_jalans')->insert([
            ['id' => 1, 'nomor_pranota' => 'PUJ-001', 'status_pembayaran' => 'paid'],
            ['id' => 2, 'nomor_pranota' => 'PUJ-002', 'status_pembayaran' => 'paid'],
        ]);
        DB::table('pembayaran_pranota_uang_jalans')->insert([
            'id' => 1, 'nomor_pembayaran' => 'SIS-001', 'tanggal_pembayaran' => '2026-10-15',
            'bank' => 'Bank Truck', 'jenis_transaksi' => 'Kredit', 'status_pembayaran' => 'paid',
            'total_pembayaran' => 2100000, 'total_tagihan_penyesuaian' => 100000,
            'total_tagihan_setelah_penyesuaian' => 2200000,
        ]);
        DB::table('pembayaran_pranota_uang_jalan_items')->insert([
            ['pembayaran_pranota_uang_jalan_id' => 1, 'pranota_uang_jalan_id' => 1, 'subtotal' => 1335000],
            ['pembayaran_pranota_uang_jalan_id' => 1, 'pranota_uang_jalan_id' => 2, 'subtotal' => 765000],
        ]);
        DB::table('surat_jalans')->insert(['id' => 2, 'status_pembayaran_uang_jalan' => 'dibayar']);
        DB::table('uang_jalans')->insert(['id' => 2, 'surat_jalan_id' => 2, 'status' => 'lunas', 'jumlah_total' => 765000]);
        DB::table('pranota_uang_jalan_items')->insert(['pranota_uang_jalan_id' => 2, 'uang_jalan_id' => 2]);
        DB::table('prospek')->insert(['id' => 1, 'keterangan' => 'Auto generated | Pranota: PUJ-002']);
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Bank Truck', 'saldo' => -2200000],
            ['id' => 2, 'nama_akun' => 'Biaya Uang Jalan Muat', 'saldo' => 2200000],
        ]);
        DB::table('coa_transactions')->insert([
            ['coa_id' => 1, 'tanggal_transaksi' => '2026-10-15', 'nomor_referensi' => 'SIS-001', 'jenis_transaksi' => 'Pembayaran Pranota Uang Jalan', 'debit' => 0, 'kredit' => 2200000, 'saldo' => -2200000],
            ['coa_id' => 2, 'tanggal_transaksi' => '2026-10-15', 'nomor_referensi' => 'SIS-001', 'jenis_transaksi' => 'Pembayaran Pranota Uang Jalan', 'debit' => 2200000, 'kredit' => 0, 'saldo' => 2200000],
        ]);
    }

    public function test_removing_one_pranota_updates_status_totals_and_journal(): void
    {
        $response = app(PembayaranPranotaUangJalanController::class)->removePranota(
            PembayaranPranotaUangJalan::findOrFail(1), PranotaUangJalan::findOrFail(2)
        );

        $this->assertSame(302, $response->getStatusCode());
        $this->assertNull(session('error'), session('error') ?? '');
        $this->assertSame([1], DB::table('pembayaran_pranota_uang_jalan_items')->pluck('pranota_uang_jalan_id')->all());
        $this->assertSame('unpaid', DB::table('pranota_uang_jalans')->where('id', 2)->value('status_pembayaran'));
        $this->assertSame('paid', DB::table('pranota_uang_jalans')->where('id', 1)->value('status_pembayaran'));
        $this->assertSame('sudah_masuk_pranota', DB::table('uang_jalans')->where('id', 2)->value('status'));
        $this->assertSame('sudah_masuk_uang_jalan', DB::table('surat_jalans')->where('id', 2)->value('status_pembayaran_uang_jalan'));
        $this->assertSame(0, DB::table('prospek')->count());
        $this->assertEquals(1335000, DB::table('pembayaran_pranota_uang_jalans')->where('id', 1)->value('total_pembayaran'));
        $this->assertEquals(1435000, DB::table('pembayaran_pranota_uang_jalans')->where('id', 1)->value('total_tagihan_setelah_penyesuaian'));
        $this->assertEquals(-1435000, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(1435000, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(1435000, DB::table('coa_transactions')->where('coa_id', 2)->value('debit'));
        $this->assertEquals(1435000, DB::table('coa_transactions')->where('coa_id', 1)->value('kredit'));
    }

    public function test_last_pranota_cannot_be_removed(): void
    {
        DB::table('pembayaran_pranota_uang_jalan_items')->where('pranota_uang_jalan_id', 2)->delete();

        app(PembayaranPranotaUangJalanController::class)->removePranota(
            PembayaranPranotaUangJalan::findOrFail(1), PranotaUangJalan::findOrFail(1)
        );

        $this->assertSame(1, DB::table('pembayaran_pranota_uang_jalan_items')->count());
        $this->assertEquals(2100000, DB::table('pembayaran_pranota_uang_jalans')->where('id', 1)->value('total_pembayaran'));
    }

    public function test_failed_journal_update_restores_the_original_payment(): void
    {
        DB::table('akun_coa')->where('id', 1)->delete();

        app(PembayaranPranotaUangJalanController::class)->removePranota(
            PembayaranPranotaUangJalan::findOrFail(1), PranotaUangJalan::findOrFail(2)
        );

        $this->assertSame(2, DB::table('pembayaran_pranota_uang_jalan_items')->count());
        $this->assertSame('paid', DB::table('pranota_uang_jalans')->where('id', 2)->value('status_pembayaran'));
        $this->assertSame('lunas', DB::table('uang_jalans')->where('id', 2)->value('status'));
        $this->assertSame(1, DB::table('prospek')->count());
        $this->assertEquals(2100000, DB::table('pembayaran_pranota_uang_jalans')->where('id', 1)->value('total_pembayaran'));
        $this->assertSame(2, DB::table('coa_transactions')->count());
    }
}

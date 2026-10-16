<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MoveBiayaKapalStuffingToAccountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun');
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('klasifikasi_biayas', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
        });
        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_biaya');
            $table->softDeletes();
        });
        Schema::create('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_biaya_kapal_id');
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->decimal('nominal', 15, 2);
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '1000', 'nama_akun' => 'Bank', 'saldo' => -100],
            ['id' => 2, 'nomor_akun' => '5000', 'nama_akun' => 'Biaya Lama', 'saldo' => 100],
            ['id' => 3, 'nomor_akun' => '5101.025', 'nama_akun' => 'Biaya Stripping/Stuffing Jakarta', 'saldo' => 0],
        ]);
        DB::table('klasifikasi_biayas')->insert(['kode' => 'STF', 'nama' => 'BIAYA STUFFING']);
        DB::table('biaya_kapals')->insert(['id' => 10, 'jenis_biaya' => 'STF']);
        DB::table('pembayaran_biaya_kapals')->insert([
            'id' => 20, 'nomor_pembayaran' => 'PBK-20', 'bank' => 'Bank', 'jenis_transaksi' => 'kredit', 'akun_coa_id' => 2,
        ]);
        DB::table('pembayaran_biaya_kapal_items')->insert([
            'pembayaran_biaya_kapal_id' => 20, 'biaya_kapal_id' => 10, 'nominal' => 100,
        ]);
        DB::table('coa_transactions')->insert([
            ['id' => 1, 'coa_id' => 2, 'nomor_referensi' => 'PBK-20', 'jenis_transaksi' => 'pembayaran_biaya_kapal', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['id' => 2, 'coa_id' => 1, 'nomor_referensi' => 'PBK-20', 'jenis_transaksi' => 'pembayaran_biaya_kapal', 'debit' => 0, 'kredit' => 100, 'saldo' => -100],
        ]);
    }

    public function test_preview_does_not_change_data_and_execute_moves_account_journal_and_balances(): void
    {
        $this->assertSame(0, Artisan::call('pembayaran-biaya-kapal:akun-stuffing'));
        $this->assertSame(2, DB::table('pembayaran_biaya_kapals')->where('id', 20)->value('akun_coa_id'));
        $this->assertSame(2, DB::table('coa_transactions')->where('id', 1)->value('coa_id'));

        $this->assertSame(0, Artisan::call('pembayaran-biaya-kapal:akun-stuffing', ['--execute' => true]));
        $this->assertSame(3, DB::table('pembayaran_biaya_kapals')->where('id', 20)->value('akun_coa_id'));
        $this->assertSame(3, DB::table('coa_transactions')->where('id', 1)->value('coa_id'));
        $this->assertEquals(0, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(100, DB::table('coa_transactions')->where('id', 1)->value('saldo'));

        $this->assertSame(0, Artisan::call('pembayaran-biaya-kapal:akun-stuffing', ['--execute' => true]));
        $this->assertEquals(100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
    }

    public function test_mixed_invoice_payment_aborts_without_changes(): void
    {
        DB::table('biaya_kapals')->insert(['id' => 11, 'jenis_biaya' => 'OTHER']);
        DB::table('pembayaran_biaya_kapal_items')->insert([
            'pembayaran_biaya_kapal_id' => 20, 'biaya_kapal_id' => 11, 'nominal' => 50,
        ]);

        $this->assertSame(1, Artisan::call('pembayaran-biaya-kapal:akun-stuffing', ['--execute' => true]));
        $this->assertSame(2, DB::table('pembayaran_biaya_kapals')->where('id', 20)->value('akun_coa_id'));
        $this->assertSame(2, DB::table('coa_transactions')->where('id', 1)->value('coa_id'));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MovePranotaRitToDriverIncentiveAccountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun');
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_rits', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->decimal('debit', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '5101.001', 'nama_akun' => 'Biaya Insentif Supir Truck Jakarta', 'saldo' => 20],
            ['id' => 2, 'nomor_akun' => 'OLD', 'nama_akun' => 'Biaya Ritasi', 'saldo' => 150],
            ['id' => 3, 'nomor_akun' => 'BANK', 'nama_akun' => 'Bank BCA', 'saldo' => -100],
        ]);
        DB::table('pembayaran_pranota_rits')->insert([
            'nomor_pembayaran' => 'SIS-001', 'bank' => 'Bank BCA',
            'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 100,
            'akun_coa_id' => null,
        ]);
        DB::table('coa_transactions')->insert([
            ['id' => 1, 'coa_id' => 1, 'nomor_referensi' => 'LAIN', 'jenis_transaksi' => 'Lain', 'debit' => 20, 'kredit' => 0, 'saldo' => 20],
            ['id' => 2, 'coa_id' => 2, 'nomor_referensi' => 'SIS-001', 'jenis_transaksi' => 'Pembayaran Pranota Rit', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['id' => 3, 'coa_id' => 3, 'nomor_referensi' => 'SIS-001', 'jenis_transaksi' => 'Pembayaran Pranota Rit', 'debit' => 0, 'kredit' => 100, 'saldo' => -100],
            ['id' => 4, 'coa_id' => 2, 'nomor_referensi' => 'LAIN-2', 'jenis_transaksi' => 'Lain', 'debit' => 50, 'kredit' => 0, 'saldo' => 150],
        ]);
    }

    public function test_preview_execute_and_repeat_keep_journals_and_balances_consistent(): void
    {
        $command = 'pembayaran-pranota-rit:akun-insentif-supir-truck-jakarta';

        $this->artisan($command)->assertSuccessful();
        $this->assertNull(DB::table('pembayaran_pranota_rits')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));

        $this->artisan($command.' --execute')->assertSuccessful();
        $this->assertEquals(1, DB::table('pembayaran_pranota_rits')->value('akun_coa_id'));
        $this->assertEquals(1, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(3, DB::table('coa_transactions')->where('id', 3)->value('coa_id'));
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(50, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(-100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(120, DB::table('coa_transactions')->where('id', 2)->value('saldo'));
        $this->assertEquals(50, DB::table('coa_transactions')->where('id', 4)->value('saldo'));

        $this->artisan($command.' --execute')->assertSuccessful();
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }

    public function test_invalid_journal_rolls_back_all_payments(): void
    {
        DB::table('pembayaran_pranota_rits')->insert([
            'nomor_pembayaran' => 'SIS-002', 'bank' => 'Bank BCA',
            'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 10,
            'akun_coa_id' => null,
        ]);
        DB::table('coa_transactions')->insert([
            'id' => 5, 'coa_id' => 2, 'nomor_referensi' => 'SIS-002',
            'jenis_transaksi' => 'Pembayaran Pranota Rit',
            'debit' => 10, 'kredit' => 0, 'saldo' => 160,
        ]);

        $this->artisan('pembayaran-pranota-rit:akun-insentif-supir-truck-jakarta --execute')->assertFailed();

        $this->assertNull(DB::table('pembayaran_pranota_rits')->where('nomor_pembayaran', 'SIS-001')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(20, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }
}

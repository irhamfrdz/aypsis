<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MovePranotaUangJalanToBiayaTruckingTest extends TestCase
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
        Schema::create('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->string('nomor_referensi');
            $table->decimal('debit', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '5101.011', 'nama_akun' => 'Biaya Trucking', 'saldo' => 20],
            ['id' => 2, 'nomor_akun' => 'OLD', 'nama_akun' => 'Biaya Uang Jalan Muat', 'saldo' => 150],
            ['id' => 3, 'nomor_akun' => 'BANK', 'nama_akun' => 'Bank BCA Trucking', 'saldo' => -100],
        ]);
        DB::table('pembayaran_pranota_uang_jalans')->insert([
            'nomor_pembayaran' => 'PUJ-001', 'bank' => 'Bank BCA Trucking',
            'jenis_transaksi' => 'Kredit', 'akun_coa_id' => null,
        ]);
        DB::table('coa_transactions')->insert([
            ['id' => 1, 'coa_id' => 1, 'nomor_referensi' => 'LAIN', 'debit' => 20, 'kredit' => 0, 'saldo' => 20],
            ['id' => 2, 'coa_id' => 2, 'nomor_referensi' => 'PUJ-001', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['id' => 3, 'coa_id' => 3, 'nomor_referensi' => 'PUJ-001', 'debit' => 0, 'kredit' => 100, 'saldo' => -100],
            ['id' => 4, 'coa_id' => 2, 'nomor_referensi' => 'LAIN-2', 'debit' => 50, 'kredit' => 0, 'saldo' => 150],
        ]);
    }

    public function test_preview_then_execute_moves_expense_entry_and_balances_without_touching_bank(): void
    {
        $this->artisan('pembayaran-pranota-uang-jalan:akun-biaya-trucking')->assertSuccessful();
        $this->assertNull(DB::table('pembayaran_pranota_uang_jalans')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));

        $this->artisan('pembayaran-pranota-uang-jalan:akun-biaya-trucking --execute')->assertSuccessful();
        $this->assertEquals(1, DB::table('pembayaran_pranota_uang_jalans')->value('akun_coa_id'));
        $this->assertEquals(1, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(3, DB::table('coa_transactions')->where('id', 3)->value('coa_id'));
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(50, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(-100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(120, DB::table('coa_transactions')->where('id', 2)->value('saldo'));
        $this->assertEquals(50, DB::table('coa_transactions')->where('id', 4)->value('saldo'));

        $this->artisan('pembayaran-pranota-uang-jalan:akun-biaya-trucking --execute')->assertSuccessful();
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }

    public function test_invalid_journal_rolls_back_every_change(): void
    {
        DB::table('pembayaran_pranota_uang_jalans')->insert([
            'nomor_pembayaran' => 'PUJ-002', 'bank' => 'Bank BCA Trucking',
            'jenis_transaksi' => 'Kredit', 'akun_coa_id' => null,
        ]);
        DB::table('coa_transactions')->insert([
            'id' => 5, 'coa_id' => 2, 'nomor_referensi' => 'PUJ-002',
            'debit' => 10, 'kredit' => 0, 'saldo' => 160,
        ]);

        $this->artisan('pembayaran-pranota-uang-jalan:akun-biaya-trucking --execute')->assertFailed();

        $this->assertNull(DB::table('pembayaran_pranota_uang_jalans')->where('nomor_pembayaran', 'PUJ-001')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(20, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }
}

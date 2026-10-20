<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MovePembayaranPranotaCatToBiayaPerbaikanAlatBeratJakartaTest extends TestCase
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
        Schema::create('pembayaran_pranota_cat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('bank');
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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->string('module')->nullable();
            $table->string('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '5101.053', 'nama_akun' => 'Biaya Perbaikan Alat Berat Jakarta', 'saldo' => 20],
            ['id' => 2, 'nomor_akun' => 'OLD', 'nama_akun' => 'Biaya CAT Kontainer', 'saldo' => 150],
            ['id' => 3, 'nomor_akun' => 'BANK', 'nama_akun' => 'Bank BCA', 'saldo' => -100],
        ]);
        DB::table('pembayaran_pranota_cat')->insert([
            'nomor_pembayaran' => 'CAT-001', 'bank' => 'Bank BCA', 'akun_coa_id' => 2,
        ]);
        DB::table('coa_transactions')->insert([
            ['id' => 1, 'coa_id' => 1, 'nomor_referensi' => 'LAIN', 'jenis_transaksi' => 'Lain', 'debit' => 20, 'kredit' => 0, 'saldo' => 20],
            ['id' => 2, 'coa_id' => 2, 'nomor_referensi' => 'CAT-001', 'jenis_transaksi' => 'Pembayaran Pranota CAT Kontainer', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['id' => 3, 'coa_id' => 3, 'nomor_referensi' => 'CAT-001', 'jenis_transaksi' => 'Pembayaran Pranota CAT Kontainer', 'debit' => 0, 'kredit' => 100, 'saldo' => -100],
            ['id' => 4, 'coa_id' => 2, 'nomor_referensi' => 'LAIN-2', 'jenis_transaksi' => 'Lain', 'debit' => 50, 'kredit' => 0, 'saldo' => 150],
        ]);
    }

    public function test_preview_and_execute_move_only_expense_journal_and_recalculate_balances(): void
    {
        $this->artisan('pembayaran-pranota-cat:akun-perbaikan-alat-berat-jakarta')->assertSuccessful();
        $this->assertEquals(2, DB::table('pembayaran_pranota_cat')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));

        $status = Artisan::call('pembayaran-pranota-cat:akun-perbaikan-alat-berat-jakarta', ['--execute' => true]);
        $this->assertSame(0, $status, Artisan::output());
        $this->assertEquals(1, DB::table('pembayaran_pranota_cat')->value('akun_coa_id'));
        $this->assertEquals(1, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(3, DB::table('coa_transactions')->where('id', 3)->value('coa_id'));
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(50, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(-100, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(120, DB::table('coa_transactions')->where('id', 2)->value('saldo'));
        $this->assertEquals(50, DB::table('coa_transactions')->where('id', 4)->value('saldo'));

        $this->artisan('pembayaran-pranota-cat:akun-perbaikan-alat-berat-jakarta --execute')->assertSuccessful();
        $this->assertEquals(120, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }

    public function test_invalid_journal_rolls_back_payment_and_account_changes(): void
    {
        DB::table('pembayaran_pranota_cat')->insert([
            'nomor_pembayaran' => 'CAT-002', 'bank' => 'Bank BCA', 'akun_coa_id' => 2,
        ]);
        DB::table('coa_transactions')->insert([
            'id' => 5, 'coa_id' => 2, 'nomor_referensi' => 'CAT-002',
            'jenis_transaksi' => 'Pembayaran Pranota CAT Kontainer',
            'debit' => 10, 'kredit' => 0, 'saldo' => 160,
        ]);

        $this->artisan('pembayaran-pranota-cat:akun-perbaikan-alat-berat-jakarta --execute')->assertFailed();

        $this->assertEquals(2, DB::table('pembayaran_pranota_cat')->where('nomor_pembayaran', 'CAT-001')->value('akun_coa_id'));
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertEquals(20, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }
}

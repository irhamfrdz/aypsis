<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MovePranotaRitKenekToDriverIncentiveAccountTest extends TestCase
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
        Schema::create('pembayaran_pranota_rit_keneks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->decimal('saldo', 15, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '5101.001', 'nama_akun' => 'Biaya Insentif Supir Truck Jakarta', 'saldo' => 20],
            ['id' => 2, 'nomor_akun' => 'OLD', 'nama_akun' => 'Biaya Lama', 'saldo' => 100],
            ['id' => 3, 'nomor_akun' => 'BANK', 'nama_akun' => 'Bank BCA', 'saldo' => -150],
        ]);
        DB::table('pembayaran_pranota_rit_keneks')->insert([
            ['nomor_pembayaran' => 'SIS-K-001', 'tanggal_pembayaran' => '2026-10-01', 'bank' => 'Bank BCA', 'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 100],
            ['nomor_pembayaran' => 'SIS-K-002', 'tanggal_pembayaran' => '2026-10-02', 'bank' => 'Bank BCA', 'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 50],
            ['nomor_pembayaran' => 'SIS-K-003', 'tanggal_pembayaran' => '2026-10-03', 'bank' => 'Bank BCA', 'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 30],
        ]);
        DB::table('coa_transactions')->insert([
            ['id' => 1, 'coa_id' => 1, 'nomor_referensi' => 'LAIN', 'tanggal_transaksi' => '2026-09-01', 'jenis_transaksi' => 'Lain', 'debit' => 20, 'kredit' => 0, 'saldo' => 20],
            ['id' => 2, 'coa_id' => 2, 'nomor_referensi' => 'SIS-K-001', 'tanggal_transaksi' => '2026-10-01', 'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['id' => 3, 'coa_id' => 3, 'nomor_referensi' => 'SIS-K-001', 'tanggal_transaksi' => '2026-10-01', 'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek', 'debit' => 0, 'kredit' => 100, 'saldo' => -100],
            ['id' => 4, 'coa_id' => 3, 'nomor_referensi' => 'SIS-K-002', 'tanggal_transaksi' => '2026-10-02', 'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek', 'debit' => 0, 'kredit' => 50, 'saldo' => -150],
        ]);
    }

    public function test_preview_execute_and_repeat_keep_all_payments_and_journals_consistent(): void
    {
        $command = 'pembayaran-pranota-rit-kenek:akun-insentif-supir-truck-jakarta';

        $this->artisan($command)->assertSuccessful();
        $this->assertSame(0, DB::table('pembayaran_pranota_rit_keneks')->whereNotNull('akun_coa_id')->count());
        $this->assertSame(4, DB::table('coa_transactions')->count());

        $this->artisan($command.' --execute')->assertSuccessful();
        $this->assertSame(3, DB::table('pembayaran_pranota_rit_keneks')->where('akun_coa_id', 1)->count());
        foreach (['SIS-K-001', 'SIS-K-002', 'SIS-K-003'] as $reference) {
            $this->assertSame(2, DB::table('coa_transactions')->where('nomor_referensi', $reference)->count());
        }
        $this->assertEquals(200, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(0, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(-180, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(200, DB::table('coa_transactions')->where('coa_id', 1)->orderByDesc('id')->value('saldo'));
        $this->assertEquals(-180, DB::table('coa_transactions')->where('coa_id', 3)->orderByDesc('id')->value('saldo'));

        $this->artisan($command.' --execute')->assertSuccessful();
        $this->assertSame(7, DB::table('coa_transactions')->count());
        $this->assertEquals(200, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }

    public function test_invalid_journal_rolls_back_all_changes(): void
    {
        DB::table('pembayaran_pranota_rit_keneks')->insert([
            'nomor_pembayaran' => 'SIS-K-004', 'tanggal_pembayaran' => '2026-10-04',
            'bank' => 'Bank BCA', 'jenis_transaksi' => 'Kredit', 'total_tagihan_setelah_penyesuaian' => 10,
        ]);
        DB::table('coa_transactions')->insert([
            'coa_id' => 3, 'nomor_referensi' => 'SIS-K-004', 'tanggal_transaksi' => '2026-10-04',
            'jenis_transaksi' => 'Pembayaran Pranota Rit Kenek', 'debit' => 0, 'kredit' => 9, 'saldo' => -159,
        ]);

        $this->artisan('pembayaran-pranota-rit-kenek:akun-insentif-supir-truck-jakarta --execute')->assertFailed();

        $this->assertSame(0, DB::table('pembayaran_pranota_rit_keneks')->whereNotNull('akun_coa_id')->count());
        $this->assertEquals(2, DB::table('coa_transactions')->where('id', 2)->value('coa_id'));
        $this->assertSame(5, DB::table('coa_transactions')->count());
        $this->assertEquals(20, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }
}

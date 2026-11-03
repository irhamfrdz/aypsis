<?php

namespace Tests\Feature;

use App\Http\Controllers\Report\KasTruckController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KasTruckUangJalanAccountDetailTest extends TestCase
{
    public function test_report_displays_every_uang_jalan_account_with_its_amount_and_memo(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun')->nullable();
            $table->string('nama_akun');
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
        });
        Schema::create('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->nullable();
            $table->string('metode_pembatalan')->nullable();
        });
        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->unsignedBigInteger('akun_bank_id')->nullable();
            $table->decimal('pph_nominal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('nomor_accurate')->nullable();
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_uang_jalan_id');
            $table->unsignedBigInteger('akun_coa_id');
            $table->decimal('nominal', 15, 2);
            $table->text('memo')->nullable();
        });

        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => null, 'nama_akun' => 'Bank BCA Trucking  - 168 2889 955'],
            ['id' => 2, 'nomor_akun' => '5101.011', 'nama_akun' => 'Biaya Trucking'],
            ['id' => 3, 'nomor_akun' => '5101.021', 'nama_akun' => 'Biaya Tambahan'],
        ]);
        DB::table('coa_transactions')->insert([
            'coa_id' => 1, 'tanggal_transaksi' => '2026-08-11', 'nomor_referensi' => 'SIS-08-26-000924',
            'jenis_transaksi' => 'Pembayaran Pranota Uang Jalan', 'kredit' => 4210000,
        ]);
        DB::table('pembayaran_pranota_uang_jalans')->insert([
            'id' => 1, 'nomor_pembayaran' => 'SIS-08-26-000924', 'nomor_accurate' => 'BTJ26 0800142',
            'akun_coa_id' => 2, 'bank' => 'Bank BCA Trucking  - 168 2889 955', 'jenis_transaksi' => 'Kredit',
        ]);
        DB::table('pembayaran_pranota_uang_jalan_akun_items')->insert([
            ['pembayaran_pranota_uang_jalan_id' => 1, 'akun_coa_id' => 2, 'nominal' => 2365000, 'memo' => 'Uang jalan No. Pranota : PUJ-001'],
            ['pembayaran_pranota_uang_jalan_id' => 1, 'akun_coa_id' => 3, 'nominal' => 1845000, 'memo' => 'Uang jalan No. Pranota : PUJ-002'],
        ]);

        $view = app(KasTruckController::class)->view(Request::create('/kas-truck/view'));
        $details = $view->getData()['transactions']->first()->rincian_akun_pembayaran;

        $this->assertCount(2, $details);
        $this->assertSame(['Biaya Trucking', 'Biaya Tambahan'], array_column($details, 'nama_akun'));
        $this->assertEquals([2365000, 1845000], array_column($details, 'nominal'));
        $this->assertSame(['Uang jalan No. Pranota : PUJ-001', 'Uang jalan No. Pranota : PUJ-002'], array_column($details, 'memo'));
        $this->assertSame(['debit', 'debit'], array_column($details, 'debit_kredit'));
    }
}

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
    public function test_report_displays_uang_jalan_and_activity_payment_accounts_including_pph(): void
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
            $table->string('nomor_accurate')->nullable();
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->unsignedBigInteger('akun_bank_id')->nullable();
            $table->unsignedBigInteger('akun_pph_id')->nullable();
            $table->json('akun_details')->nullable();
            $table->string('debit_kredit')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('pph_persen', 3, 1)->nullable();
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
        Schema::create('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('nomor_accurate')->nullable();
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_ongkos_truk_akun_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_ongkos_truk_id');
            $table->unsignedBigInteger('akun_coa_id');
            $table->decimal('nominal', 15, 2);
            $table->string('memo')->nullable();
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

        DB::table('akun_coa')->insert(['id' => 4, 'nomor_akun' => '2102.005', 'nama_akun' => 'Utang PPh Kendaraan']);
        DB::table('coa_transactions')->insert([
            'coa_id' => 1, 'tanggal_transaksi' => '2026-08-10', 'nomor_referensi' => 'PAL-001',
            'jenis_transaksi' => 'Pembayaran Aktivitas Lain', 'kredit' => 1399125,
        ]);
        DB::table('pembayaran_aktivitas_lains')->insert([
            'nomor' => 'PAL-001', 'nomor_accurate' => 'BTJ26 0800137', 'akun_coa_id' => 2,
            'akun_bank_id' => 1, 'akun_pph_id' => 4, 'debit_kredit' => 'kredit',
            'jumlah' => 1400000, 'pph_persen' => 2.5, 'pph_nominal' => 875, 'grand_total' => 1399125,
            'akun_details' => json_encode([
                ['akun_coa_id' => 2, 'nominal' => 1365000, 'memo' => 'perbaikan kendaraan', 'debit_kredit' => 'debit'],
                ['akun_coa_id' => 2, 'nominal' => 35000, 'memo' => 'jasa perbaikan', 'debit_kredit' => 'debit'],
            ]),
        ]);

        $view = app(KasTruckController::class)->view(Request::create('/kas-truck/view'));
        $payment = $view->getData()['transactions']->firstWhere('nomor_referensi', 'PAL-001');
        $paymentDetails = $payment->rincian_akun_pembayaran;

        $this->assertCount(3, $paymentDetails);
        $this->assertSame(['Biaya Trucking', 'Biaya Trucking', 'Utang PPh Kendaraan'], array_column($paymentDetails, 'nama_akun'));
        $this->assertEquals([1365000, 35000, 875], array_column($paymentDetails, 'nominal'));
        $this->assertSame(['debit', 'debit', 'kredit'], array_column($paymentDetails, 'debit_kredit'));
        $this->assertSame('Potongan PPh 2,5%', $paymentDetails[2]['memo']);

        DB::table('akun_coa')->insert(['id' => 5, 'nomor_akun' => '5101.031', 'nama_akun' => 'Biaya Ongkos Truk Tambahan']);
        DB::table('coa_transactions')->insert([
            'coa_id' => 1, 'tanggal_transaksi' => '2026-08-12', 'nomor_referensi' => 'SIS-08-26-000925',
            'jenis_transaksi' => 'Pembayaran Pranota Ongkos Truk', 'kredit' => 100000,
        ]);
        DB::table('pembayaran_pranota_ongkos_truks')->insert([
            'id' => 1, 'nomor_pembayaran' => 'SIS-08-26-000925', 'nomor_accurate' => 'BTJ26 0800143',
            'bank' => 'Bank BCA Trucking  - 168 2889 955', 'jenis_transaksi' => 'Kredit',
        ]);
        DB::table('pembayaran_pranota_ongkos_truk_akun_items')->insert([
            ['pembayaran_pranota_ongkos_truk_id' => 1, 'akun_coa_id' => 2, 'nominal' => 60000, 'memo' => 'Ongkos utama'],
            ['pembayaran_pranota_ongkos_truk_id' => 1, 'akun_coa_id' => 5, 'nominal' => 40000, 'memo' => 'Ongkos tambahan'],
        ]);

        $view = app(KasTruckController::class)->view(Request::create('/kas-truck/view'));
        $truckPayment = $view->getData()['transactions']->firstWhere('nomor_referensi', 'SIS-08-26-000925');
        $truckDetails = $truckPayment->rincian_akun_pembayaran;

        $this->assertCount(2, $truckDetails);
        $this->assertSame(['Biaya Trucking', 'Biaya Ongkos Truk Tambahan'], array_column($truckDetails, 'nama_akun'));
        $this->assertEquals([60000, 40000], array_column($truckDetails, 'nominal'));
        $this->assertSame(['Ongkos utama', 'Ongkos tambahan'], array_column($truckDetails, 'memo'));
        $this->assertSame(['debit', 'debit'], array_column($truckDetails, 'debit_kredit'));
    }
}

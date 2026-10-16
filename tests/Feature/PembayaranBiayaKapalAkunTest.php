<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Services\CoaTransactionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranBiayaKapalAkunTest extends TestCase
{
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
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
            $table->string('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'BANK TEST'],
            ['id' => 2, 'nama_akun' => 'Biaya Trucking'],
            ['id' => 3, 'nama_akun' => 'Biaya Trucking'],
        ]);
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    public function test_invoice_and_adjustment_use_the_selected_coa_id(): void
    {
        $payment = (object) [
            'akun_coa_id' => 3,
            'akunCoa' => Coa::findOrFail(3),
            'bank' => 'BANK TEST',
            'jenis_transaksi' => 'kredit',
            'tanggal_pembayaran' => '2026-10-16',
            'nomor_pembayaran' => 'PBK-TEST',
            'total_tagihan_penyesuaian' => 25,
            'alasan_penyesuaian' => 'Tambahan biaya',
            'biayaKapals' => collect([(object) [
                'pivot' => (object) ['nominal' => 100],
                'nomor_invoice' => 'INV-TEST',
                'display_nama_kapal' => 'Kapal Test',
                'klasifikasiBiaya' => (object) ['nama' => 'Biaya Perbaikan Kapal'],
            ]]),
        ];

        $this->assertTrue(app(CoaTransactionService::class)->pembayaranBiayaKapal($payment));
        $this->assertEquals(2, DB::table('coa_transactions')->where('coa_id', 3)->count());
        $this->assertEquals(0, DB::table('coa_transactions')->where('coa_id', 2)->count());
        $this->assertEquals(2, DB::table('coa_transactions')->where('coa_id', 1)->count());
        $this->assertEquals(125, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(-125, DB::table('akun_coa')->where('id', 1)->value('saldo'));
    }
}

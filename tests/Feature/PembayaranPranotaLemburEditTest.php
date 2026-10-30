<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaLemburController;
use App\Models\PembayaranPranotaLembur;
use App\Models\PranotaLembur;
use App\Services\CoaTransactionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaLemburEditTest extends TestCase
{
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();

        Schema::create('pranota_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->date('tanggal_pranota');
            $table->decimal('total_setelah_adjustment', 15, 2);
            $table->string('status');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->string('jenis_transaksi');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->string('status_pembayaran');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pembayaran_pranota_lembur_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_lembur_id');
            $table->unsignedBigInteger('pranota_lembur_id');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
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
            $table->softDeletes();
        });
        Schema::create('nomor_terakhir', function (Blueprint $table) {
            $table->id();
            $table->string('modul')->unique();
            $table->unsignedBigInteger('nomor_terakhir')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    public function test_edit_replaces_pranota_and_rebuilds_journal_for_new_bank_and_amount(): void
    {
        $oldPranota = PranotaLembur::create([
            'nomor_pranota' => 'PL-OLD',
            'tanggal_pranota' => '2026-10-01',
            'total_setelah_adjustment' => 100,
            'status' => PranotaLembur::STATUS_PAID,
        ]);
        $newPranota = PranotaLembur::create([
            'nomor_pranota' => 'PL-NEW',
            'tanggal_pranota' => '2026-10-02',
            'total_setelah_adjustment' => 200,
            'status' => PranotaLembur::STATUS_APPROVED,
        ]);
        $payment = PembayaranPranotaLembur::create([
            'nomor_pembayaran' => 'SIS-TEST',
            'tanggal_pembayaran' => '2026-10-03',
            'bank' => 'Bank Lama',
            'jenis_transaksi' => 'Kredit',
            'total_pembayaran' => 100,
            'total_tagihan_penyesuaian' => 0,
            'total_tagihan_setelah_penyesuaian' => 100,
            'status_pembayaran' => PembayaranPranotaLembur::STATUS_PAID,
        ]);
        $payment->pranotaLemburs()->attach($oldPranota->id, ['subtotal' => 100]);
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Bank Lama', 'saldo' => 1000],
            ['id' => 2, 'nama_akun' => 'Bank Baru', 'saldo' => 1000],
            ['id' => 3, 'nama_akun' => 'Biaya Lembur Jakarta', 'saldo' => 0],
            ['id' => 4, 'nama_akun' => 'Biaya Lembur Batam', 'saldo' => 0],
        ]);
        $service = app(CoaTransactionService::class);
        $this->assertTrue($service->recordDoubleEntry(
            ['nama_akun' => 'Biaya Lembur Jakarta', 'jumlah' => 100],
            ['nama_akun' => 'Bank Lama', 'jumlah' => 100],
            '2026-10-03', 'SIS-TEST', 'Pembayaran Pranota Lembur'
        ));

        $response = app(PembayaranPranotaLemburController::class)->update(
            Request::create('/pembayaran-pranota-lembur/'.$payment->id, 'PUT', [
                'pranota_lembur_ids' => [$newPranota->id],
                'nomor_accurate' => 'ACC-123',
                'tanggal_pembayaran' => '2026-10-04',
                'bank' => 'Bank Baru',
                'akun_coa_id' => 4,
                'jenis_transaksi' => 'Kredit',
                'total_tagihan_penyesuaian' => -20,
                'keterangan' => 'Diperbarui',
                'total_pembayaran' => 9999,
                'total_tagihan_setelah_penyesuaian' => 9999,
            ]),
            $payment
        );

        $this->assertSame(route('pembayaran-pranota-lembur.index'), $response->getTargetUrl());
        $payment->refresh();
        $this->assertEquals(200, $payment->total_pembayaran);
        $this->assertEquals(-20, $payment->total_tagihan_penyesuaian);
        $this->assertEquals(180, $payment->total_tagihan_setelah_penyesuaian);
        $this->assertSame('Bank Baru', $payment->bank);
        $this->assertSame(4, $payment->akun_coa_id);
        $this->assertSame([$newPranota->id], $payment->pranotaLemburs->pluck('id')->all());
        $this->assertSame(PranotaLembur::STATUS_APPROVED, $oldPranota->fresh()->status);
        $this->assertSame(PranotaLembur::STATUS_PAID, $newPranota->fresh()->status);
        $this->assertEquals(1000, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertEquals(820, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(0, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(180, DB::table('akun_coa')->where('id', 4)->value('saldo'));
        $this->assertSame(2, DB::table('coa_transactions')->where('nomor_referensi', 'SIS-TEST')->whereNull('deleted_at')->count());
    }

    public function test_create_uses_selected_expense_account_in_payment_and_journal(): void
    {
        $pranota = PranotaLembur::create([
            'nomor_pranota' => 'PL-NEW',
            'tanggal_pranota' => '2026-10-02',
            'total_setelah_adjustment' => 200,
            'status' => PranotaLembur::STATUS_APPROVED,
        ]);
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Bank Test', 'saldo' => 1000],
            ['id' => 2, 'nama_akun' => 'Biaya Lembur Jakarta', 'saldo' => 0],
            ['id' => 3, 'nama_akun' => 'Biaya Lembur Batam', 'saldo' => 0],
        ]);
        DB::table('nomor_terakhir')->insert(['modul' => 'SIS', 'nomor_terakhir' => 0]);

        $response = app(PembayaranPranotaLemburController::class)->store(
            Request::create('/pembayaran-pranota-lembur', 'POST', [
                'pranota_lembur_ids' => [$pranota->id],
                'tanggal_pembayaran' => '2026-10-04',
                'bank' => 'Bank Test',
                'akun_coa_id' => 3,
                'jenis_transaksi' => 'Kredit',
                'total_pembayaran' => 200,
                'total_tagihan_penyesuaian' => 0,
                'total_tagihan_setelah_penyesuaian' => 200,
            ])
        );

        $this->assertSame(route('pembayaran-pranota-lembur.index'), $response->getTargetUrl());
        $payment = PembayaranPranotaLembur::firstOrFail();
        $this->assertSame(3, $payment->akun_coa_id);
        $this->assertEquals(200, DB::table('akun_coa')->where('id', 3)->value('saldo'));
        $this->assertEquals(0, DB::table('akun_coa')->where('id', 2)->value('saldo'));
        $this->assertEquals(800, DB::table('akun_coa')->where('id', 1)->value('saldo'));
        $this->assertSame(PranotaLembur::STATUS_PAID, $pranota->fresh()->status);
    }
}

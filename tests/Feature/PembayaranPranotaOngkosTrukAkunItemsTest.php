<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaOngkosTrukController;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaOngkosTruk;
use App\Services\CoaTransactionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PembayaranPranotaOngkosTrukAkunItemsTest extends TestCase
{
    public function test_update_rejects_account_costs_that_do_not_match_final_payment(): void
    {
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
        });
        Schema::create('pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->string('status_pembayaran');
            $table->timestamps();
            $table->softDeletes();
        });
        \DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Bank Trucking'],
            ['id' => 2, 'nama_akun' => 'Biaya A'],
        ]);
        \DB::table('pranota_ongkos_truks')->insert(['id' => 1]);
        \DB::table('pembayaran_pranota_ongkos_truks')->insert([
            'id' => 1,
            'nomor_pembayaran' => 'SIS-TEST-2',
            'status_pembayaran' => 'paid',
        ]);

        $request = Request::create('/pembayaran-pranota-ongkos-truk/1', 'PUT', [
            'pranota_ongkos_truk_ids' => [1],
            'nomor_pembayaran' => 'SIS-TEST-2',
            'tanggal_pembayaran' => '2026-10-29',
            'jenis_transaksi' => 'Kredit',
            'bank' => 'Bank Trucking',
            'total_pembayaran' => 100,
            'total_tagihan_setelah_penyesuaian' => 100,
            'akun_items' => [['akun_coa_id' => 2, 'nominal' => 90]],
        ]);

        $this->expectException(ValidationException::class);
        (new PembayaranPranotaOngkosTrukController(new CoaTransactionService))->update($request, 1);
    }

    public function test_account_costs_and_memos_create_balanced_journal(): void
    {
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran');
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->string('keterangan');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_29_103500_create_pembayaran_pranota_ongkos_truk_akun_items_table.php'))->up();

        $dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        try {
            $bank = Coa::create(['nama_akun' => 'Bank Trucking', 'saldo' => 1000]);
            $expenseA = Coa::create(['nama_akun' => 'Biaya A', 'saldo' => 0]);
            $expenseB = Coa::create(['nama_akun' => 'Biaya B', 'saldo' => 0]);
            $payment = PembayaranPranotaOngkosTruk::create([
                'nomor_pembayaran' => 'SIS-TEST-1',
                'tanggal_pembayaran' => '2026-10-29',
                'bank' => $bank->nama_akun,
                'jenis_transaksi' => 'Kredit',
                'total_tagihan_setelah_penyesuaian' => 100,
            ]);
            $payment->akunItems()->createMany([
                ['akun_coa_id' => $expenseA->id, 'nominal' => 60, 'memo' => 'Memo A'],
                ['akun_coa_id' => $expenseB->id, 'nominal' => 40, 'memo' => 'Memo B'],
            ]);

            $controller = new PembayaranPranotaOngkosTrukController(new CoaTransactionService);
            (new \ReflectionMethod($controller, 'recordAccountEntries'))->invoke($controller, $payment);

            $lines = CoaTransaction::orderBy('id')->get();
            $this->assertCount(3, $lines);
            $this->assertEquals(100, $lines->sum('debit'));
            $this->assertEquals(100, $lines->sum('kredit'));
            $this->assertEquals(60, $lines->where('coa_id', $expenseA->id)->first()->debit);
            $this->assertEquals(40, $lines->where('coa_id', $expenseB->id)->first()->debit);
            $this->assertEquals(100, $lines->where('coa_id', $bank->id)->first()->kredit);
            $this->assertStringContainsString('Memo A', $lines->where('coa_id', $expenseA->id)->first()->keterangan);
            $this->assertStringContainsString('Memo B', $lines->where('coa_id', $expenseB->id)->first()->keterangan);
            $this->assertEquals(900, $bank->fresh()->saldo);
        } finally {
            Model::setEventDispatcher($dispatcher);
        }
    }
}

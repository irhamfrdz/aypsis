<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranAktivitasLainController;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranAktivitasLain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranAktivitasLainAkunDetailsTest extends TestCase
{
    public function test_mixed_account_directions_create_balanced_journal_and_net_bank_payment(): void
    {
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo', 18, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->string('keterangan');
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->decimal('saldo', 18, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        foreach ([1, 2, 3] as $id) {
            DB::table('akun_coa')->insert(['id' => $id, 'saldo' => 1000]);
        }

        $dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        try {
            $payment = new PembayaranAktivitasLain;
            $payment->forceFill(['nomor' => 'TEST-001', 'pph_nominal' => 0, 'grand_total' => 70]);
            $validated = [
                'tanggal' => '2026-10-29',
                'jumlah' => 70,
                'debit_kredit' => 'kredit',
                'jenis_aktivitas' => 'Pembayaran Lain Lain',
                'keterangan' => 'Uji jurnal',
                'akun_bank_id' => 3,
                'akun_details' => [
                    ['akun_coa_id' => 1, 'debit_kredit' => 'debit', 'nominal' => 100, 'memo' => 'Biaya'],
                    ['akun_coa_id' => 2, 'debit_kredit' => 'kredit', 'nominal' => 30, 'memo' => 'Koreksi'],
                ],
            ];

            $controller = new PembayaranAktivitasLainController;
            $method = new \ReflectionMethod($controller, 'createDoubleBookJournal');
            $method->invoke($controller, $payment, $validated);

            $lines = CoaTransaction::orderBy('id')->get();
            $this->assertCount(3, $lines);
            $this->assertEqualsCanonicalizing([1, 2, 3], $lines->pluck('coa_id')->all());
            $this->assertEquals(100, $lines->sum('debit'));
            $this->assertEquals(100, $lines->sum('kredit'));
            $this->assertEquals(70, $lines->where('coa_id', 3)->first()->kredit);
            $this->assertEquals(1100, Coa::find(1)->saldo);
            $this->assertEquals(970, Coa::find(2)->saldo);
            $this->assertEquals(930, Coa::find(3)->saldo);

            $payment->nomor = 'TEST-002';
            $validated['akun_details'] = [
                ['akun_coa_id' => 1, 'debit_kredit' => 'kredit', 'nominal' => 100],
                ['akun_coa_id' => 2, 'debit_kredit' => 'debit', 'nominal' => 30],
            ];
            $method->invoke($controller, $payment, $validated);

            $reverseLines = CoaTransaction::where('nomor_referensi', 'TEST-002')->get();
            $this->assertEquals(100, $reverseLines->sum('debit'));
            $this->assertEquals(100, $reverseLines->sum('kredit'));
            $this->assertEquals(70, $reverseLines->where('coa_id', 3)->first()->debit);
            $this->assertEquals(1000, Coa::find(1)->saldo);
            $this->assertEquals(1000, Coa::find(2)->saldo);
            $this->assertEquals(1000, Coa::find(3)->saldo);
        } finally {
            Model::setEventDispatcher($dispatcher);
        }
    }
}

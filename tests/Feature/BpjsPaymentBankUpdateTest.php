<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaBpjsController;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsHeader;
use App\Services\BpjsPaymentBankUpdater;
use App\Services\CoaTransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BpjsPaymentBankUpdateTest extends TestCase
{
    private PembayaranPranotaBpjs $payment;

    protected function setUp(): void
    {
        parent::setUp();
        foreach ([
            '2026_08_28_112737_create_pranota_bpjs_headers_table.php',
            '2026_08_28_112738_create_pranota_bpjs_details_table.php',
            '2026_11_03_100000_create_pembayaran_pranota_bpjs_tables.php',
            '2026_11_03_110000_allow_split_bpjs_payments.php',
            '2026_11_05_130000_add_bank_allocations_to_pembayaran_pranota_bpjs.php',
            '2026_11_10_180000_add_journal_allocations_to_bpjs_payments.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun');
            $table->string('nama_akun');
            $table->string('tipe_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        (require database_path('migrations/2025_10_06_153821_create_coa_transactions_table.php'))->up();
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '1001', 'nama_akun' => 'Bank Lama', 'tipe_akun' => 'Bank', 'saldo' => 1000],
            ['id' => 2, 'nomor_akun' => '1002', 'nama_akun' => 'Bank Baru', 'tipe_akun' => 'Bank', 'saldo' => 2000],
            ['id' => 3, 'nomor_akun' => '6101.006', 'nama_akun' => 'Biaya BPJS Jakarta', 'tipe_akun' => 'Biaya', 'saldo' => 0],
            ['id' => 4, 'nomor_akun' => '2103.002', 'nama_akun' => 'Utang BPJS', 'tipe_akun' => 'Hutang', 'saldo' => 0],
            ['id' => 5, 'nomor_akun' => '6101.001', 'nama_akun' => 'Gaji Jakarta', 'tipe_akun' => 'Biaya', 'saldo' => 0],
        ]);
        $allocations = collect([5 => 30.25, 3 => 50, 4 => 20.25])->map(fn ($amount, $id) => [
            'coa_id' => $id, 'subtotal' => $amount,
            'nomor_akun' => Coa::find($id)->nomor_akun, 'nama_akun' => Coa::find($id)->nama_akun,
        ])->values()->all();
        $this->payment = PembayaranPranotaBpjs::create([
            'nomor_pembayaran' => 'BANK-EDIT', 'tanggal_pembayaran' => '2026-11-12',
            'bank_coa_id' => 5, 'akun_coa_id' => 1, 'total_pembayaran' => 100.50,
            'nomor_accurate' => 'KEEP-ACCURATE', 'journal_allocations' => $allocations,
            'bank_allocations' => [['coa_id' => 5, 'nomor_akun' => '6101.001', 'nama_akun' => 'Gaji Jakarta', 'subtotal' => 100.50]],
        ]);
        $pranota = PranotaBpjsHeader::create([
            'nomor_pranota' => 'PBPJS-EDIT', 'tanggal_pranota' => '2026-11-12',
            'periode_bulan' => 11, 'periode_tahun' => 2026, 'status' => 'paid', 'grand_total' => 100.50,
        ]);
        $this->payment->pranotas()->attach($pranota->id, ['subtotal' => 100.50]);
        Coa::withoutEvents(function () use ($allocations) {
            foreach ($allocations as $allocation) {
                $this->assertTrue((new CoaTransactionService)->recordDoubleEntry(
                    ['coa_id' => $allocation['coa_id'], 'nama_akun' => $allocation['nama_akun'], 'jumlah' => $allocation['subtotal']],
                    ['coa_id' => 1, 'nama_akun' => 'Bank Lama', 'jumlah' => $allocation['subtotal']],
                    '2026-11-12', 'BANK-EDIT', 'Pembayaran Pranota BPJS'
                ));
            }
        });
    }

    private function controller(): PembayaranPranotaBpjsController
    {
        return new PembayaranPranotaBpjsController(new CoaTransactionService);
    }

    private function change(): bool
    {
        return Coa::withoutEvents(fn () => (new BpjsPaymentBankUpdater)->change($this->payment, 2, 1));
    }

    public function test_changing_bank_moves_split_credits_and_preserves_debits_totals_and_pranota(): void
    {
        $before = $this->payment->fresh()->toArray();
        $debits = DB::table('coa_transactions')->where('debit', '>', 0)->get()->toArray();
        $this->assertTrue($this->change());
        $this->assertEquals(1000, Coa::find(1)->saldo);
        $this->assertEquals(1899.50, Coa::find(2)->saldo);
        $this->assertSame(2, $this->payment->fresh()->akun_coa_id);
        $this->assertEquals($debits, DB::table('coa_transactions')->where('debit', '>', 0)->get()->toArray());
        $this->assertSame(3, CoaTransaction::where('coa_id', 2)->count());
        $this->assertSame(0, CoaTransaction::where('coa_id', 1)->count());
        $this->assertEquals(100.50, CoaTransaction::sum('debit'));
        $this->assertEquals(100.50, CoaTransaction::sum('kredit'));
        foreach (['bank_allocations', 'journal_allocations', 'total_pembayaran', 'nomor_accurate'] as $field) {
            $this->assertSame($before[$field], $this->payment->fresh()->toArray()[$field]);
        }
        $this->assertSame('paid', PranotaBpjsHeader::first()->status);
        $this->assertEquals(100.50, $this->payment->pranotas()->first()->pivot->subtotal);
        $html = $this->controller()->print($this->payment->fresh())->render();
        $this->assertStringContainsString('Bank Baru', $html);
    }

    public function test_bank_only_update_preserves_accurate_and_accurate_only_update_preserves_bank(): void
    {
        Coa::withoutEvents(fn () => $this->controller()->update(Request::create('/', 'PATCH', [
            'akun_coa_id' => 2, 'expected_akun_coa_id' => 1,
        ]), $this->payment));
        $this->assertSame('KEEP-ACCURATE', $this->payment->fresh()->nomor_accurate);
        $this->controller()->update(Request::create('/', 'PATCH', ['nomor_accurate' => 'NEW']), $this->payment->fresh());
        $this->assertSame(2, $this->payment->fresh()->akun_coa_id);
        $this->assertSame('NEW', $this->payment->fresh()->nomor_accurate);
        $this->assertEquals(1899.50, Coa::find(2)->saldo);
    }

    public function test_same_bank_is_a_no_op_and_stale_edits_are_rejected(): void
    {
        $before = DB::table('coa_transactions')->get()->toArray();
        $this->assertFalse((new BpjsPaymentBankUpdater)->change($this->payment, 1, 1));
        $this->assertEquals($before, DB::table('coa_transactions')->get()->toArray());
        $this->change();
        $this->expectException(ValidationException::class);
        (new BpjsPaymentBankUpdater)->change($this->payment, 1, 1);
    }

    public function test_debit_accounts_and_missing_bank_ids_are_rejected_without_balance_changes(): void
    {
        foreach ([3, 4, 5, 999] as $bankId) {
            try {
                Coa::withoutEvents(fn () => (new BpjsPaymentBankUpdater)->change($this->payment, $bankId, 1));
                $this->fail('Invalid bank should be rejected.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('akun_coa_id', $e->errors());
            }
            $this->assertEquals(899.50, Coa::find(1)->saldo);
            $this->assertEquals(2000, Coa::find(2)->saldo);
            $this->assertSame(1, $this->payment->fresh()->akun_coa_id);
        }
    }

    public function test_incomplete_journal_is_rejected(): void
    {
        DB::table('coa_transactions')->where('kredit', '>', 0)->limit(1)->delete();
        try {
            $this->change();
            $this->fail('Incomplete journals should be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('akun_coa_id', $e->errors());
        }
        $this->assertSame(1, $this->payment->fresh()->akun_coa_id);
        $this->assertEquals(899.50, Coa::find(1)->saldo);
    }

    public function test_update_validates_bank_and_requires_original_bank_to_prevent_stale_saves(): void
    {
        foreach ([['akun_coa_id' => 999, 'expected_akun_coa_id' => 1], ['akun_coa_id' => 2]] as $payload) {
            try {
                $this->controller()->update(Request::create('/', 'PATCH', $payload), $this->payment);
                $this->fail('Invalid request should be rejected.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
            $this->assertSame(1, $this->payment->fresh()->akun_coa_id);
            $this->assertEquals(899.50, Coa::find(1)->saldo);
            $this->assertEquals(2000, Coa::find(2)->saldo);
        }
    }

    public function test_edit_preserves_journals_for_other_payments(): void
    {
        Coa::withoutEvents(fn () => (new CoaTransactionService)->recordDoubleEntry(
            ['coa_id' => 3, 'nama_akun' => 'Biaya BPJS Jakarta', 'jumlah' => 7.25],
            ['coa_id' => 2, 'nama_akun' => 'Bank Baru', 'jumlah' => 7.25],
            '2026-11-12', 'OTHER-PAYMENT', 'Pembayaran Pranota BPJS'
        ));
        $before = CoaTransaction::where('nomor_referensi', 'OTHER-PAYMENT')->get()->toArray();
        $this->change();
        $this->assertSame($before, CoaTransaction::where('nomor_referensi', 'OTHER-PAYMENT')->get()->toArray());
        $this->assertEquals(1892.25, Coa::find(2)->saldo);
        $this->assertEquals(1000, Coa::find(1)->saldo);
    }

    public function test_failed_save_rolls_back_account_balances_and_all_journal_changes(): void
    {
        DB::unprepared("CREATE TRIGGER fail_payment_update BEFORE UPDATE ON pembayaran_pranota_bpjs BEGIN SELECT RAISE(ABORT, 'Simulated save failure'); END");
        $before = DB::table('coa_transactions')->get()->toArray();
        try {
            $this->change();
            $this->fail('Save must fail.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Simulated save failure', $e->getMessage());
        }
        $this->assertEquals($before, DB::table('coa_transactions')->get()->toArray());
        $this->assertEquals(899.50, Coa::find(1)->saldo);
        $this->assertEquals(2000, Coa::find(2)->saldo);
        $this->assertSame(1, $this->payment->fresh()->akun_coa_id);
    }

    public function test_withdrawal_after_bank_edit_reverses_new_bank_and_keeps_old_bank_balance(): void
    {
        $this->change();
        Coa::withoutEvents(fn () => $this->controller()->destroy($this->payment->fresh()));
        $this->assertNull($this->payment->fresh());
        $this->assertEquals(1000, Coa::find(1)->saldo);
        $this->assertEquals(2000, Coa::find(2)->saldo);
        $this->assertSame(0, CoaTransaction::count());
        $this->assertSame('unpaid', PranotaBpjsHeader::first()->status);
    }
}

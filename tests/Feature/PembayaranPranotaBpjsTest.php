<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaBpjsController;
use App\Http\Controllers\PranotaBpjsController;
use App\Models\Coa;
use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsHeader;
use App\Services\CoaTransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PembayaranPranotaBpjsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_08_28_112737_create_pranota_bpjs_headers_table.php'))->up();
        Schema::table('pranota_bpjs_headers', function (Blueprint $table) {
            $table->json('snapshot_rumus')->nullable();
        });
        (require database_path('migrations/2026_08_28_112738_create_pranota_bpjs_details_table.php'))->up();
        Schema::table('pranota_bpjs_details', function (Blueprint $table) {
            $table->string('tipe_karyawan')->nullable();
            $table->json('snapshot_bpjs')->nullable();
        });
        (require database_path('migrations/2026_11_03_100000_create_pembayaran_pranota_bpjs_tables.php'))->up();
        (require database_path('migrations/2026_11_03_110000_allow_split_bpjs_payments.php'))->up();
        (require database_path('migrations/2026_11_05_130000_add_bank_allocations_to_pembayaran_pranota_bpjs.php'))->up();
        (require database_path('migrations/2025_09_30_141213_create_nomor_terakhir_table.php'))->up();
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
            ['id' => 1, 'nomor_akun' => '100', 'nama_akun' => 'Bank Uji', 'tipe_akun' => 'Bank', 'saldo' => 1000000],
            ['id' => 2, 'nomor_akun' => '200', 'nama_akun' => 'Hutang BPJS', 'tipe_akun' => 'Hutang', 'saldo' => -1000000],
        ]);
    }

    private function pranota(string $status = 'draft', float $total = 123456.78): PranotaBpjsHeader
    {
        return PranotaBpjsHeader::create([
            'nomor_pranota' => 'BPJS-'.(PranotaBpjsHeader::count() + 1),
            'tanggal_pranota' => '2026-11-01', 'periode_bulan' => 11,
            'periode_tahun' => 2026, 'status' => $status, 'grand_total' => $total,
        ]);
    }

    private function payload(array $ids): array
    {
        return ['pranota_ids' => $ids, 'tanggal_pembayaran' => '2026-11-03', 'bank_coa_id' => 1, 'akun_coa_id' => 2, 'total_pembayaran' => 1];
    }

    private function pay(array $data, ?CoaTransactionService $journal = null)
    {
        // Audit logging is independent of payment accounting and needs the full application schema.
        return Coa::withoutEvents(fn () => (new PembayaranPranotaBpjsController($journal ?? new CoaTransactionService))->store(Request::create('/', 'POST', $data)));
    }

    public function test_payment_uses_server_totals_posts_balanced_journal_and_locks_pranotas(): void
    {
        $first = $this->pranota();
        $second = $this->pranota('approved', 200000.25);
        $this->pay($this->payload([$first->id, $second->id]));
        $payment = PembayaranPranotaBpjs::firstOrFail();
        $this->assertSame('323457.03', $payment->total_pembayaran);
        $this->assertSame(2, $payment->pranotas()->count());
        $this->assertSame('paid', $first->fresh()->status);
        $this->assertSame('paid', $second->fresh()->status);
        $this->assertEquals(323457.03, DB::table('coa_transactions')->sum('debit'));
        $this->assertEquals(323457.03, DB::table('coa_transactions')->sum('kredit'));
        $this->assertEquals(1323457.03, Coa::find(1)->saldo);
        $this->assertEquals(-1323457.03, Coa::find(2)->saldo);
        $this->assertTrue((new PembayaranPranotaBpjsController(new CoaTransactionService))->create(Request::create('/'))->isRedirect());
        $locked = $first->fresh();
        $controller = new PranotaBpjsController;
        $controller->update(Request::create('/', 'PUT'), $locked);
        $controller->destroy($locked);
        $this->assertNotNull($first->fresh());
        $this->assertSame('323457.03', $payment->fresh()->total_pembayaran);
        $this->expectException(ValidationException::class);
        $this->pay($this->payload([$first->id]));
    }

    public function test_journal_failure_rolls_back_payment_status_and_sequence(): void
    {
        $pranota = $this->pranota();
        $journal = $this->mock(CoaTransactionService::class);
        $journal->shouldReceive('recordDoubleEntry')->once()->andReturn(false);
        $this->pay($this->payload([$pranota->id]), $journal);
        $this->assertSame(0, PembayaranPranotaBpjs::count());
        $this->assertSame(0, DB::table('pembayaran_pranota_bpjs_items')->count());
        $this->assertSame('draft', $pranota->fresh()->status);
        $this->assertSame(0, DB::table('nomor_terakhir')->count());
        $this->assertSame(0, DB::table('coa_transactions')->count());
    }

    public function test_invalid_pranota_and_account_inputs_are_rejected(): void
    {
        $draft = $this->pranota();
        $cancelled = $this->pranota('cancelled');
        $zero = $this->pranota('draft', 0);
        $deleted = $this->pranota();
        $deleted->delete();
        foreach ([
            $this->payload([$draft->id, $draft->id]),
            $this->payload([$cancelled->id]),
            $this->payload([$zero->id]),
            $this->payload([$deleted->id]),
            array_replace($this->payload([$draft->id]), ['akun_coa_id' => 1]),
            array_replace($this->payload([$draft->id]), ['bank_coa_id' => 999, 'akun_coa_id' => 1]),
            array_replace($this->payload([$draft->id]), ['tanggal_pembayaran' => 'bukan-tanggal']),
        ] as $payload) {
            try {
                $this->pay($payload);
                $this->fail('Input tidak valid harus ditolak.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $this->assertSame(0, PembayaranPranotaBpjs::count());
        $this->assertSame(0, DB::table('coa_transactions')->count());
    }

    public function test_backdated_payment_uses_selected_date_for_payment_and_journal(): void
    {
        $pranota = $this->pranota();
        $this->pay(array_replace($this->payload([$pranota->id]), ['tanggal_pembayaran' => '2026-10-01']));
        $payment = PembayaranPranotaBpjs::firstOrFail();
        $this->assertSame('2026-10-01', $payment->tanggal_pembayaran->format('Y-m-d'));
        $entries = DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor_pembayaran)->get();
        $this->assertCount(2, $entries);
        foreach ($entries as $entry) {
            $this->assertSame('2026-10-01', substr($entry->tanggal_transaksi, 0, 10));
        }
    }

    public function test_both_account_fields_accept_accounts_from_coa(): void
    {
        $pranota = $this->pranota();
        $this->pay(array_replace($this->payload([$pranota->id]), ['bank_coa_id' => 2, 'akun_coa_id' => 1]));
        $payment = PembayaranPranotaBpjs::firstOrFail();
        $this->assertEquals(2, $payment->bank_coa_id);
        $this->assertEquals(1, $payment->akun_coa_id);
        $this->assertEquals($pranota->grand_total, $payment->total_pembayaran);
    }

    public function test_types_can_be_paid_separately_without_duplicate_components(): void
    {
        $pranota = $this->pranota('draft', 600);
        foreach ([['Crew', 'Crew', 100, 200], ['LAPANGAN', 'LAPANGAN', 50, 250]] as [$jknType, $jamType, $jkn, $jam]) {
            DB::table('pranota_bpjs_details')->insert([
                'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 1,
                'snapshot_bpjs' => json_encode(['karyawan' => ['nik' => '001', 'nama_lengkap' => 'SUPIR UJI', 'tipe_jkn' => $jknType, 'tipe_bp_jamsostek' => $jamType]]),
                'bpjs_kesehatan' => $jkn * 0.8, 'bpjs_ketenagakerjaan' => $jkn * 0.2, 'total' => $jkn + $jam,
            ]);
        }
        DB::table('akun_coa')->insert([
            ['id' => 3, 'nomor_akun' => '5501.001', 'nama_akun' => 'Biaya Gaji ABK', 'tipe_akun' => 'Biaya', 'saldo' => 0],
            ['id' => 4, 'nomor_akun' => '6201.002', 'nama_akun' => 'Biaya Upah Harian Batam', 'tipe_akun' => 'Biaya', 'saldo' => 0],
        ]);
        $service = new \App\Services\BpjsPaymentParts;
        $groups = $service->groups($pranota);
        $this->assertCount(4, $groups);
        $first = array_key_first($groups);
        $this->assertSame(100.0, $groups[$first]['amount']);
        $this->assertSame('001', $groups[$first]['employees'][0]['nik']);
        $this->assertSame('SUPIR UJI', $groups[$first]['employees'][0]['nama']);
        $this->assertSame(100.0, $groups[$first]['employees'][0]['amount']);
        $this->assertNotSame($groups[$first]['selection_key'], array_values($groups)[1]['selection_key']);
        try {
            $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$first], 'bpjs_group' => 'wrong-group']);
            $this->fail('Group berbeda harus ditolak.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('group_keys', $e->errors());
        }
        $journal = $this->mock(CoaTransactionService::class);
        $journal->shouldReceive('recordDoubleEntry')->once()->andReturn(false);
        $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$first]], $journal);
        $this->assertSame('draft', $pranota->fresh()->status);
        $this->assertSame(0, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertSame(0, PembayaranPranotaBpjs::count());
        $this->assertCount(4, $service->groups($pranota->fresh()));
        $automaticPayload = array_diff_key($this->payload([$pranota->id]), ['bank_coa_id' => true]);
        $this->pay($automaticPayload + ['group_keys' => [$first], 'bpjs_group' => $groups[$first]['selection_key']]);
        $this->assertSame('partial', $pranota->fresh()->status);
        $this->assertEquals(100, PembayaranPranotaBpjs::first()->total_pembayaran);
        $this->assertCount(3, $service->groups($pranota->fresh()));
        try {
            $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$first]]);
            $this->fail('Bagian yang sama tidak boleh dibayar dua kali.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('group_keys', $e->errors());
        }
        $remaining = array_keys($service->groups($pranota->fresh()));
        foreach ($remaining as $key) {
            $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$key]]);
        }
        $this->assertSame('paid', $pranota->fresh()->status);
        $this->assertEquals(600, PembayaranPranotaBpjs::sum('total_pembayaran'));
        $this->assertSame(4, $pranota->pembayaran()->count());
        $this->assertEquals(3, PembayaranPranotaBpjs::first()->bank_coa_id);
        $this->assertSame(4, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertEquals(600, DB::table('coa_transactions')->sum('debit'));
        $this->assertEquals(600, DB::table('coa_transactions')->sum('kredit'));
        $this->assertSame([], $service->groups($pranota->fresh()));
    }

    public function test_payment_branch_prioritizes_current_employee_and_falls_back_to_snapshot(): void
    {
        $pranota = $this->pranota('draft', 100);
        $id = DB::table('pranota_bpjs_details')->insertGetId([
            'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 235,
            'snapshot_bpjs' => json_encode(['karyawan' => [
                'nik' => '0133', 'nama_lengkap' => 'A HOANG', 'cabang_bpjs' => null,
                'group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'LAPANGAN',
            ]]),
            'bpjs_kesehatan' => 80, 'bpjs_ketenagakerjaan' => 20, 'total' => 100,
        ]);
        $detail = \App\Models\PranotaBpjsDetail::findOrFail($id);
        $employee = (new \App\Models\Karyawan)->forceFill(['cabang_bpjs' => 'BTM']);
        $detail->setRelation('karyawan', $employee);
        $pranota->setRelation('details', collect([$detail]));
        $service = new \App\Services\BpjsPaymentParts;
        $groups = array_values($service->groups($pranota));
        $this->assertSame('BTM', $groups[0]['cabang_bpjs']);
        $this->assertSame(100.0, $groups[0]['amount']);
        $this->assertNull($detail->fresh()->snapshot_bpjs['karyawan']['cabang_bpjs']);

        $snapshot = $detail->snapshot_bpjs;
        $snapshot['karyawan']['cabang_bpjs'] = 'PLUIT';
        $detail->snapshot_bpjs = $snapshot;
        $employee->cabang_bpjs = 'JKT';
        $current = array_values($service->groups($pranota))[0];
        $this->assertSame('JKT', $current['cabang_bpjs']);
        $this->assertSame('JKT', $current['employees'][0]['cabang_bpjs']);
        $this->assertSame(100.0, $current['amount']);
        $employee->cabang_bpjs = '';
        $this->assertSame('PLUIT', array_values($service->groups($pranota))[0]['employees'][0]['cabang_bpjs']);
        $detail->setRelation('karyawan', null);
        $this->assertSame('PLUIT', array_values($service->groups($pranota))[0]['employees'][0]['cabang_bpjs']);
        $snapshot['karyawan']['cabang_bpjs'] = '  ';
        $detail->snapshot_bpjs = $snapshot;
        $employee->cabang_bpjs = '';
        $this->assertSame('Tanpa cabang', array_values($service->groups($pranota))[0]['cabang_bpjs']);
    }

    public function test_same_type_is_combined_across_case_spacing_and_branches(): void
    {
        $pranota = $this->pranota('draft', 300);
        foreach ([[' Lapangan ', 'BTM', 100], ['LAPANGAN', 'PLUIT', 200]] as [$type, $branch, $amount]) {
            DB::table('pranota_bpjs_details')->insert([
                'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 1,
                'snapshot_bpjs' => json_encode(['karyawan' => [
                    'nik' => '001', 'nama_lengkap' => 'KARYAWAN UJI',
                    'group_jkn' => 'JKN-KIS', 'tipe_jkn' => $type, 'cabang_bpjs' => $branch,
                ]]),
                'bpjs_kesehatan' => $amount, 'total' => $amount,
            ]);
        }
        DB::table('akun_coa')->insert(['id' => 4, 'nomor_akun' => '6201.002', 'nama_akun' => 'Biaya Upah Harian Batam', 'tipe_akun' => 'Biaya', 'saldo' => 0]);
        $service = new \App\Services\BpjsPaymentParts;
        $groups = $service->groups($pranota);
        $this->assertCount(1, $groups);
        $group = reset($groups);
        $this->assertSame('LAPANGAN', $group['tipe']);
        $this->assertSame(300.0, $group['amount']);
        $this->assertCount(2, $group['employees']);
        $this->assertCount(2, $group['parts']);
        $this->pay(['pranota_ids' => [$pranota->id], 'tanggal_pembayaran' => '2026-11-03',
            'akun_coa_id' => 1, 'group_keys' => [$group['key']], 'bpjs_group' => $group['selection_key']]);
        $this->assertSame('300.00', PembayaranPranotaBpjs::firstOrFail()->total_pembayaran);
        $this->assertSame(2, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertSame([], $service->groups($pranota->fresh()));
    }

    public function test_individual_employees_can_be_paid_without_settling_the_other_employees(): void
    {
        $pranota = $this->pranota('draft', 300);
        foreach ([['001', 100], ['002', 200]] as [$nik, $amount]) {
            DB::table('pranota_bpjs_details')->insert([
                'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 1,
                'snapshot_bpjs' => json_encode(['karyawan' => [
                    'nik' => $nik, 'nama_lengkap' => 'KARYAWAN '.$nik, 'cabang_bpjs' => 'BTM',
                    'group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'LAPANGAN',
                ]]), 'bpjs_kesehatan' => $amount, 'total' => $amount,
            ]);
        }
        DB::table('akun_coa')->insert(['id' => 4, 'nomor_akun' => '6201.002', 'nama_akun' => 'Biaya Upah Harian Batam', 'tipe_akun' => 'Biaya', 'saldo' => 0]);
        $service = new \App\Services\BpjsPaymentParts;
        $groups = $service->groups($pranota);
        $group = reset($groups);
        $this->assertSame('BTM', $group['employees'][0]['cabang_bpjs']);
        $payload = ['pranota_ids' => [$pranota->id], 'tanggal_pembayaran' => '2026-11-03', 'akun_coa_id' => 1,
            'select_employees' => 1, 'bpjs_group' => $group['selection_key'], 'group_keys' => [$group['key']]];
        foreach ([[], ['employee_keys' => ['999999:jkn']]] as $invalid) {
            try {
                $this->pay($payload + $invalid);
                $this->fail('Employee selection must be valid and nonempty.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('employee_keys', $e->errors());
            }
        }
        $selection = ['employee_keys' => [$group['employees'][0]['key']]];
        $this->pay($payload + $selection);
        $this->assertSame('100.00', PembayaranPranotaBpjs::firstOrFail()->total_pembayaran);
        $this->assertEquals(100, PembayaranPranotaBpjs::first()->bank_allocations[0]['subtotal']);
        $this->assertSame('partial', $pranota->fresh()->status);
        $remaining = array_values($service->groups($pranota->fresh()));
        $this->assertSame(200.0, $remaining[0]['amount']);
        $this->assertCount(1, $remaining[0]['employees']);
        $this->assertSame('002', $remaining[0]['employees'][0]['nik']);
        try {
            $this->pay($payload + $selection);
            $this->fail('An employee component must not be paid twice.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('employee_keys', $e->errors());
        }
        $this->assertSame(1, PembayaranPranotaBpjs::count());
    }

    public function test_journal_account_mapping_and_selection_validation(): void
    {
        DB::table('akun_coa')->insert([
            ['id' => 3, 'nomor_akun' => '5501.001', 'nama_akun' => 'Biaya Gaji ABK', 'tipe_akun' => 'Biaya', 'saldo' => 0],
            ['id' => 4, 'nomor_akun' => '6201.002', 'nama_akun' => 'Biaya Upah Harian Batam', 'tipe_akun' => 'Biaya', 'saldo' => 0],
        ]);
        $resolver = new \App\Services\BpjsJournalAccount;
        $this->assertEquals(3, $resolver->resolve('BPU-CREW')->id);
        $this->assertEquals(3, $resolver->resolve('', 'BPU-CREW')->id);
        $this->assertEquals(4, $resolver->resolve('lapangan')->id);
        $this->assertNull($resolver->resolve('Unknown'));
        $this->assertNull($resolver->resolve('Kantor'));
        $this->assertSame(3, $resolver->selectedId([['credit_id' => 3], ['credit_id' => 3]]));
        foreach ([[['credit_id' => null]], [['credit_id' => 3], ['credit_id' => 4]]] as $groups) {
            try {
                $resolver->selectedId($groups);
                $this->fail('Pemetaan akun tidak valid harus ditolak.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('group_keys', $e->errors());
            }
        }
    }

    public function test_multiple_automatic_accounts_share_one_payment_bank_and_roll_back_together(): void
    {
        $pranota = $this->pranota('draft', 300);
        foreach ([['Crew', 100], ['LAPANGAN', 200]] as [$type, $amount]) {
            DB::table('pranota_bpjs_details')->insert([
                'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 1,
                'snapshot_bpjs' => json_encode(['karyawan' => ['group_jkn' => 'JKN-KIS', 'tipe_jkn' => $type]]),
                'bpjs_kesehatan' => $amount, 'total' => $amount,
            ]);
        }
        DB::table('akun_coa')->insert([
            ['id' => 3, 'nomor_akun' => '5501.001', 'nama_akun' => 'Biaya Gaji ABK', 'tipe_akun' => 'Biaya', 'saldo' => 0],
            ['id' => 4, 'nomor_akun' => '6201.002', 'nama_akun' => 'Biaya Upah Harian Batam', 'tipe_akun' => 'Biaya', 'saldo' => 0],
        ]);
        $groups = (new \App\Services\BpjsPaymentParts)->groups($pranota);
        $payload = ['pranota_ids' => [$pranota->id], 'tanggal_pembayaran' => '2026-11-03', 'akun_coa_id' => 1,
            'group_keys' => array_keys($groups), 'bpjs_group' => reset($groups)['selection_key']];
        $journal = $this->mock(CoaTransactionService::class);
        $journal->shouldReceive('recordDoubleEntry')->once()->ordered()->andReturn(true);
        $journal->shouldReceive('recordDoubleEntry')->once()->ordered()->andReturn(false);
        $this->pay($payload, $journal);
        $this->assertSame(0, PembayaranPranotaBpjs::count());
        $this->assertSame(0, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertSame('draft', $pranota->fresh()->status);
        $this->assertEquals(1000000, Coa::find(1)->saldo);
        $this->pay($payload);
        $payment = PembayaranPranotaBpjs::firstOrFail();
        $this->assertCount(2, $payment->bank_allocations);
        $this->assertSame('300.00', $payment->total_pembayaran);
        $this->assertEquals(300, array_sum(array_column($payment->bank_allocations, 'subtotal')));
        $this->assertEquals(100, DB::table('coa_transactions')->where('coa_id', 3)->sum('debit'));
        $this->assertEquals(200, DB::table('coa_transactions')->where('coa_id', 4)->sum('debit'));
        $this->assertEquals(300, DB::table('coa_transactions')->where('coa_id', 1)->sum('kredit'));
        $this->assertEquals(100, Coa::find(3)->saldo);
        $this->assertEquals(200, Coa::find(4)->saldo);
        $this->assertEquals(999700, Coa::find(1)->saldo);
        $this->assertSame('paid', $pranota->fresh()->status);
    }

    public function test_multiple_types_with_one_account_combine_their_amounts(): void
    {
        $resolver = new \App\Services\BpjsJournalAccount;
        $this->assertEquals([3 => 100.50, 4 => 200], $resolver->selectedAmounts([
            ['credit_id' => 3, 'amount' => 75.25], ['credit_id' => 3, 'amount' => 25.25],
            ['credit_id' => 4, 'amount' => 200],
        ]));
        $this->expectException(ValidationException::class);
        $resolver->selectedAmounts([['credit_id' => null, 'amount' => 100]]);
    }

    public function test_show_route_binds_payment_and_endpoints_require_permissions(): void
    {
        $this->pay($this->payload([$this->pranota()->id]));
        $request = Request::create('/pembayaran-pranota-bpjs/1');
        $route = app('router')->getRoutes()->match($request);
        $this->assertSame('pembayaran-pranota-bpjs.show', $route->getName());
        $this->assertSame('1', $route->parameter('pembayaran_pranota_bpj'));
        $controller = new PembayaranPranotaBpjsController(new CoaTransactionService);
        $middleware = collect($controller->getMiddleware())->pluck('middleware')->all();
        $this->assertContains('can:pembayaran-pranota-bpjs-view', $middleware);
        $this->assertContains('can:pembayaran-pranota-bpjs-create', $middleware);
    }
}

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
        $this->assertEquals(676542.97, Coa::find(1)->saldo);
        $this->assertEquals(-676542.97, Coa::find(2)->saldo);
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
            array_replace($this->payload([$draft->id]), ['bank_coa_id' => 2, 'akun_coa_id' => 1]),
            array_replace($this->payload([$draft->id]), ['tanggal_pembayaran' => '2026-10-01']),
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

    public function test_types_can_be_paid_separately_without_duplicate_components(): void
    {
        $pranota = $this->pranota('draft', 600);
        foreach ([['KARY', 'PPU', 100, 200], ['NON KARY', 'BPU', 50, 250]] as [$jknType, $jamType, $jkn, $jam]) {
            DB::table('pranota_bpjs_details')->insert([
                'pranota_bpjs_header_id' => $pranota->id, 'karyawan_id' => 1,
                'snapshot_bpjs' => json_encode(['karyawan' => ['tipe_jkn' => $jknType, 'tipe_bp_jamsostek' => $jamType]]),
                'bpjs_kesehatan' => $jkn * 0.8, 'bpjs_ketenagakerjaan' => $jkn * 0.2, 'total' => $jkn + $jam,
            ]);
        }
        $service = new \App\Services\BpjsPaymentParts;
        $groups = $service->groups($pranota);
        $this->assertCount(4, $groups);
        $first = array_key_first($groups);
        $this->assertSame(100.0, $groups[$first]['amount']);
        $journal = $this->mock(CoaTransactionService::class);
        $journal->shouldReceive('recordDoubleEntry')->once()->andReturn(false);
        $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$first]], $journal);
        $this->assertSame('draft', $pranota->fresh()->status);
        $this->assertSame(0, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertSame(0, PembayaranPranotaBpjs::count());
        $this->assertCount(4, $service->groups($pranota->fresh()));
        $this->pay($this->payload([$pranota->id]) + ['group_keys' => [$first]]);
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
        $this->pay($this->payload([$pranota->id]) + ['group_keys' => $remaining]);
        $this->assertSame('paid', $pranota->fresh()->status);
        $this->assertEquals(600, PembayaranPranotaBpjs::sum('total_pembayaran'));
        $this->assertSame(2, $pranota->pembayaran()->count());
        $this->assertSame(4, DB::table('pembayaran_pranota_bpjs_parts')->count());
        $this->assertEquals(600, DB::table('coa_transactions')->sum('debit'));
        $this->assertEquals(600, DB::table('coa_transactions')->sum('kredit'));
        $this->assertSame([], $service->groups($pranota->fresh()));
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

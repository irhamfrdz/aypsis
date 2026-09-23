<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranBiayaKapalController;
use App\Http\Controllers\BiayaKapalController;
use App\Models\BiayaKapal;
use App\Models\PembayaranBiayaKapal;
use App\Services\CoaTransactionService;
use App\Services\TemasPaymentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TemasPaymentTest extends TestCase
{
    private TemasPaymentService $service;

    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        // Isolated in-memory schema: never migrate or clear the configured application DB.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->nullable();
            $table->date('tanggal')->nullable();
            $table->json('nama_kapal')->nullable();
            $table->json('no_voyage')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->nullable();
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
        (require database_path('migrations/2026_04_27_142615_create_biaya_kapal_temas_table.php'))->up();
        (require database_path('migrations/2026_06_12_100757_add_container_fields_to_biaya_kapal_temas_table.php'))->up();
        (require database_path('migrations/2026_09_23_000001_add_nomor_bl_to_biaya_kapal_temas_table.php'))->up();
        (require database_path('migrations/2026_09_23_000002_add_is_per_container_to_biaya_kapal_temas_table.php'))->up();
        Schema::table('biaya_kapal_temas', fn (Blueprint $table) => $table->decimal('biaya_admin', 15, 2)->default(0));
        (require database_path('migrations/2026_09_22_130000_create_biaya_kapal_temas_stages.php'))->up();
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('no_voyage')->nullable();
            $table->string('nomor_bl')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
        });
        Schema::create('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_pembayaran', 'nomor_accurate', 'tanggal_pembayaran', 'bank', 'jenis_transaksi', 'alasan_penyesuaian', 'keterangan', 'status_pembayaran'] as $field) {
                $table->string($field)->nullable();
            }
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_biaya_kapal_id');
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            $table->unique(['pembayaran_biaya_kapal_id', 'biaya_kapal_id']);
        });
        Schema::create('nomor_terakhir', function (Blueprint $table) {
            $table->id();
            $table->string('modul')->unique();
            $table->integer('nomor_terakhir')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_09_22_120000_add_temas_payment_stages_to_pembayaran_biaya_kapal_items.php'))->up();
        $this->service = app(TemasPaymentService::class);
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    private function invoice(): BiayaKapal
    {
        // Stale header total must not override the actual container costs.
        $invoice = BiayaKapal::create(['nominal' => 999, 'total_biaya' => 999, 'status_pembayaran' => 'pending']);
        DB::table('biaya_kapal_temas')->insert([
            ['biaya_kapal_id' => $invoice->id, 'nomor_kontainer' => 'TEMU1', 'grand_total' => 600000],
            ['biaya_kapal_id' => $invoice->id, 'nomor_kontainer' => 'TEMU2', 'grand_total' => 400000],
        ]);

        return $invoice;
    }

    private function pay(BiayaKapal $invoice, string $mode, $amount = null, ?int $dpId = null): PembayaranBiayaKapal
    {
        return DB::transaction(function () use ($invoice, $mode, $amount, $dpId) {
            $locked = BiayaKapal::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $item = $this->service->prepare($locked, $mode, $amount, $dpId, '2026-09-22');
            $payment = PembayaranBiayaKapal::create([
                'nomor_pembayaran' => 'TEST', 'tanggal_pembayaran' => '2026-09-22',
                'status_pembayaran' => 'paid', 'total_pembayaran' => $item['nominal'],
            ]);
            $payment->items()->create(array_merge($item, ['biaya_kapal_id' => $invoice->id]));
            $this->service->syncInvoice($locked);

            return $payment;
        });
    }

    public function test_dp_settlement_and_cancellation_recompute_balance_and_keep_history(): void
    {
        $invoice = $this->invoice();
        $dp = $this->pay($invoice, 'dp', '300000.25');
        $this->assertSame('699999.75', $invoice->fresh()->sisa_pembayaran);
        $this->assertSame('pending', $invoice->fresh()->status_pembayaran);
        $dpId = $dp->items()->first()->id;
        $settlement = $this->pay($invoice, 'pelunasan_dp', null, $dpId);
        $this->assertSame('699999.75', $settlement->items()->first()->nominal);
        $this->assertSame('paid', $invoice->fresh()->status_pembayaran);
        $this->assertSame('0.00', $invoice->fresh()->sisa_pembayaran);
        $this->assertCount(2, $this->service->summary($invoice->fresh())['riwayat']);

        $settlement->delete();
        $this->service->syncInvoice($invoice->fresh());
        $this->assertSame('699999.75', $invoice->fresh()->sisa_pembayaran);
        $this->service->assertPaymentCanBeCancelled($dp);
        $replacement = $this->pay($invoice, 'pelunasan_dp', null, $dpId);
        $replacement->delete();
        $dp->delete();
        $this->service->syncInvoice($invoice->fresh());
        $this->assertSame('1000000.00', $invoice->fresh()->sisa_pembayaran);
        $this->assertSame('0.00', $invoice->fresh()->dp);
        $this->assertSame(3, DB::table('pembayaran_biaya_kapal_items')->count());
    }

    public function test_dp_must_be_positive_and_less_than_total(): void
    {
        $this->expectException(ValidationException::class);
        $this->pay($this->invoice(), 'dp', '1000000');
    }

    public function test_second_dp_is_rejected(): void
    {
        $invoice = $this->invoice();
        $this->pay($invoice, 'dp', 100000);
        $this->expectException(ValidationException::class);
        $this->pay($invoice, 'dp', 100000);
    }

    public function test_settlement_cannot_reference_another_invoice(): void
    {
        $dp = $this->pay($this->invoice(), 'dp', 100000);
        $this->expectException(ValidationException::class);
        $this->pay($this->invoice(), 'pelunasan_dp', null, $dp->items()->first()->id);
    }

    public function test_second_settlement_is_rejected(): void
    {
        $invoice = $this->invoice();
        $dp = $this->pay($invoice, 'dp', 100000);
        $dpId = $dp->items()->first()->id;
        $this->pay($invoice, 'pelunasan_dp', null, $dpId);
        $this->expectException(ValidationException::class);
        $this->pay($invoice, 'pelunasan_dp', null, $dpId);
    }

    public function test_dp_with_active_settlement_cannot_be_cancelled(): void
    {
        $invoice = $this->invoice();
        $dp = $this->pay($invoice, 'dp', 100000);
        $this->pay($invoice, 'pelunasan_dp', null, $dp->items()->first()->id);
        $this->expectException(ValidationException::class);
        $this->service->assertPaymentCanBeCancelled($dp);
    }

    public function test_invoice_with_dp_cannot_be_edited_or_deleted(): void
    {
        $invoice = $this->invoice();
        $this->pay($invoice, 'dp', 100000);
        $this->expectException(ValidationException::class);
        $this->service->assertInvoiceEditable($invoice);
    }

    public function test_existing_full_payments_are_included(): void
    {
        $invoice = $this->invoice();
        $payment = PembayaranBiayaKapal::create(['nomor_pembayaran' => 'OLD', 'tanggal_pembayaran' => '2026-09-01', 'status_pembayaran' => 'paid']);
        $payment->items()->create(['biaya_kapal_id' => $invoice->id, 'nominal' => 1000000]);
        $this->assertSame('lunas', $this->service->summary($invoice)['status']);
        $this->expectException(ValidationException::class);
        $this->pay($invoice, 'dp', 100000);
    }

    public function test_controller_rejects_tampered_payment_total_without_writing(): void
    {
        $invoice = $this->invoice();
        $controller = new PembayaranBiayaKapalController($this->mock(CoaTransactionService::class));
        try {
            $controller->store(Request::create('/', 'POST', [
                'biaya_kapal_ids' => [$invoice->id], 'tanggal_pembayaran' => '2026-09-22',
                'jenis_transaksi' => 'kredit', 'payment_mode' => 'dp',
                'nominal_dp' => 300000, 'total_pembayaran' => 1,
            ]));
            $this->fail('Expected a validation error.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('total_pembayaran', $e->errors());
        }
        $this->assertSame(0, PembayaranBiayaKapal::count());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_controller_stores_dp_without_posting_coa(): void
    {
        $invoice = $this->invoice();
        $coa = $this->mock(CoaTransactionService::class);
        $coa->shouldNotReceive('pembayaranBiayaKapal');
        $coa->shouldNotReceive('deleteTransactionByReference');
        $controller = new PembayaranBiayaKapalController($coa);
        $controller->store(Request::create('/', 'POST', [
            'biaya_kapal_ids' => [$invoice->id], 'tanggal_pembayaran' => '2026-09-22', 'bank' => 'BANK TEST',
            'jenis_transaksi' => 'kredit', 'payment_mode' => 'dp',
            'nominal_dp' => 300000, 'total_pembayaran' => 300000,
        ]));
        $this->assertSame(1, PembayaranBiayaKapal::count());
        $this->assertSame('300000.00', $invoice->fresh()->dp);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_controller_can_pay_dp_settle_and_cancel_in_order(): void
    {
        $invoice = $this->invoice();
        $coa = $this->mock(CoaTransactionService::class);
        $coa->shouldNotReceive('pembayaranBiayaKapal');
        $coa->shouldNotReceive('deleteTransactionByReference');
        $controller = new PembayaranBiayaKapalController($coa);
        $common = [
            'biaya_kapal_ids' => [$invoice->id], 'tanggal_pembayaran' => '2026-09-22',
            'bank' => 'BANK TEST', 'jenis_transaksi' => 'kredit',
        ];
        $controller->store(Request::create('/', 'POST', $common + [
            'payment_mode' => 'dp', 'nominal_dp' => 300000, 'total_pembayaran' => 300000,
        ]));
        $dp = PembayaranBiayaKapal::firstOrFail();
        $this->assertSame('300000.00', $dp->total_pembayaran);
        $this->assertSame('pending', $invoice->fresh()->status_pembayaran);
        $controller->update(Request::create('/', 'PUT', $common + ['keterangan' => 'DP dikonfirmasi']), $dp->id);
        $this->assertSame('DP dikonfirmasi', $dp->fresh()->keterangan);
        $this->assertSame('300000.00', $dp->fresh()->total_pembayaran);
        $controller->store(Request::create('/', 'POST', $common + [
            'payment_mode' => 'pelunasan_dp', 'dp_item_id' => $dp->items()->first()->id,
            'total_pembayaran' => 700000,
        ]));
        $settlement = PembayaranBiayaKapal::orderByDesc('id')->firstOrFail();
        $this->assertSame('700000.00', $settlement->total_pembayaran);
        $this->assertSame('paid', $invoice->fresh()->status_pembayaran);
        $controller->destroy($settlement->id);
        $this->assertSame('700000.00', $invoice->fresh()->sisa_pembayaran);
        $controller->destroy($dp->id);
        $this->assertSame('1000000.00', $invoice->fresh()->sisa_pembayaran);
        $this->assertSame(2, DB::table('pembayaran_biaya_kapal_items')->count());
        $this->assertSame(0, PembayaranBiayaKapal::count());
    }

    public function test_full_payment_and_cancelled_dp_reference(): void
    {
        $invoice = $this->invoice();
        $full = $this->pay($invoice, 'lunas');
        $this->assertSame('1000000.00', $full->total_pembayaran);
        $this->assertSame('paid', $invoice->fresh()->status_pembayaran);
        $other = $this->invoice();
        $dp = $this->pay($other, 'dp', 300000);
        $dpId = $dp->items()->first()->id;
        $dp->delete();
        $this->service->syncInvoice($other->fresh());
        $this->expectException(ValidationException::class);
        $this->pay($other, 'pelunasan_dp', null, $dpId);
    }

    public function test_settlement_date_cannot_precede_dp(): void
    {
        $invoice = $this->invoice();
        $dp = $this->pay($invoice, 'dp', 300000);
        $this->expectException(ValidationException::class);
        $this->service->prepare($invoice->fresh(), 'pelunasan_dp', null, $dp->items()->first()->id, '2026-09-21');
    }

    public function test_manual_coa_sync_is_rejected_for_temas(): void
    {
        $invoice = $this->invoice();
        $dp = $this->pay($invoice, 'dp', 300000);
        $coa = $this->mock(CoaTransactionService::class);
        $coa->shouldNotReceive('pembayaranBiayaKapal');
        $coa->shouldNotReceive('deleteTransactionByReference');
        $this->expectException(ValidationException::class);
        (new PembayaranBiayaKapalController($coa))->syncCoa($dp->id);
    }

    public function test_temas_and_other_invoices_cannot_share_one_payment(): void
    {
        $temas = $this->invoice();
        $other = BiayaKapal::create(['nominal' => 100000, 'status_pembayaran' => 'pending']);
        $coa = $this->mock(CoaTransactionService::class);
        $coa->shouldNotReceive('pembayaranBiayaKapal');
        try {
            (new PembayaranBiayaKapalController($coa))->store(Request::create('/', 'POST', [
                'biaya_kapal_ids' => [$temas->id, $other->id], 'tanggal_pembayaran' => '2026-09-22',
                'bank' => 'BANK TEST', 'jenis_transaksi' => 'kredit', 'total_pembayaran' => 1100000,
            ]));
            $this->fail('Mixed invoices must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('biaya_kapal_ids', $e->errors());
        }
        $this->assertSame(0, PembayaranBiayaKapal::count());
    }

    private function finalCosts(): array
    {
        return [
            'kapal' => 'TEMAS 1', 'voyage' => 'V001', 'types' => ['MANUAL', 'MANUAL'],
            'manual_names' => ['Handling', 'Handling'], 'custom_prices' => [600000, 400000],
            'quantities' => [1, 1], 'nomor_kontainers' => ['TEMU001', 'TEMU002'],
            'nomor_bls' => ['BL001', 'BL002'],
            'size_items' => ['20ft', '40ft'],
        ];
    }

    public function test_storage_style_dp_has_no_final_invoice_and_settlement_deducts_it_once(): void
    {
        $billing = app(\App\Services\TemasBillingService::class);
        $dpInvoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $billing->replace($dpInvoice, [['kapal' => 'TEMAS 1', 'voyage' => 'V001', 'payment_mode' => 'dp', 'nominal_dibayar' => 300000]]);
        $dp = \App\Models\BiayaKapalTemasStage::firstOrFail();
        $this->assertSame('0.00', $dp->nilai_tagihan);
        $this->assertSame('300000.00', $dpInvoice->fresh()->nominal);
        $this->assertSame(1, $billing->candidates()->count());
        $settlement = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $section = $this->finalCosts() + ['payment_mode' => 'pelunasan_dp', 'dp_stage_id' => $dp->id];
        $section['kapal'] = 'IGNORED';
        $section['pph_active'] = 'on';
        $section['pph'] = 20000; // Storage-style settlement excludes these extras.
        $billing->replace($settlement, [$section]);
        $stage = \App\Models\BiayaKapalTemasStage::where('biaya_kapal_id', $settlement->id)->firstOrFail();
        $this->assertSame('TEMAS 1', $stage->kapal);
        $this->assertSame('1000000.00', $stage->nilai_tagihan);
        $this->assertSame('300000.00', $stage->dp_diperhitungkan);
        $this->assertSame('700000.00', $settlement->fresh()->nominal);
        $this->assertEquals(700000, $settlement->temasDetails()->sum('grand_total'));
        $this->assertSame(0, $billing->candidates()->count());
        $billing->replace($settlement, [$section]); // Re-editing settlement does not deduct DP again.
        $this->assertSame('700000.00', $settlement->fresh()->nominal);
        $settlement->delete();
        $this->assertSame(1, $billing->candidates()->count());
    }

    public function test_storage_style_direct_payment_includes_tax_and_extras(): void
    {
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        app(\App\Services\TemasBillingService::class)->replace($invoice, [$this->finalCosts() + [
            'payment_mode' => 'lunas', 'pph_active' => 'on', 'pph' => 20000, 'biaya_materai' => 10000,
        ]]);
        $this->assertSame('990000.00', $invoice->fresh()->nominal);
        $this->assertEquals(990000, $invoice->temasDetails()->sum('grand_total'));
        $this->assertSame(2, $invoice->temasDetails()->count());
    }

    public function test_child_bl_suffixes_are_stored_as_one_parent_bl(): void
    {
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $section = $this->finalCosts();
        $section['nomor_bls'] = ['01-1', '01-2'];

        app(\App\Services\TemasBillingService::class)->replace($invoice, [$section]);

        $this->assertSame(['01'], $invoice->temasDetails()->pluck('nomor_bl')->unique()->values()->all());
    }

    public function test_temas_costs_multiply_by_container_count_except_bl_only_charges(): void
    {
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        app(\App\Services\TemasBillingService::class)->replace($invoice, [[
            'kapal' => 'TEMAS 1',
            'voyage' => 'V001',
            'types' => ['MANUAL', 'MANUAL', 'MANUAL', 'MANUAL'],
            'manual_names' => ['THC', 'ADM DO', 'DO Kontainer', 'Materai'],
            'custom_prices' => [100000, 10000, 20000, 30000],
            'quantities' => [99, 99, 99, 99],
            'nomor_kontainers' => array_fill(0, 4, 'TEMU001, TEMU002, TEMU003'),
            'nomor_bls' => array_fill(0, 4, '01'),
            'size_items' => array_fill(0, 4, '20ft'),
        ]]);

        $details = $invoice->temasDetails()->get()->keyBy('jenis_biaya');
        $this->assertSame('3.00', $details['THC']->kuantitas);
        $this->assertSame('300000.00', $details['THC']->sub_total);
        foreach (['ADM DO', 'DO Kontainer', 'Materai'] as $singleCharge) {
            $this->assertSame('1.00', $details[$singleCharge]->kuantitas);
        }
        $this->assertSame('360000.00', $invoice->fresh()->nominal);
    }

    public function test_per_container_checkbox_overrides_the_default_cost_rule(): void
    {
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        app(\App\Services\TemasBillingService::class)->replace($invoice, [[
            'kapal' => 'TEMAS 1',
            'voyage' => 'V001',
            'types' => ['MANUAL', 'MANUAL'],
            'manual_names' => ['THC', 'ADM DO'],
            'custom_prices' => [100000, 10000],
            'quantities' => [1, 1],
            'per_containers' => [0, 1],
            'nomor_kontainers' => array_fill(0, 2, 'TEMU001, TEMU002, TEMU003'),
            'nomor_bls' => array_fill(0, 2, '01'),
            'size_items' => array_fill(0, 2, '20ft'),
        ]]);

        $details = $invoice->temasDetails()->get()->keyBy('jenis_biaya');
        $this->assertFalse($details['THC']->is_per_container);
        $this->assertSame('1.00', $details['THC']->kuantitas);
        $this->assertTrue($details['ADM DO']->is_per_container);
        $this->assertSame('3.00', $details['ADM DO']->kuantitas);
        $this->assertSame('130000.00', $invoice->fresh()->nominal);
    }

    public function test_temas_print_groups_child_bl_numbers_and_lists_all_containers(): void
    {
        $invoice = BiayaKapal::create([
            'nomor_invoice' => 'INV-TEMAS-01',
            'tanggal' => '2026-09-23',
            'status_pembayaran' => 'pending',
        ]);
        app(\App\Services\TemasBillingService::class)->replace($invoice, [[
            'kapal' => 'TEMAS 1',
            'voyage' => 'V001',
            'types' => ['MANUAL'],
            'manual_names' => ['Handling BL'],
            'custom_prices' => [1000000],
            'quantities' => [1],
            'nomor_kontainers' => ['TEMU001, TEMU002'],
            'nomor_bls' => ['01'],
            'size_items' => ['20ft'],
        ]]);
        DB::table('manifests')->insert([
            ['no_voyage' => 'V001', 'nomor_bl' => '01-1', 'nomor_kontainer' => 'TEMU001', 'size_kontainer' => '20'],
            ['no_voyage' => 'V001', 'nomor_bl' => '01-2', 'nomor_kontainer' => 'TEMU002', 'size_kontainer' => '20'],
        ]);

        $html = app(BiayaKapalController::class)->printTemas($invoice->fresh())->render();

        $this->assertStringContainsString('Detail Biaya per Nomor BL', $html);
        $this->assertStringContainsString('01-1, 01-2', $html);
        $this->assertStringContainsString('TEMU001', $html);
        $this->assertStringContainsString('TEMU002', $html);
        $this->assertStringContainsString('HANDLING BL', $html);
    }

    public function test_storage_style_rejects_final_invoice_below_dp_and_rolls_back(): void
    {
        $billing = app(\App\Services\TemasBillingService::class);
        $dpInvoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $billing->replace($dpInvoice, [['kapal' => 'TEMAS 1', 'voyage' => 'V001', 'payment_mode' => 'dp', 'nominal_dibayar' => 2000000]]);
        $dp = \App\Models\BiayaKapalTemasStage::firstOrFail();
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        try {
            $billing->replace($invoice, [$this->finalCosts() + ['payment_mode' => 'pelunasan_dp', 'dp_stage_id' => $dp->id]]);
            $this->fail('Final invoice below DP must be rejected.');
        } catch (ValidationException $e) {
            $this->assertSame(0, $invoice->temasDetails()->count());
            $this->assertSame(1, $billing->candidates()->count());
        }
    }

    public function test_storage_style_rejects_second_settlement_and_dp_edit(): void
    {
        $billing = app(\App\Services\TemasBillingService::class);
        $dpInvoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $billing->replace($dpInvoice, [['kapal' => 'TEMAS 1', 'voyage' => 'V001', 'payment_mode' => 'dp', 'nominal_dibayar' => 300000]]);
        $dp = \App\Models\BiayaKapalTemasStage::firstOrFail();
        $section = $this->finalCosts() + ['payment_mode' => 'pelunasan_dp', 'dp_stage_id' => $dp->id];
        $invoice = BiayaKapal::create(['status_pembayaran' => 'pending']);
        $billing->replace($invoice, [$section]);
        try {
            $billing->replace(BiayaKapal::create(['status_pembayaran' => 'pending']), [$section]);
            $this->fail('Second settlement must be rejected.');
        } catch (ValidationException $e) {
            $this->assertCount(1, $dp->settlements()->get());
        }
        $this->expectException(ValidationException::class);
        $billing->assertCanReplace($dpInvoice);
    }
}

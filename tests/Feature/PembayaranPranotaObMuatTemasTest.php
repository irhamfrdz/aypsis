<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaObController;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaOb;
use App\Models\PranotaObMuatTemas;
use App\Models\User;
use App\Services\CoaTransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaObMuatTemasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('tagihan_ob', fn (Blueprint $table) => $table->id());
        Schema::create('pranota_obs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('status')->default('unpaid');
            $table->json('items');
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_pembayaran', 'nomor_accurate', 'bank', 'jenis_transaksi', 'tanggal_kas', 'alasan_penyesuaian', 'keterangan', 'status', 'kapal', 'voyage'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['total_pembayaran', 'penyesuaian', 'total_setelah_penyesuaian', 'dp_amount', 'total_biaya_pranota'] as $column) {
                $table->decimal($column, 15, 2)->nullable();
            }
            foreach (['nomor_cetakan', 'pembayaran_ob_id', 'akun_coa_id', 'akun_bank_id', 'created_by', 'updated_by'] as $column) {
                $table->unsignedBigInteger($column)->nullable();
            }
            foreach (['pranota_ob_ids', 'pembayaran_ob_ids', 'breakdown_supir'] as $column) {
                $table->json($column)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
            $table->string('kode_nomor')->nullable();
            $table->string('nomor_akun')->nullable();
            $table->string('tipe_akun')->nullable();
        });
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
        });
        Schema::create('pembayaran_obs', function (Blueprint $table) {
            $table->id();
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->date('tanggal_pembayaran')->nullable();
            $table->unsignedBigInteger('akun_coa_id')->nullable();
        });
        (require database_path('migrations/2026_10_09_000004_create_pranota_ob_muat_temas_tables.php'))->up();
        (require database_path('migrations/2026_10_09_000005_add_muat_temas_to_pembayaran_pranota_ob.php'))->up();
        DB::table('users')->insert(['id' => 1, 'name' => 'Penguji']);
        $user = new User;
        $user->setRawAttributes(['id' => 1, 'name' => 'Penguji']);
        $this->actingAs($user);
        Gate::define('pembayaran-pranota-ob-create', fn () => true);
        Gate::define('pembayaran-pranota-ob-delete', fn () => true);
        DB::table('akun_coa')->insert([['id' => 1, 'nama_akun' => 'Biaya OB'], ['id' => 2, 'nama_akun' => 'Bank'], ['id' => 3, 'nama_akun' => 'Biaya Tambahan'], ['id' => 4, 'nama_akun' => 'Uang Muka OB']]);
        (require database_path('migrations/2026_11_02_000001_create_pembayaran_pranota_ob_akun_items_table.php'))->up();
        (require database_path('migrations/2026_11_03_000001_add_memo_to_pembayaran_pranota_ob_akun_items_table.php'))->up();
        DB::table('pranota_obs')->insert([
            'id' => 1, 'nomor_pranota' => 'OB-001', 'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001',
            'items' => json_encode([['nomor_kontainer' => 'REGULAR', 'supir' => 'SUPIR UJI', 'biaya' => 100000]]),
        ]);
        $temas = PranotaObMuatTemas::create([
            'nomor_pranota' => 'PMT-10-26-000001', 'tanggal_pranota' => '2026-10-09',
            'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001', 'nominal' => 250000,
            'adjustment' => 10000, 'grand_total' => 260000, 'created_by' => 1,
        ]);
        DB::table('tagihan_ob')->insert(['id' => 1]);
        $temas->items()->create(['tagihan_ob_id' => 1, 'snapshot' => [
            'nomor_kontainer' => 'TEMAS', 'nama_supir' => 'SUPIR UJI', 'biaya' => 250000,
        ]]);
    }

    private function paymentRequest(array $ids): Request
    {
        return Request::create('/pembayaran-pranota-ob', 'POST', [
            'nomor_pembayaran' => 'PY-OB-TEST', 'debit_kredit' => 'credit',
            'akun_items' => [['akun_coa_id' => 1, 'nominal' => 360000]], 'akun_bank_id' => 2, 'tanggal_kas' => '2026-10-09',
            'kapal' => 'KAPAL UJI', 'voyage' => '001', 'pranota_ids' => $ids,
            'total_pembayaran' => 1,
        ]);
    }

    public function test_dp_account_defaults_to_linked_dp_account_only_when_unambiguous(): void
    {
        DB::table('pembayaran_obs')->insert([
            ['id' => 1, 'dp_amount' => 100000, 'akun_coa_id' => 4],
            ['id' => 2, 'dp_amount' => 50000, 'akun_coa_id' => 4],
            ['id' => 3, 'dp_amount' => 50000, 'akun_coa_id' => 3],
        ]);

        $payment = new PembayaranPranotaOb(['pembayaran_ob_ids' => [1, 2]]);
        $this->assertSame(4, $payment->defaultAkunDpId());

        $payment->pembayaran_ob_ids = [1, 3];
        $this->assertNull($payment->defaultAkunDpId());

        $payment->pembayaran_ob_ids = null;
        $payment->pembayaran_ob_id = 1;
        $this->assertSame(4, $payment->defaultAkunDpId());

        $payment->akun_dp_id = 3;
        $this->assertSame(3, $payment->defaultAkunDpId());
    }

    public function test_mixed_payment_keeps_same_numeric_ids_separate_and_uses_saved_cost(): void
    {
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('recordTransaction')->times(5)->andReturn(new CoaTransaction);
        $service->shouldReceive('deleteTransactionByReference')->twice()->andReturn(true);
        $controller = new PembayaranPranotaObController($service);
        $response = $controller->store($this->paymentRequest(['1', 'muat_temas:1']));
        $this->assertEquals(route('pembayaran-pranota-ob.index'), $response->getTargetUrl());
        $payment = PembayaranPranotaOb::firstOrFail();
        $this->assertEquals([1], $payment->pranota_ob_ids);
        $this->assertEquals([1], $payment->pranota_ob_muat_temas_ids);
        $this->assertEquals(360000, $payment->total_biaya_pranota);
        $this->assertCount(2, $payment->pranota_obs);
        $this->assertDatabaseHas('pranota_obs', ['id' => 1, 'status' => 'paid']);
        $this->assertDatabaseHas('pranota_ob_muat_temas', ['id' => 1, 'status' => 'paid']);
        $this->assertEquals('pembayaran-pranota-ob.print', $controller->print((string) $payment->id)->name());
        $payment->update(['dp_amount' => 25000, 'akun_dp_id' => 4]);
        $controller->updateTotal($payment);
        $this->assertEquals(335000, $payment->fresh()->total_pembayaran);
        $this->assertEquals(360000, $payment->fresh()->total_biaya_pranota);
        $controller->destroy((string) $payment->id);
        $this->assertDatabaseHas('pranota_obs', ['id' => 1, 'status' => 'unpaid']);
        $this->assertDatabaseHas('pranota_ob_muat_temas', ['id' => 1, 'status' => 'unpaid']);
        $this->assertDatabaseCount('pembayaran_pranota_obs', 0);
    }

    public function test_multiple_accounts_keep_gross_amounts_and_book_dp_separately(): void
    {
        DB::table('pembayaran_obs')->insert(['id' => 1, 'dp_amount' => 60000]);
        $entries = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('recordTransaction')->times(4)
            ->andReturnUsing(function ($nama, $debit, $kredit, $tanggal, $referensi, $jenis, $keterangan, $coaId) use (&$entries) {
                $entries[] = [$coaId, $debit, $kredit, $keterangan];

                return new CoaTransaction;
            });
        $controller = new PembayaranPranotaObController($service);
        $request = $this->paymentRequest(['1', 'muat_temas:1']);
        $request->merge([
            'akun_items' => [['akun_coa_id' => 1, 'nominal' => 100000, 'memo' => 'Biaya kontainer Jakarta'], ['akun_coa_id' => 3, 'nominal' => 260000, 'memo' => 'Biaya tambahan']],
            'dp_ids' => [1], 'akun_dp_id' => 4,
        ]);

        $controller->store($request);
        $payment = PembayaranPranotaOb::firstOrFail();
        $this->assertEquals(300000, $payment->total_pembayaran);
        $this->assertEquals(60000, $payment->dp_amount);
        $this->assertEquals([100000, 260000], $payment->akunItems->pluck('nominal')->map(fn ($value) => (int) $value)->all());
        $this->assertSame(['Biaya kontainer Jakarta', 'Biaya tambahan'], $payment->akunItems->pluck('memo')->all());
        $this->assertEquals([[1, 100000.0, 0], [3, 260000.0, 0], [4, 0, 60000.0], [2, 0, 300000.0]], array_map(fn ($entry) => array_slice($entry, 0, 3), $entries));
        $this->assertStringContainsString('Memo: Biaya kontainer Jakarta', $entries[0][3]);
        $this->assertStringContainsString('Memo: Biaya tambahan', $entries[1][3]);
        $this->assertStringNotContainsString('Memo:', $entries[2][3]);
    }

    public function test_create_books_driver_debt_once_as_receivable_credit(): void
    {
        DB::table('akun_coa')->insert(['id' => 5, 'nama_akun' => 'Piutang Lain-Lain']);
        DB::table('pembayaran_obs')->insert(['id' => 1, 'dp_amount' => 60000, 'akun_coa_id' => 4]);
        $entries = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('recordTransaction')->times(4)
            ->andReturnUsing(function ($nama, $debit, $kredit) use (&$entries) {
                $entries[] = [$nama, $debit, $kredit];

                return new CoaTransaction;
            });

        $request = $this->paymentRequest(['1', 'muat_temas:1']);
        $request->merge([
            'akun_items' => [
                ['akun_coa_id' => 1, 'nominal' => 360000],
                ['akun_coa_id' => 5, 'nominal' => 100000],
            ],
            'dp_ids' => [1],
            'akun_dp_id' => 4,
            'breakdown_supir' => json_encode([[
                'nama_supir' => 'SUPIR UJI',
                'total_biaya' => 360000,
                'dp' => 60000,
                'sisa' => 300000,
                'potongan_utang' => 100000,
                'potongan_tabungan' => 0,
                'potongan_bpjs' => 0,
                'grand_total' => 200000,
            ]]),
        ]);
        (new PembayaranPranotaObController($service))->store($request);

        $payment = PembayaranPranotaOb::firstOrFail();
        $this->assertEquals(200000, $payment->total_pembayaran);
        $this->assertEquals(200000, $payment->total_setelah_penyesuaian);
        $this->assertEquals([
            ['Biaya OB', 360000, 0],
            ['Piutang Lain-Lain', 0, 100000],
            ['Uang Muka OB', 0, 60000],
            ['Bank', 0, 200000],
        ], $entries);
    }

    public function test_editing_account_memo_updates_its_journal_line(): void
    {
        $payment = PembayaranPranotaOb::create([
            'nomor_pembayaran' => 'PY-OB-MEMO',
            'tanggal_kas' => '2026-10-09',
            'jenis_transaksi' => 'credit',
            'total_pembayaran' => 100000,
            'total_setelah_penyesuaian' => 100000,
            'dp_amount' => 0,
            'akun_coa_id' => 1,
            'akun_bank_id' => 2,
        ]);
        $payment->akunItems()->create(['akun_coa_id' => 1, 'nominal' => 100000, 'memo' => 'Catatan lama']);

        $journalDescriptions = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('deleteTransactionByReference')->once()->andReturn(true);
        $service->shouldReceive('recordTransaction')->twice()
            ->andReturnUsing(function ($nama, $debit, $kredit, $tanggal, $referensi, $jenis, $keterangan) use (&$journalDescriptions) {
                $journalDescriptions[] = $keterangan;

                return new CoaTransaction;
            });

        $request = Request::create('/pembayaran-pranota-ob/'.$payment->id, 'PUT', [
            'tanggal_kas' => '2026-10-09',
            'debit_kredit' => 'credit',
            'akun_items' => [['akun_coa_id' => 1, 'nominal' => 100000, 'memo' => 'Catatan baru']],
            'akun_bank_id' => 2,
        ]);
        $response = (new PembayaranPranotaObController($service))->update($request, (string) $payment->id);

        $this->assertEquals(route('pembayaran-pranota-ob.index'), $response->getTargetUrl());
        $this->assertSame('Catatan baru', $payment->fresh()->akunItems()->first()->memo);
        $this->assertStringContainsString('Memo: Catatan baru', $journalDescriptions[0]);
        $this->assertStringNotContainsString('Memo:', $journalDescriptions[1]);
    }

    public function test_piutang_lain_lain_reduces_bank_payment_on_edit(): void
    {
        DB::table('akun_coa')->insert(['id' => 5, 'nama_akun' => 'Piutang Lain-Lain']);
        $payment = PembayaranPranotaOb::create([
            'nomor_pembayaran' => 'PY-OB-PIUTANG',
            'tanggal_kas' => '2026-10-09',
            'jenis_transaksi' => 'credit',
            'total_pembayaran' => 100000,
            'total_setelah_penyesuaian' => 100000,
            'dp_amount' => 0,
            'akun_coa_id' => 1,
            'akun_bank_id' => 2,
        ]);
        $payment->akunItems()->create(['akun_coa_id' => 1, 'nominal' => 100000]);

        $entries = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('deleteTransactionByReference')->twice()->andReturn(true);
        $service->shouldReceive('recordTransaction')->times(6)
            ->andReturnUsing(function ($nama, $debit, $kredit) use (&$entries) {
                $entries[] = [$nama, $debit, $kredit];

                return new CoaTransaction;
            });

        $request = Request::create('/pembayaran-pranota-ob/'.$payment->id, 'PUT', [
            'tanggal_kas' => '2026-10-09',
            'debit_kredit' => 'credit',
            'akun_items' => [
                ['akun_coa_id' => 1, 'nominal' => 100000],
                ['akun_coa_id' => 5, 'nominal' => 20000],
            ],
            'akun_bank_id' => 2,
        ]);
        (new PembayaranPranotaObController($service))->update($request, (string) $payment->id);

        $this->assertEquals(80000, $payment->fresh()->total_pembayaran);
        $this->assertEquals(80000, $payment->fresh()->total_setelah_penyesuaian);
        $this->assertEquals([
            ['Biaya OB', 100000, 0],
            ['Piutang Lain-Lain', 0, 20000],
            ['Bank', 0, 80000],
        ], $entries);

        (new PembayaranPranotaObController($service))->update($request, (string) $payment->id);
        $this->assertEquals(80000, $payment->fresh()->total_pembayaran);
    }

    public function test_potongan_utang_in_breakdown_is_not_deducted_twice(): void
    {
        DB::table('akun_coa')->insert(['id' => 5, 'nama_akun' => 'Piutang Lain-Lain']);
        $breakdown = [[
            'nama_supir' => 'IWAN',
            'total_biaya' => 3520000,
            'dp' => 1800000,
            'sisa' => 1720000,
            'potongan_utang' => 100000,
            'potongan_tabungan' => 0,
            'potongan_bpjs' => 0,
            'grand_total' => 1620000,
        ]];
        $payment = PembayaranPranotaOb::create([
            'nomor_pembayaran' => 'PY-OB-POTONGAN',
            'tanggal_kas' => '2026-11-03',
            'jenis_transaksi' => 'credit',
            'total_pembayaran' => 1620000,
            'total_setelah_penyesuaian' => 1620000,
            'dp_amount' => 1800000,
            'breakdown_supir' => $breakdown,
            'akun_coa_id' => 1,
            'akun_dp_id' => 4,
            'akun_bank_id' => 2,
        ]);
        $payment->akunItems()->create(['akun_coa_id' => 1, 'nominal' => 3520000]);

        $entries = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('deleteTransactionByReference')->once()->andReturn(true);
        $service->shouldReceive('recordTransaction')->times(4)
            ->andReturnUsing(function ($nama, $debit, $kredit) use (&$entries) {
                $entries[] = [$nama, $debit, $kredit];

                return new CoaTransaction;
            });

        $request = Request::create('/pembayaran-pranota-ob/'.$payment->id, 'PUT', [
            'tanggal_kas' => '2026-11-03',
            'debit_kredit' => 'credit',
            'akun_items' => [
                ['akun_coa_id' => 1, 'nominal' => 3520000],
                ['akun_coa_id' => 5, 'nominal' => 100000],
            ],
            'akun_dp_id' => 4,
            'akun_bank_id' => 2,
            'breakdown_supir' => json_encode($breakdown),
        ]);
        (new PembayaranPranotaObController($service))->update($request, (string) $payment->id);

        $this->assertEquals(1620000, $payment->fresh()->total_pembayaran);
        $this->assertEquals(1620000, $payment->fresh()->total_setelah_penyesuaian);
        $this->assertEquals([
            ['Biaya OB', 3520000, 0],
            ['Piutang Lain-Lain', 0, 100000],
            ['Uang Muka OB', 0, 1800000],
            ['Bank', 0, 1620000],
        ], $entries);
    }

    public function test_account_amounts_must_equal_gross_total_before_dp(): void
    {
        DB::table('pembayaran_obs')->insert(['id' => 1, 'dp_amount' => 60000]);
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldNotReceive('recordTransaction');
        $controller = new PembayaranPranotaObController($service);
        $request = $this->paymentRequest(['1', 'muat_temas:1']);
        $request->merge([
            'akun_items' => [['akun_coa_id' => 1, 'nominal' => 300000]],
            'dp_ids' => [1], 'akun_dp_id' => 4,
        ]);

        $controller->store($request);
        $this->assertDatabaseCount('pembayaran_pranota_obs', 0);
        $this->assertDatabaseHas('pranota_obs', ['id' => 1, 'status' => 'unpaid']);
        $this->assertDatabaseHas('pranota_ob_muat_temas', ['id' => 1, 'status' => 'unpaid']);
    }

    public function test_paid_or_missing_pranota_cannot_create_another_payment(): void
    {
        PranotaObMuatTemas::findOrFail(1)->update(['status' => 'paid']);
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldNotReceive('recordDoubleEntry');
        $controller = new PembayaranPranotaObController($service);
        foreach ([['muat_temas:1'], ['1', 'muat_temas:999']] as $ids) {
            $controller->store($this->paymentRequest($ids));
            $this->assertDatabaseCount('pembayaran_pranota_obs', 0);
            $this->assertDatabaseHas('pranota_obs', ['id' => 1, 'status' => 'unpaid']);
        }
    }

    public function test_criteria_and_selection_include_unpaid_temas_and_hide_paid_temas(): void
    {
        $controller = new PembayaranPranotaObController($this->mock(CoaTransactionService::class));
        $request = Request::create('/pembayaran-pranota-ob/create', 'GET', ['kapal' => 'KAPAL UJI', 'voyage' => '001']);
        $this->assertCount(2, $controller->create($request)->getData()['pranotaList']);
        PranotaObMuatTemas::findOrFail(1)->update(['status' => 'paid']);
        $this->assertCount(1, $controller->create($request)->getData()['pranotaList']);
        PranotaObMuatTemas::findOrFail(1)->update(['status' => 'unpaid', 'nama_kapal' => 'KAPAL TEMAS', 'no_voyage' => '002']);
        $criteria = $controller->selectCriteria()->getData();
        $this->assertContains('KAPAL TEMAS', $criteria['kapalList']);
        $this->assertEquals(['001'], $criteria['voyagesByKapal']['KAPAL UJI']->all());
        $this->assertEquals(['002'], $criteria['voyagesByKapal']['KAPAL TEMAS']->all());
    }

    public function test_breakdown_includes_adjustment_without_changing_snapshot(): void
    {
        $temas = PranotaObMuatTemas::findOrFail(1);
        $this->assertEquals(260000, array_sum(array_column($temas->getPaymentItems(), 'biaya')));
        $this->assertEquals(250000, $temas->items->first()->snapshot->biaya);
    }
}

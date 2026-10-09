<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaObController;
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
        });
        (require database_path('migrations/2026_10_09_000004_create_pranota_ob_muat_temas_tables.php'))->up();
        (require database_path('migrations/2026_10_09_000005_add_muat_temas_to_pembayaran_pranota_ob.php'))->up();
        DB::table('users')->insert(['id' => 1, 'name' => 'Penguji']);
        $user = new User;
        $user->setRawAttributes(['id' => 1, 'name' => 'Penguji']);
        $this->actingAs($user);
        Gate::define('pembayaran-pranota-ob-create', fn () => true);
        Gate::define('pembayaran-pranota-ob-delete', fn () => true);
        DB::table('akun_coa')->insert([['id' => 1, 'nama_akun' => 'Biaya OB'], ['id' => 2, 'nama_akun' => 'Bank']]);
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
            'akun_coa_id' => 1, 'akun_bank_id' => 2, 'tanggal_kas' => '2026-10-09',
            'kapal' => 'KAPAL UJI', 'voyage' => '001', 'pranota_ids' => $ids,
            'total_pembayaran' => 1,
        ]);
    }

    public function test_mixed_payment_keeps_same_numeric_ids_separate_and_uses_saved_cost(): void
    {
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('recordDoubleEntry')->once()->withArgs(fn ($debit, $credit) => $debit['jumlah'] === 360000.0 && $credit['jumlah'] === 360000.0)->andReturn(true);
        $service->shouldReceive('deleteTransactionByReference')->once()->andReturn(true);
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
        $payment->update(['dp_amount' => 25000]);
        $controller->updateTotal($payment);
        $this->assertEquals(335000, $payment->fresh()->total_pembayaran);
        $this->assertEquals(360000, $payment->fresh()->total_biaya_pranota);
        $controller->destroy((string) $payment->id);
        $this->assertDatabaseHas('pranota_obs', ['id' => 1, 'status' => 'unpaid']);
        $this->assertDatabaseHas('pranota_ob_muat_temas', ['id' => 1, 'status' => 'unpaid']);
        $this->assertDatabaseCount('pembayaran_pranota_obs', 0);
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
        $this->assertContains('002', $criteria['voyageList']);
    }

    public function test_breakdown_includes_adjustment_without_changing_snapshot(): void
    {
        $temas = PranotaObMuatTemas::findOrFail(1);
        $this->assertEquals(260000, array_sum(array_column($temas->getPaymentItems(), 'biaya')));
        $this->assertEquals(250000, $temas->items->first()->snapshot->biaya);
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranAktivitasLainController;
use App\Models\PembayaranAktivitasLain;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PembayaranAktivitasLainBatamTest extends TestCase
{
    private int $taxAccountId;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('coa_transactions');
        Schema::dropIfExists('akun_coa');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('pembayaran_aktivitas_lains');
        Schema::dropIfExists('mobils');

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->nullable();
            $table->string('no_kir')->nullable();
        });

        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->date('tanggal');
            $table->string('jenis_aktivitas');
            $table->string('lokasi')->nullable();
            $table->decimal('pph_persen', 3, 1)->nullable();
            $table->decimal('pph_nominal', 15, 2)->default(0);
            $table->boolean('pph_manual')->default(false);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('sub_jenis_kendaraan')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->json('nomor_polisi_list')->nullable();
            $table->json('kendaraan_details')->nullable();
            $table->string('penerima')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->enum('debit_kredit', ['debit', 'kredit']);
            $table->unsignedBigInteger('akun_coa_id')->nullable();
            $table->json('akun_details')->nullable();
            $table->unsignedBigInteger('akun_bank_id')->nullable();
            $table->unsignedBigInteger('akun_pph_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $migration = require base_path('database/migrations/2026_10_17_000005_make_debit_kredit_nullable_for_batam_vehicle_payments.php');
        $migration->up();

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun')->nullable();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        $this->taxAccountId = DB::table('akun_coa')->insertGetId(['nomor_akun' => '2102.003', 'saldo' => 0]);
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('action')->nullable();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_batam_vehicle_payment_does_not_require_accounts_or_create_journal(): void
    {
        $this->withoutMiddleware();

        $this->post(route('pembayaran-aktivitas-lain.store'), $this->vehiclePayload('batam'))
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertNull($payment->debit_kredit);
        $this->assertNull($payment->akun_coa_id);
        $this->assertNull($payment->akun_bank_id);
        $this->assertSame(0, DB::table('coa_transactions')->count());

        $response = app(PembayaranAktivitasLainController::class)->updateJumlah(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id.'/update-jumlah', 'PATCH', ['jumlah' => 200]),
            $payment
        );
        $this->assertSame(route('pembayaran-aktivitas-lain.show', $payment), $response->getTargetUrl());
        $this->assertSame(196, $payment->fresh()->grand_total);
        $this->assertSame(0, DB::table('coa_transactions')->count());
    }

    public function test_jakarta_vehicle_payment_still_creates_double_book_journal(): void
    {
        $this->withoutMiddleware();

        $expenseId = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $bankId = DB::table('akun_coa')->insertGetId(['saldo' => 1000]);
        $payload = $this->vehiclePayload('jakarta') + [
            'debit_kredit' => 'kredit',
            'akun_coa_id' => $expenseId,
            'akun_bank_id' => $bankId,
        ];

        $this->post(route('pembayaran-aktivitas-lain.store'), $payload)
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame('kredit', $payment->debit_kredit);
        $this->assertSame(3, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());
        $this->assertEquals(100, DB::table('akun_coa')->find($expenseId)->saldo);
        $this->assertEquals(902, DB::table('akun_coa')->find($bankId)->saldo);
        $this->assertEquals(-2, DB::table('akun_coa')->where('nomor_akun', '2102.003')->value('saldo'));

        app(PembayaranAktivitasLainController::class)->updateJumlah(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id.'/update-jumlah', 'PATCH', ['jumlah' => 200]),
            $payment
        );
        $this->assertSame(196, $payment->fresh()->grand_total);
        $this->assertEquals(804, DB::table('akun_coa')->find($bankId)->saldo);
        $this->assertEquals(-4, DB::table('akun_coa')->where('nomor_akun', '2102.003')->value('saldo'));
        $this->assertSame(3, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());

        app(PembayaranAktivitasLainController::class)->destroy($payment->fresh());
        $this->assertSame(0, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());
        $this->assertEquals(1000, DB::table('akun_coa')->find($bankId)->saldo);
        $this->assertEquals(0, DB::table('akun_coa')->where('nomor_akun', '2102.003')->value('saldo'));
    }

    public function test_other_payment_splits_amount_across_accounts_and_updates_journal(): void
    {
        $this->withoutMiddleware();

        $first = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $second = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $bank = DB::table('akun_coa')->insertGetId(['saldo' => 1000]);
        $payload = [
            'tanggal' => '2026-10-28',
            'jenis_aktivitas' => 'Pembayaran Lain Lain',
            'penerima' => 'Penerima',
            'keterangan' => 'Biaya lain',
            'jumlah' => 999,
            'debit_kredit' => 'kredit',
            'akun_bank_id' => $bank,
            'akun_details' => [
                ['akun_coa_id' => $first, 'nominal' => 100, 'memo' => 'Biaya administrasi'],
                ['akun_coa_id' => $second, 'nominal' => 250, 'memo' => 'Biaya pengiriman'],
            ],
        ];

        $this->post(route('pembayaran-aktivitas-lain.store'), $payload)
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame(350, $payment->jumlah);
        $this->assertCount(2, $payment->akun_details);
        $this->assertSame('Biaya administrasi', $payment->akun_details[0]['memo']);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $first, 'debit' => 100]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $second, 'debit' => 250]);
        $this->assertStringContainsString('Biaya pengiriman', DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->where('coa_id', $second)->value('keterangan'));
        $this->assertEquals(650, DB::table('akun_coa')->find($bank)->saldo);

        $payload['akun_details'] = [['akun_coa_id' => $second, 'nominal' => 400, 'memo' => 'Memo diperbarui']];
        $response = app(PembayaranAktivitasLainController::class)->update(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $payload),
            $payment
        );
        $this->assertSame(route('pembayaran-aktivitas-lain.show', $payment), $response->getTargetUrl());
        $this->assertSame(400, $payment->fresh()->jumlah);
        $this->assertSame('Memo diperbarui', $payment->fresh()->akun_details[0]['memo']);
        $this->assertStringContainsString('Memo diperbarui', DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->where('coa_id', $second)->value('keterangan'));
        $this->assertEquals(0, DB::table('akun_coa')->find($first)->saldo);
        $this->assertEquals(400, DB::table('akun_coa')->find($second)->saldo);
        $this->assertEquals(600, DB::table('akun_coa')->find($bank)->saldo);

        $payload['akun_details'][] = ['akun_coa_id' => $second, 'nominal' => 50, 'memo' => 'Biaya tambahan'];
        $response = app(PembayaranAktivitasLainController::class)->update(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $payload),
            $payment->fresh()
        );
        $this->assertSame(route('pembayaran-aktivitas-lain.show', $payment), $response->getTargetUrl());
        $this->assertSame(450, $payment->fresh()->jumlah);
        $this->assertCount(2, $payment->fresh()->akun_details);
        $this->assertEquals(450, DB::table('akun_coa')->find($second)->saldo);
        $this->assertEquals(550, DB::table('akun_coa')->find($bank)->saldo);
        $this->assertDatabaseHas('coa_transactions', [
            'nomor_referensi' => $payment->nomor,
            'coa_id' => $second,
            'debit' => 50,
        ]);

        $payload['akun_details'] = [['akun_coa_id' => $bank, 'nominal' => 100]];
        try {
            app(PembayaranAktivitasLainController::class)->update(
                Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $payload),
                $payment->fresh()
            );
            $this->fail('Akun bank tidak boleh digunakan sebagai akun biaya.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('akun_details.0.akun_coa_id', $exception->errors());
        }
    }

    public function test_other_payment_debit_creates_one_credit_per_account(): void
    {
        $this->withoutMiddleware();

        $first = DB::table('akun_coa')->insertGetId(['saldo' => 500]);
        $second = DB::table('akun_coa')->insertGetId(['saldo' => 500]);
        $bank = DB::table('akun_coa')->insertGetId(['saldo' => 0]);

        $this->post(route('pembayaran-aktivitas-lain.store'), [
            'tanggal' => '2026-10-28',
            'jenis_aktivitas' => 'Pembayaran Lain Lain',
            'penerima' => 'Penerima',
            'keterangan' => 'Pengembalian biaya',
            'debit_kredit' => 'debit',
            'akun_bank_id' => $bank,
            'akun_details' => [
                ['akun_coa_id' => $first, 'nominal' => 100],
                ['akun_coa_id' => $second, 'nominal' => 200],
            ],
        ])->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame(300, $payment->jumlah);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $first, 'kredit' => 100]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $second, 'kredit' => 200]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $bank, 'debit' => 300]);
    }

    public function test_changing_vehicle_payment_to_batam_reverses_existing_journal(): void
    {
        $this->withoutMiddleware();

        $expenseId = DB::table('akun_coa')->insertGetId(['saldo' => 100]);
        $bankId = DB::table('akun_coa')->insertGetId(['saldo' => 900]);
        $payment = PembayaranAktivitasLain::create([
            'nomor' => 'PAL/2026/10/0001',
            'tanggal' => '2026-10-17',
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'lokasi' => 'jakarta',
            'pph_persen' => 2,
            'akun_pph_id' => $this->taxAccountId,
            'jumlah' => 100,
            'debit_kredit' => 'kredit',
            'akun_coa_id' => $expenseId,
            'akun_bank_id' => $bankId,
        ]);
        DB::table('coa_transactions')->insert([
            ['coa_id' => $expenseId, 'tanggal_transaksi' => '2026-10-17', 'nomor_referensi' => $payment->nomor, 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['coa_id' => $bankId, 'tanggal_transaksi' => '2026-10-17', 'nomor_referensi' => $payment->nomor, 'debit' => 0, 'kredit' => 100, 'saldo' => 900],
        ]);

        $response = app(PembayaranAktivitasLainController::class)->update(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $this->vehiclePayload('batam')),
            $payment
        );
        $this->assertSame(route('pembayaran-aktivitas-lain.show', $payment), $response->getTargetUrl());

        $this->assertNull($payment->fresh()->debit_kredit);
        $this->assertNull($payment->fresh()->akun_coa_id);
        $this->assertNull($payment->fresh()->akun_bank_id);
        $this->assertSame(0, DB::table('coa_transactions')->count());
        $this->assertEquals(0, DB::table('akun_coa')->find($expenseId)->saldo);
        $this->assertEquals(1000, DB::table('akun_coa')->find($bankId)->saldo);
    }

    public function test_changing_vehicle_payment_from_batam_to_jakarta_creates_journal(): void
    {
        $expenseId = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $bankId = DB::table('akun_coa')->insertGetId(['saldo' => 1000]);
        $payment = PembayaranAktivitasLain::create([
            'nomor' => 'PAL/2026/10/0001',
            'tanggal' => '2026-10-17',
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'lokasi' => 'batam',
            'pph_persen' => 2,
            'akun_pph_id' => $this->taxAccountId,
            'jumlah' => 100,
        ]);
        $payload = $this->vehiclePayload('jakarta') + [
            'debit_kredit' => 'kredit',
            'akun_coa_id' => $expenseId,
            'akun_bank_id' => $bankId,
        ];

        $response = app(PembayaranAktivitasLainController::class)->update(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $payload),
            $payment
        );

        $this->assertSame(route('pembayaran-aktivitas-lain.show', $payment), $response->getTargetUrl());
        $this->assertSame('kredit', $payment->fresh()->debit_kredit);
        $this->assertSame(3, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());
        $this->assertEquals(100, DB::table('akun_coa')->find($expenseId)->saldo);
        $this->assertEquals(902, DB::table('akun_coa')->find($bankId)->saldo);
    }

    public function test_existing_gross_bank_journal_can_be_corrected_once(): void
    {
        $expenseId = DB::table('akun_coa')->insertGetId(['saldo' => 100]);
        $bankId = DB::table('akun_coa')->insertGetId(['saldo' => 900]);
        $payment = PembayaranAktivitasLain::create([
            'nomor' => 'PAL/2026/10/0100',
            'tanggal' => '2026-10-10',
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'lokasi' => 'jakarta',
            'pph_persen' => 2,
            'akun_pph_id' => $this->taxAccountId,
            'jumlah' => 100,
            'debit_kredit' => 'kredit',
            'akun_coa_id' => $expenseId,
            'akun_bank_id' => $bankId,
        ]);
        DB::table('coa_transactions')->insert([
            ['coa_id' => $expenseId, 'tanggal_transaksi' => '2026-10-10', 'nomor_referensi' => $payment->nomor, 'jenis_transaksi' => 'Pembayaran Aktivitas Lain', 'debit' => 100, 'kredit' => 0, 'saldo' => 100],
            ['coa_id' => $bankId, 'tanggal_transaksi' => '2026-10-10', 'nomor_referensi' => $payment->nomor, 'jenis_transaksi' => 'Pembayaran Aktivitas Lain', 'debit' => 0, 'kredit' => 100, 'saldo' => 900],
        ]);

        $this->artisan('pembayaran-aktivitas-lain:perbaiki-jurnal-pph', ['nomor' => $payment->nomor, '--execute' => true])
            ->assertExitCode(0);
        $this->assertEquals(98, DB::table('coa_transactions')->where('coa_id', $bankId)->value('kredit'));
        $this->assertEquals(902, DB::table('akun_coa')->find($bankId)->saldo);
        $this->assertEquals(-2, DB::table('akun_coa')->where('nomor_akun', '2102.003')->value('saldo'));
        $this->assertSame(3, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());

        $this->artisan('pembayaran-aktivitas-lain:perbaiki-jurnal-pph', ['nomor' => $payment->nomor, '--execute' => true])
            ->assertExitCode(0);
        $this->assertSame(3, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());
    }

    public function test_vehicle_pph_automatically_uses_tax_payable_account(): void
    {
        $this->withoutMiddleware();

        $expenseId = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $bankId = DB::table('akun_coa')->insertGetId(['saldo' => 1000]);
        $customTaxId = DB::table('akun_coa')->insertGetId(['nomor_akun' => '2102.005', 'saldo' => 0]);
        $payload = $this->vehiclePayload('jakarta') + [
            'debit_kredit' => 'kredit',
            'akun_coa_id' => $expenseId,
            'akun_bank_id' => $bankId,
        ];
        unset($payload['akun_pph_id']);
        $this->post(route('pembayaran-aktivitas-lain.store'), $payload)
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame($this->taxAccountId, $payment->akun_pph_id);
        $this->assertEquals(2, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->where('coa_id', $this->taxAccountId)->value('kredit'));
        $this->assertEquals(0, DB::table('akun_coa')->find($customTaxId)->saldo);

        $payload['akun_pph_id'] = $customTaxId;
        $payload['pph_manual'] = '1';
        $payload['pph_nominal'] = 3;
        app(PembayaranAktivitasLainController::class)->update(
            Request::create('/pembayaran-aktivitas-lain/'.$payment->id, 'PUT', $payload),
            $payment
        );

        $this->assertSame($this->taxAccountId, $payment->fresh()->akun_pph_id);
        $this->assertEquals(0, DB::table('akun_coa')->find($customTaxId)->saldo);
        $this->assertEquals(-3, DB::table('akun_coa')->find($this->taxAccountId)->saldo);
    }

    public function test_vehicle_payment_saves_costs_and_memos_per_account_with_pph_journal(): void
    {
        $this->withoutMiddleware();

        $first = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $second = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $bank = DB::table('akun_coa')->insertGetId(['saldo' => 1000]);
        $payload = $this->vehiclePayload('jakarta') + ['akun_bank_id' => $bank];
        $payload['akun_details'] = [
            ['akun_coa_id' => $first, 'debit_kredit' => 'debit', 'nominal' => 100, 'memo' => 'Pajak kendaraan'],
            ['akun_coa_id' => $second, 'debit_kredit' => 'debit', 'nominal' => 200, 'memo' => 'Administrasi'],
        ];

        $this->post(route('pembayaran-aktivitas-lain.store'), $payload)
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame(300, $payment->jumlah);
        $this->assertSame('Administrasi', $payment->akun_details[1]['memo']);
        $this->assertEquals(294, $payment->grand_total);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $first, 'debit' => 100]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $second, 'debit' => 200]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $bank, 'kredit' => 294]);
        $this->assertDatabaseHas('coa_transactions', ['nomor_referensi' => $payment->nomor, 'coa_id' => $this->taxAccountId, 'kredit' => 6]);
        $this->assertStringContainsString('Administrasi', DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->where('coa_id', $second)->value('keterangan'));
    }

    public function test_batam_vehicle_payment_saves_account_breakdown_without_journal(): void
    {
        $this->withoutMiddleware();

        $expense = DB::table('akun_coa')->insertGetId(['saldo' => 0]);
        $payload = $this->vehiclePayload('batam');
        $payload['akun_details'] = [['akun_coa_id' => $expense, 'debit_kredit' => 'debit', 'nominal' => 250, 'memo' => 'Biaya STNK']];

        $this->post(route('pembayaran-aktivitas-lain.store'), $payload)
            ->assertRedirect(route('pembayaran-aktivitas-lain.index'));

        $payment = PembayaranAktivitasLain::firstOrFail();
        $this->assertSame(250, $payment->jumlah);
        $this->assertSame('Biaya STNK', $payment->akun_details[0]['memo']);
        $this->assertSame(0, DB::table('coa_transactions')->where('nomor_referensi', $payment->nomor)->count());
    }

    private function vehiclePayload(string $lokasi): array
    {
        return [
            'tanggal' => '2026-10-17',
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'lokasi' => $lokasi,
            'pph_persen' => '2',
            'pph_manual' => '0',
            'akun_pph_id' => $this->taxAccountId,
            'sub_jenis_kendaraan' => 'Plat',
            'nomor_polisi' => 'BP 1234 AA',
            'penerima' => 'Vendor',
            'keterangan' => 'Biaya kendaraan',
            'jumlah' => 100,
        ];
    }
}

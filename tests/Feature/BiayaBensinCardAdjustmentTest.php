<?php

namespace Tests\Feature;

use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use App\Services\BiayaBensinCardService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BiayaBensinCardAdjustmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kartu')->unique();
            $table->string('nama_kartu');
            $table->string('provider');
            $table->string('status');
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });
        Schema::create('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_kartu_bensin_batam_id');
            $table->string('nomor_transaksi')->nullable();
            $table->dateTime('tanggal');
            $table->string('tipe');
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    private function card(string $number, int $balance): MasterKartuBensinBatam
    {
        return MasterKartuBensinBatam::withoutEvents(fn () => MasterKartuBensinBatam::create([
            'nomor_kartu' => $number,
            'nama_kartu' => $number,
            'provider' => 'Test',
            'status' => 'aktif',
            'saldo' => $balance,
        ]));
    }

    private function expense(): BiayaBensin
    {
        $expense = new BiayaBensin;
        $expense->id = 168;

        return $expense;
    }

    public function test_removing_card_deletes_its_history_and_rebases_later_balances(): void
    {
        $card = $this->card('20000063107', 55000);
        $this->history($card, 'BBS-000168', 'berkurang', 30000, 100000, 70000);
        $this->history($card, 'BBS-000169', 'berkurang', 10000, 70000, 60000);
        $this->history($card, 'BBS-000168', 'berkurang', 5000, 60000, 55000);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->removeUsage($this->expense(), $card->nomor_kartu)
        ));

        $this->assertSame('90000.00', $card->fresh()->saldo);
        $history = $card->histories()->sole();
        $this->assertSame('BBS-000169', $history->nomor_transaksi);
        $this->assertSame('100000.00', $history->saldo_sebelum);
        $this->assertSame('90000.00', $history->saldo_sesudah);
    }

    public function test_changing_card_moves_debit_to_new_card(): void
    {
        $oldCard = $this->card('OLD', 50000);
        $newCard = $this->card('NEW', 100000);
        $this->history($oldCard, 'BBS-000168', 'berkurang', 50000, 100000, 50000);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(function () {
            $service = app(BiayaBensinCardService::class);
            $service->removeUsage($this->expense(), 'OLD');
            $service->adjust($this->expense(), null, 0, 'NEW', 50000, 'Pengisian bensin setelah perubahan kartu BBS-000168');
        }));

        $this->assertSame('100000.00', $oldCard->fresh()->saldo);
        $this->assertSame('50000.00', $newCard->fresh()->saldo);
        $this->assertSame(0, $oldCard->histories()->count());
        $this->assertSame('berkurang', $newCard->histories()->sole()->tipe);
    }

    public function test_removing_legacy_card_usage_matches_created_audit(): void
    {
        $card = $this->card('OLD', 70000);
        $history = $this->history($card, null, 'berkurang', 30000, 100000, 70000);
        DB::table('master_kartu_bensin_batam_histories')->where('id', $history->id)->update([
            'created_at' => '2026-09-18 09:26:04', 'created_by' => 17,
        ]);
        DB::table('audit_logs')->insert([
            'auditable_type' => BiayaBensin::class,
            'auditable_id' => 168,
            'action' => 'created',
            'new_values' => json_encode(['nomor_kartu' => 'OLD', 'biaya' => 30000, 'created_by' => 17]),
            'created_at' => '2026-09-18 09:26:04',
        ]);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->removeUsage($this->expense(), 'OLD')
        ));

        $this->assertSame(0, $card->histories()->count());
        $this->assertSame('100000.00', $card->fresh()->saldo);
    }

    private function history(
        MasterKartuBensinBatam $card,
        ?string $number,
        string $type,
        int $amount,
        int $before,
        int $after
    ) {
        return $card->histories()->create([
            'nomor_transaksi' => $number,
            'tanggal' => now(),
            'tipe' => $type,
            'nominal' => $amount,
            'saldo_sebelum' => $before,
            'saldo_sesudah' => $after,
            'keterangan' => 'Pengisian bensin Kendaraan TEST',
        ]);
    }

    public function test_changing_amount_on_same_card_records_only_difference(): void
    {
        $card = $this->card('SAME', 100000);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->adjust($this->expense(), 'SAME', 30000, 'SAME', 20000, 'Koreksi biaya bensin BBS-000168')
        ));

        $this->assertSame('110000.00', $card->fresh()->saldo);
        $this->assertSame('10000.00', $card->histories()->sole()->nominal);
    }

    public function test_unavailable_new_card_rolls_back_old_card_credit(): void
    {
        $oldCard = $this->card('OLD', 50000);

        try {
            MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
                app(BiayaBensinCardService::class)->adjust($this->expense(), 'OLD', 50000, 'MISSING', 50000, 'Koreksi biaya bensin BBS-000168')
            ));
            $this->fail('Expected a validation error for the missing card.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nomor_kartu', $e->errors());
        }

        $this->assertSame('50000.00', $oldCard->fresh()->saldo);
        $this->assertSame(0, $oldCard->histories()->count());
    }

    public function test_unchanged_card_and_amount_do_not_add_history(): void
    {
        $card = $this->card('SAME', 50000);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->adjust($this->expense(), 'SAME', 30000, 'SAME', 30000, 'Koreksi biaya bensin BBS-000168')
        ));

        $this->assertSame('50000.00', $card->fresh()->saldo);
        $this->assertSame(0, $card->histories()->count());
    }
}

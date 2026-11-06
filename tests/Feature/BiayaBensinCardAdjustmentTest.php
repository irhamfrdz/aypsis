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

    public function test_removing_card_restores_balance_and_records_correction(): void
    {
        $card = $this->card('20000063107', -3502500);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->adjust($this->expense(), $card->nomor_kartu, 3000000, null, 0, 'Koreksi biaya bensin BBS-000168')
        ));

        $this->assertSame('-502500.00', $card->fresh()->saldo);
        $history = $card->histories()->sole();
        $this->assertSame('bertambah', $history->tipe);
        $this->assertSame('3000000.00', $history->nominal);
        $this->assertSame('BBS-000168', $history->nomor_transaksi);
    }

    public function test_changing_card_moves_debit_to_new_card(): void
    {
        $oldCard = $this->card('OLD', 50000);
        $newCard = $this->card('NEW', 100000);

        MasterKartuBensinBatam::withoutEvents(fn () => DB::transaction(fn () =>
            app(BiayaBensinCardService::class)->adjust($this->expense(), 'OLD', 50000, 'NEW', 50000, 'Koreksi biaya bensin BBS-000168')
        ));

        $this->assertSame('100000.00', $oldCard->fresh()->saldo);
        $this->assertSame('50000.00', $newCard->fresh()->saldo);
        $this->assertSame('bertambah', $oldCard->histories()->sole()->tipe);
        $this->assertSame('berkurang', $newCard->histories()->sole()->tipe);
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

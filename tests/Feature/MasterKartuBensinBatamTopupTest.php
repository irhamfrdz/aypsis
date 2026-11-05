<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterKartuBensinBatamController;
use App\Models\MasterKartuBensinBatam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterKartuBensinBatamTopupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kartu');
            $table->string('nama_kartu');
            $table->string('provider');
            $table->string('status');
            $table->decimal('saldo', 15, 2);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_kartu_bensin_batam_id');
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

    public function test_topup_increases_card_balance_and_records_history(): void
    {
        $card = MasterKartuBensinBatam::withoutEvents(fn () => MasterKartuBensinBatam::create([
            'nomor_kartu' => 'BBM-001',
            'nama_kartu' => 'Kartu Batam',
            'provider' => 'Pertamina',
            'status' => 'aktif',
            'saldo' => 100000,
        ]));

        MasterKartuBensinBatam::withoutEvents(fn () => (new MasterKartuBensinBatamController)->topup(
            Request::create('/topup', 'POST', [
                'tanggal' => '2026-11-05',
                'nominal' => 250000,
                'keterangan' => 'Isi ulang untuk operasional',
            ]),
            $card->id
        ));

        $this->assertSame('350000.00', $card->fresh()->saldo);
        $history = $card->histories()->sole();
        $this->assertSame('bertambah', $history->tipe);
        $this->assertSame('250000.00', $history->nominal);
        $this->assertSame('100000.00', $history->saldo_sebelum);
        $this->assertSame('350000.00', $history->saldo_sesudah);
        $this->assertSame('2026-11-05', $history->tanggal->toDateString());
        $this->assertSame('Isi ulang untuk operasional', $history->keterangan);
    }
}

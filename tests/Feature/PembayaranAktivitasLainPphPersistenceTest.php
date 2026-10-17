<?php

namespace Tests\Feature;

use App\Models\PembayaranAktivitasLain;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranAktivitasLainPphPersistenceTest extends TestCase
{
    public function test_migration_backfills_existing_payments_and_model_updates_totals(): void
    {
        Schema::create('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_aktivitas');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('pph_persen', 3, 1)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $existingId = DB::table('pembayaran_aktivitas_lains')->insertGetId([
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'jumlah' => 100000,
            'pph_persen' => 2.5,
        ]);

        $migration = require base_path('database/migrations/2026_10_17_000003_add_pph_totals_to_pembayaran_aktivitas_lains_table.php');
        $migration->up();
        $manualMigration = require base_path('database/migrations/2026_10_17_000004_add_pph_manual_to_pembayaran_aktivitas_lains_table.php');
        $manualMigration->up();

        $existing = PembayaranAktivitasLain::findOrFail($existingId);
        $this->assertSame(2500, $existing->pph_nominal);
        $this->assertSame(97500, $existing->grand_total);

        $payment = PembayaranAktivitasLain::create([
            'jenis_aktivitas' => 'Pembayaran Kendaraan',
            'jumlah' => 100000,
            'pph_persen' => 2,
            'pph_nominal' => 9999,
            'grand_total' => 1,
        ])->fresh();
        $this->assertSame(2000, $payment->pph_nominal);
        $this->assertSame(98000, $payment->grand_total);

        $payment->update(['jumlah' => 200000]);
        $this->assertSame(4000, $payment->fresh()->pph_nominal);
        $this->assertSame(196000, $payment->fresh()->grand_total);

        $payment->update(['pph_manual' => true, 'pph_nominal' => 3500]);
        $this->assertSame(3500, $payment->fresh()->pph_nominal);
        $this->assertSame(196500, $payment->fresh()->grand_total);

        $payment->update(['keterangan' => 'Revisi keterangan']);
        $this->assertSame(3500, $payment->fresh()->pph_nominal);

        $payment->update(['jumlah' => 300000, 'pph_manual' => false]);
        $this->assertSame(6000, $payment->fresh()->pph_nominal);
        $this->assertSame(294000, $payment->fresh()->grand_total);

        $payment->update(['jenis_aktivitas' => 'Pembayaran Kapal']);
        $this->assertSame(0, $payment->fresh()->pph_nominal);
        $this->assertSame(300000, $payment->fresh()->grand_total);
    }
}

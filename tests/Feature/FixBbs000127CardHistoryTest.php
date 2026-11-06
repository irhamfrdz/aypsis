<?php

namespace Tests\Feature;

use App\Services\LegacyFuelCardHistoryRepair;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FixBbs000127CardHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
        });
        Schema::create('biaya_bensin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->string('nomor_kartu')->nullable();
            $table->decimal('biaya', 15, 2);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kartu');
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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        DB::table('mobils')->insert(['id' => 82, 'nomor_polisi' => 'BP8923ZH']);
        DB::table('biaya_bensin')->insert([
            'id' => 127, 'mobil_id' => 82, 'nomor_kartu' => null, 'biaya' => 669000,
            'created_by' => 197, 'created_at' => '2026-09-03 11:37:50', 'updated_at' => '2026-09-03 11:37:59',
        ]);
        DB::table('master_kartu_bensin_batams')->insert([
            'id' => 4, 'nomor_kartu' => '20000063108', 'saldo' => 231000,
        ]);
        DB::table('master_kartu_bensin_batam_histories')->insert([
            [
                'id' => 12, 'master_kartu_bensin_batam_id' => 4,
                'tanggal' => '2026-09-03 11:37:51', 'tipe' => 'berkurang', 'nominal' => 669000,
                'saldo_sebelum' => 1000000, 'saldo_sesudah' => 331000,
                'keterangan' => 'Pengisian bensin Kendaraan BP8923ZH', 'created_by' => 197,
                'created_at' => '2026-09-03 11:37:51', 'updated_at' => '2026-09-03 11:37:51',
            ],
            [
                'id' => 13, 'master_kartu_bensin_batam_id' => 4,
                'tanggal' => '2026-09-10 12:39:00', 'tipe' => 'berkurang', 'nominal' => 100000,
                'saldo_sebelum' => 331000, 'saldo_sesudah' => 231000,
                'keterangan' => 'Pengisian bensin Kendaraan LAIN', 'created_by' => 197,
                'created_at' => '2026-09-10 12:39:00', 'updated_at' => '2026-09-10 12:39:00',
            ],
        ]);
    }

    public function test_removes_stale_card_charge_and_rebases_later_balances_once(): void
    {
        DB::table('audit_logs')->insert([
            'auditable_type' => 'App\\Models\\BiayaBensin', 'auditable_id' => 127,
            'action' => 'updated',
            'old_values' => json_encode(['nomor_kartu' => '20000063108']),
            'new_values' => json_encode(['nomor_kartu' => null]),
        ]);

        $migration = require database_path('migrations/2026_11_06_140000_fix_bbs_000127_card_history.php');
        $migration->up();
        $migration->up();

        $this->assertDatabaseMissing('master_kartu_bensin_batam_histories', ['id' => 12]);
        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', [
            'id' => 13, 'saldo_sebelum' => 1000000, 'saldo_sesudah' => 900000,
        ]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 900000]);
    }

    public function test_does_not_change_card_without_audit_evidence(): void
    {
        $migration = require database_path('migrations/2026_11_06_140000_fix_bbs_000127_card_history.php');
        $migration->up();

        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', ['id' => 12]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 231000]);
    }

    public function test_legacy_repair_removes_a_charge_after_card_was_cleared(): void
    {
        $this->insertCreatedAudit();
        $this->insertChangeAudit('updated', ['nomor_kartu' => '20000063108', 'biaya' => 669000], [
            'nomor_kartu' => null, 'biaya' => 669000,
        ]);

        DB::transaction(function () {
            $repair = app(LegacyFuelCardHistoryRepair::class);
            $repair->repair(127, '20000063108', 669000, null, 'removed', 'Pengisian bensin Kendaraan BP8923ZH');
            $repair->repair(127, '20000063108', 669000, null, 'removed', 'Pengisian bensin Kendaraan BP8923ZH');
        });

        $this->assertDatabaseMissing('master_kartu_bensin_batam_histories', ['id' => 12]);
        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', [
            'id' => 13, 'saldo_sebelum' => 1000000, 'saldo_sesudah' => 900000,
        ]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 900000]);
    }

    public function test_legacy_repair_adjusts_original_charge_after_amount_changed(): void
    {
        DB::table('biaya_bensin')->where('id', 127)->update(['nomor_kartu' => '20000063108', 'biaya' => 670000]);
        $this->insertCreatedAudit();
        $this->insertChangeAudit('updated', ['nomor_kartu' => '20000063108', 'biaya' => 669000], [
            'nomor_kartu' => '20000063108', 'biaya' => 670000,
        ]);

        DB::transaction(fn () => app(LegacyFuelCardHistoryRepair::class)
            ->repair(127, '20000063108', 669000, 670000, 'amount', 'Pengisian bensin Kendaraan BP8923ZH'));

        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', [
            'id' => 12, 'nominal' => 670000, 'saldo_sebelum' => 1000000, 'saldo_sesudah' => 330000,
        ]);
        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', [
            'id' => 13, 'saldo_sebelum' => 330000, 'saldo_sesudah' => 230000,
        ]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 230000]);
    }

    public function test_legacy_repair_removes_charge_after_expense_was_deleted(): void
    {
        DB::table('biaya_bensin')->where('id', 127)->delete();
        $this->insertCreatedAudit();
        $this->insertChangeAudit('deleted', ['nomor_kartu' => '20000063108', 'biaya' => 669000], null);

        DB::transaction(fn () => app(LegacyFuelCardHistoryRepair::class)
            ->repair(127, '20000063108', 669000, null, 'deleted', 'Pengisian bensin Kendaraan BP8923ZH'));

        $this->assertDatabaseMissing('master_kartu_bensin_batam_histories', ['id' => 12]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 900000]);
    }

    public function test_backfills_history_number_even_when_amount_and_plate_changed(): void
    {
        DB::table('biaya_bensin')->insert([
            'id' => 14, 'mobil_id' => 82, 'nomor_kartu' => '20000063108', 'biaya' => 200000,
            'created_by' => 155, 'created_at' => '2026-07-28 15:10:35', 'updated_at' => '2026-07-28 15:11:10',
        ]);
        DB::table('master_kartu_bensin_batam_histories')->insert([
            'id' => 1, 'master_kartu_bensin_batam_id' => 4,
            'tanggal' => '2026-07-28 15:10:35', 'tipe' => 'berkurang', 'nominal' => 200000,
            'saldo_sebelum' => 0, 'saldo_sesudah' => -200000,
            'keterangan' => 'Pengisian bensin Kendaraan BP9250ZN', 'created_by' => 155,
            'created_at' => '2026-07-28 15:10:35', 'updated_at' => '2026-07-28 15:10:35',
        ]);
        DB::table('audit_logs')->insert([
            'auditable_type' => 'App\\Models\\BiayaBensin', 'auditable_id' => 14,
            'action' => 'created', 'new_values' => json_encode([
                'nomor_kartu' => '20000063108', 'biaya' => 2000000, 'created_by' => 155,
            ]),
            'created_at' => '2026-07-28 15:10:35',
        ]);

        $migration = require database_path('migrations/2026_11_06_160000_backfill_fuel_card_history_numbers.php');
        $migration->up();
        $migration->up();

        $this->assertDatabaseHas('master_kartu_bensin_batam_histories', [
            'id' => 1, 'nomor_transaksi' => 'BBS-000014', 'nominal' => 200000,
            'saldo_sebelum' => 0, 'saldo_sesudah' => -200000,
        ]);
        $this->assertDatabaseHas('master_kartu_bensin_batams', ['id' => 4, 'saldo' => 231000]);
    }

    private function insertCreatedAudit(): void
    {
        DB::table('audit_logs')->insert([
            'auditable_type' => 'App\\Models\\BiayaBensin', 'auditable_id' => 127,
            'action' => 'created', 'new_values' => json_encode([
                'nomor_kartu' => '20000063108', 'biaya' => 669000, 'created_by' => 197,
            ]),
            'created_at' => '2026-09-03 11:37:50',
        ]);
    }

    private function insertChangeAudit(string $action, array $old, ?array $new): void
    {
        DB::table('audit_logs')->insert([
            'auditable_type' => 'App\\Models\\BiayaBensin', 'auditable_id' => 127,
            'action' => $action, 'old_values' => json_encode($old), 'new_values' => json_encode($new),
            'created_at' => '2026-09-03 11:37:59',
        ]);
    }
}

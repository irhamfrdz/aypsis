<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapPemakaianBanController;
use App\Models\StockBan;
use App\Models\StockBanLuarBatam;
use App\Services\StockBanUsageHistoryService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockBanUsageHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->nullable();
            $table->string('no_kir')->nullable();
        });
        Schema::create('stock_bans', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->date('tanggal_digunakan')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('nomor_bukti_pakai')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_ban_luar_batams', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_ban_usage_histories', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('audit_log_id')->nullable()->unique();
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable();
            $table->date('tanggal_digunakan');
            $table->string('unit_name')->nullable();
            $table->string('nama_ban')->nullable();
            $table->string('merk')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('nomor_bukti_pakai')->nullable();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->string('penerima_name')->nullable();
            $table->text('keterangan')->nullable();
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
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_installations_remain_in_vehicle_history_after_return_and_reuse(): void
    {
        $mobilId = DB::table('mobils')->insertGetId(['nomor_polisi' => 'B 1234 ABC']);
        $ban = StockBan::create(['status' => 'Stok', 'nomor_seri' => 'BAN-01']);

        $ban->update(['status' => 'Terpakai', 'mobil_id' => $mobilId, 'tanggal_keluar' => '2026-01-10', 'tanggal_digunakan' => '2026-01-10']);
        $ban->update(['status' => 'Stok', 'mobil_id' => null, 'tanggal_keluar' => null]);
        $ban->update(['status' => 'Terpakai', 'mobil_id' => $mobilId, 'tanggal_keluar' => '2026-03-10', 'tanggal_digunakan' => '2026-03-10']);

        $dates = DB::table('stock_ban_usage_histories')->orderBy('tanggal_digunakan')->pluck('tanggal_digunakan')->all();
        $this->assertSame(['2026-01-10', '2026-03-10'], $dates);

        $data = (new RekapPemakaianBanController)->index(Request::create('/', 'GET', [
            'start_date' => '2026-01-01', 'end_date' => '2026-10-06', 'unit' => $mobilId,
        ]))->getData();
        $this->assertSame(2, $data['items']->total());
    }

    public function test_backfill_restores_a_past_installation_from_audit_log(): void
    {
        $mobilId = DB::table('mobils')->insertGetId(['nomor_polisi' => 'B 5678 XYZ']);
        DB::table('audit_logs')->insert([
            'auditable_type' => StockBan::class, 'auditable_id' => 9, 'action' => 'created',
            'new_values' => json_encode(['status' => 'Stok', 'nomor_seri' => 'BAN-LAMA']),
            'created_at' => '2026-01-01 08:00:00',
        ]);
        DB::table('audit_logs')->insert([
            'auditable_type' => StockBan::class, 'auditable_id' => 9, 'action' => 'updated',
            'old_values' => json_encode(['status' => 'Stok', 'mobil_id' => null]),
            'new_values' => json_encode(['status' => 'Terpakai', 'mobil_id' => $mobilId, 'tanggal_digunakan' => '2026-02-01']),
            'created_at' => '2026-02-01 08:00:00',
        ]);
        DB::table('audit_logs')->insert([
            'auditable_type' => StockBan::class, 'auditable_id' => 9, 'action' => 'updated',
            'old_values' => json_encode(['status' => 'Terpakai', 'mobil_id' => $mobilId]),
            'new_values' => json_encode(['status' => 'Stok', 'mobil_id' => null]),
            'created_at' => '2026-03-01 08:00:00',
        ]);

        (new StockBanUsageHistoryService)->backfill();
        (new StockBanUsageHistoryService)->backfill();

        $this->assertDatabaseHas('stock_ban_usage_histories', [
            'source_id' => 9, 'mobil_id' => $mobilId, 'tanggal_digunakan' => '2026-02-01', 'nomor_seri' => 'BAN-LAMA',
        ]);
        $this->assertSame(1, DB::table('stock_ban_usage_histories')->count());
    }

    public function test_batam_installation_is_recorded_too(): void
    {
        $mobilId = DB::table('mobils')->insertGetId(['nomor_polisi' => 'BP 1234 A']);
        $ban = StockBanLuarBatam::create(['status' => 'Stok', 'nomor_seri' => 'BTM-01']);
        $ban->update(['status' => 'Terpakai', 'mobil_id' => $mobilId, 'tanggal_keluar' => '2026-05-01']);

        $this->assertDatabaseHas('stock_ban_usage_histories', [
            'source_type' => 'stock_ban_luar_batams', 'source_id' => $ban->id,
            'mobil_id' => $mobilId, 'tanggal_digunakan' => '2026-05-01',
        ]);
    }
}

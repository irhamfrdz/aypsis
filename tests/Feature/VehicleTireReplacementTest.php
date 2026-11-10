<?php

namespace Tests\Feature;

use App\Services\VehicleTireReplacementService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VehicleTireReplacementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_11_10_020000_create_pemakaian_ban_table.php'))->up();
    }

    private function usage(array $overrides = []): void
    {
        DB::table('pemakaian_ban')->insert(array_replace([
            'stock_table' => 'stock_bans', 'stock_id' => 1,
            'jenis_komponen' => 'ban', 'mobil_id' => 1,
            'tanggal_digunakan' => '2026-10-01', 'qty' => 1,
            'nama_barang' => 'Ban', 'unit_name' => 'B1234',
        ], $overrides));
    }

    public function test_counts_all_components_and_quantities_with_period_vehicle_and_deletion_filters(): void
    {
        foreach (['ban', 'ban_dalam', 'ban_perut', 'ring_velg', 'velg'] as $kind) {
            $this->usage(['jenis_komponen' => $kind, 'qty' => 2]);
        }
        $this->usage(['tanggal_digunakan' => '2026-10-31']);
        $this->usage(['tanggal_digunakan' => '2026-09-30']);
        $this->usage(['tanggal_digunakan' => '2026-11-01']);
        $this->usage(['deleted_at' => '2026-10-02']);
        $this->usage(['mobil_id' => 2]);
        $this->usage(['jenis_komponen' => 'cat']);
        $service = app(VehicleTireReplacementService::class);
        $summary = $service->summarize([1], '2026-10-01', '2026-10-31');
        $this->assertSame(6, $summary[1]['replacement_count']);
        $this->assertSame(11, $summary[1]['tire_count']);
        $this->assertCount(6, $summary[1]['details']);
        $this->assertTrue($service->summarize([], '2026-10-01', '2026-10-31')->isEmpty());
    }

    public function test_counts_vehicle_usage_regardless_of_receiver_and_renders_details_safely(): void
    {
        $this->usage(['mobil_id' => 1, 'penerima_id' => 10, 'penerima_name' => 'BENI',
            'jenis_komponen' => 'ring_velg', 'nama_barang' => 'Ring Velg', 'qty' => 3,
            'keterangan' => '<script>alert(1)</script>']);
        $this->usage(['mobil_id' => 9, 'penerima_name' => ' beni ']);
        $this->usage(['mobil_id' => 1, 'penerima_id' => 20]);
        $this->usage();
        $summary = app(VehicleTireReplacementService::class)->summarize([1], '2026-10-01', '2026-10-31')[1];
        $this->assertSame(3, $summary['replacement_count']);
        $this->assertSame(5, $summary['tire_count']);
        $html = view('dashboard-perjalanan-kendaraan.partials.tire-details', ['tireDetails' => $summary['details']])->render();
        $this->assertStringContainsString('Ring Velg', $html);
        $this->assertStringContainsString('3 unit', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('Tidak ada pergantian ban dan velg', view('dashboard-perjalanan-kendaraan.partials.tire-details', ['tireDetails' => collect()])->render());
    }
    public function test_component_usage_is_synchronized_on_create_date_change_and_delete(): void
    {
        \Illuminate\Support\Facades\Schema::create('stock_ban_usage_histories', function ($t) { $t->id(); });
        foreach (['stock_ban_dalams', 'stock_velgs', 'stock_ring_velgs'] as $name) {
            \Illuminate\Support\Facades\Schema::create($name, function ($t) {
                $t->id(); $t->unsignedBigInteger('nama_stock_ban_id'); $t->string('ukuran')->nullable();
            });
        }
        \Illuminate\Support\Facades\Schema::create('nama_stock_bans', function ($t) { $t->id(); $t->string('nama'); });
        \Illuminate\Support\Facades\Schema::create('mobils', function ($t) { $t->id(); $t->string('nomor_polisi'); });
        \Illuminate\Support\Facades\Schema::create('stock_ban_dalam_usages', function ($t) {
            $t->id();
            foreach (['stock_ban_dalam_id', 'mobil_id', 'penerima_id', 'kapal_id', 'gudang_id'] as $column) { $t->unsignedBigInteger($column)->nullable(); }
            $t->integer('qty'); $t->date('tanggal_keluar'); $t->date('tanggal_digunakan')->nullable();
            $t->string('penerima_manual')->nullable(); $t->text('keterangan')->nullable(); $t->timestamps();
        });
        DB::table('nama_stock_bans')->insert(['id' => 1, 'nama' => 'Ring Velg']);
        DB::table('stock_ring_velgs')->insert(['id' => 1, 'nama_stock_ban_id' => 1, 'ukuran' => '8']);
        DB::table('mobils')->insert(['id' => 1, 'nomor_polisi' => 'B1234']);
        $usage = \App\Models\StockBanDalamUsage::create([
            'qty' => 3, 'mobil_id' => 1, 'tanggal_keluar' => '2026-10-01', 'keterangan' => '[Ring Velg ID: 1] test',
        ]);
        $this->assertDatabaseHas('pemakaian_ban', ['source_id' => $usage->id, 'jenis_komponen' => 'ring_velg', 'qty' => 3]);
        $usage->update(['tanggal_keluar' => '2026-10-05']);
        $this->assertDatabaseHas('pemakaian_ban', ['source_id' => $usage->id, 'tanggal_digunakan' => '2026-10-05']);
        $this->assertSame(1, DB::table('pemakaian_ban')->count());
        $usage->delete();
        $this->assertNotNull(DB::table('pemakaian_ban')->value('deleted_at'));
        $this->assertTrue(app(VehicleTireReplacementService::class)->summarize([1], '2026-10-01', '2026-10-31')->isEmpty());
    }

}

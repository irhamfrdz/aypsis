<?php

namespace Tests\Feature;

use App\Http\Controllers\StockBanController;
use App\Models\NamaStockBan;
use App\Models\StockBanDalam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockBanMasterDefaultsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Auth::shouldReceive('id')->andReturn(1);
        foreach ([
            '2026_01_23_133500_create_nama_stock_bans_table.php',
            '2026_10_31_110000_add_kuantitas_to_nama_stock_bans_table.php',
            '2026_10_31_120000_add_type_to_nama_stock_bans_table.php',
            '2026_01_26_151000_create_stock_ban_dalams_table.php',
        ] as $file) {
            (require database_path('migrations/'.$file))->up();
        }
        Schema::table('stock_ban_dalams', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
    }

    private function createStock(NamaStockBan $master, array $overrides = []): StockBanDalam
    {
        $response = (new StockBanController)->store(Request::create('/stock-ban', 'POST', array_merge([
            'nama_stock_ban_id' => $master->id,
            'tanggal_masuk' => '2026-10-31',
            'harga_beli' => 1000,
            'lokasi' => 'Ruko 10',
        ], $overrides)));
        $this->assertTrue($response->getSession()->has('success'));

        return StockBanDalam::latest('id')->firstOrFail();
    }

    public function test_pumps_and_pulley_use_their_own_master_quantity_and_type(): void
    {
        $items = [
            ['POMPA AIR ALKON BESAR', 2, 'Unit Besar'],
            ['POMPA AIR ALKON KECIL', 3, 'Unit Kecil'],
            ['PULLEY CRANE', 7, 'Set'],
        ];
        foreach ($items as [$name, $qty, $type]) {
            $master = NamaStockBan::withoutEvents(fn () => NamaStockBan::create([
                'nama' => $name, 'status' => 'active', 'kuantitas' => $qty, 'type' => $type,
            ]));
            $stock = $this->createStock($master, ['qty' => 999, 'type' => 'wrong', 'ukuran' => 'stale hidden value']);
            $this->assertSame($master->id, $stock->nama_stock_ban_id);
            $this->assertSame($qty, $stock->qty);
            $this->assertSame($type, $stock->type);
            $this->assertNull($stock->ukuran);
            $this->assertSame($qty, $master->fresh()->kuantitas);
        }
    }

    public function test_zero_master_quantity_and_missing_type_use_zero_and_pcs(): void
    {
        $master = NamaStockBan::withoutEvents(fn () => NamaStockBan::create([
            'nama' => ' pulley   crane ', 'status' => 'active', 'kuantitas' => 0, 'type' => null,
        ]));
        $stock = $this->createStock($master);
        $this->assertSame(0, $stock->qty);
        $this->assertSame('pcs', $stock->type);
    }

    public function test_existing_ban_dalam_and_paint_keep_their_entered_quantity_and_type(): void
    {
        foreach (['BAN DALAM', 'CAT PUTIH'] as $name) {
            $master = NamaStockBan::withoutEvents(fn () => NamaStockBan::create([
                'nama' => $name, 'status' => 'active', 'kuantitas' => 50, 'type' => 'Master Type',
            ]));
            $stock = $this->createStock($master, ['qty' => 4, 'type' => 'pail']);
            $this->assertSame(4, $stock->qty);
            $this->assertSame('pail', $stock->type);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\NamaStockBanController;
use App\Models\NamaStockBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NamaStockBanQuantityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_01_23_133500_create_nama_stock_bans_table.php'))->up();
        (require database_path('migrations/2026_10_31_110000_add_kuantitas_to_nama_stock_bans_table.php'))->up();
    }

    public function test_quantity_is_saved_updated_and_can_be_zero(): void
    {
        NamaStockBan::withoutEvents(function () {
            $controller = new NamaStockBanController;
            $payload = ['nama' => 'Ban Dalam', 'status' => 'active', 'kuantitas' => 25];
            $controller->store(Request::create('/', 'POST', $payload));
            $item = NamaStockBan::firstOrFail();
            $this->assertSame(25, $item->kuantitas);

            $payload['kuantitas'] = 0;
            $controller->update(Request::create('/', 'PUT', $payload), (string) $item->id);
            $this->assertSame(0, $item->fresh()->kuantitas);
        });
    }

    public function test_invalid_quantities_are_rejected(): void
    {
        foreach ([-1, '1.5', 'abc', '', '4294967296'] as $quantity) {
            try {
                (new NamaStockBanController)->store(Request::create('/', 'POST', [
                    'nama' => 'Ban Dalam', 'status' => 'active', 'kuantitas' => $quantity,
                ]));
                $this->fail('Invalid quantity accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('kuantitas', $exception->errors());
            }
        }
        $this->assertSame(0, NamaStockBan::count());
    }

    public function test_migration_defaults_existing_records_to_zero_and_can_be_rolled_back(): void
    {
        $migration = require database_path('migrations/2026_10_31_110000_add_kuantitas_to_nama_stock_bans_table.php');
        $migration->down();
        $id = DB::table('nama_stock_bans')->insertGetId(['nama' => 'Ban Lama', 'status' => 'active']);
        $migration->up();
        $this->assertSame(0, NamaStockBan::findOrFail($id)->kuantitas);
        $migration->down();
        $this->assertSame('Ban Lama', DB::table('nama_stock_bans')->find($id)->nama);
    }
}

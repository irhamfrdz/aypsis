<?php

namespace Tests\Feature;

use App\Http\Controllers\StockBanController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockBanVelgHistoryTest extends TestCase
{
    public function test_velg_history_uses_velg_record_even_when_another_stock_item_has_the_same_id(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('nama_stock_bans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->softDeletes();
        });
        foreach (['stock_velgs', 'stock_ring_velgs', 'stock_ban_dalams'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('nama_stock_ban_id');
                $table->integer('qty')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
        Schema::create('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_ban_dalam_id')->nullable();
            $table->integer('qty');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->date('tanggal_digunakan')->nullable();
            $table->timestamps();
        });

        $catId = DB::table('nama_stock_bans')->insertGetId(['nama' => 'CAT BIRU']);
        $velgId = DB::table('nama_stock_bans')->insertGetId(['nama' => 'VELG LOBANG 8']);
        $ringVelgId = DB::table('nama_stock_bans')->insertGetId(['nama' => 'RING VELG LOBANG 8']);
        DB::table('stock_ban_dalams')->insert(['id' => 1, 'nama_stock_ban_id' => $catId]);
        DB::table('stock_velgs')->insert(['id' => 1, 'nama_stock_ban_id' => $velgId]);
        DB::table('stock_ring_velgs')->insert(['id' => 1, 'nama_stock_ban_id' => $ringVelgId]);
        DB::table('stock_ban_dalam_usages')->insert([
            ['qty' => 2, 'stock_ban_dalam_id' => null, 'keterangan' => '[Velg ID: 1] Dipakai'],
            ['qty' => 5, 'stock_ban_dalam_id' => 1, 'keterangan' => 'Cat dipakai'],
            ['qty' => 3, 'stock_ban_dalam_id' => null, 'keterangan' => '[Ring Velg ID: 1] Dipakai'],
        ]);

        $view = app(StockBanController::class)->showVelgHistory('velg', 1);

        $this->assertSame('VELG LOBANG 8', $view->getData()['item']->namaStockBan->nama);
        $this->assertCount(1, $view->getData()['usages']);
        $this->assertSame('[Velg ID: 1] Dipakai', $view->getData()['usages']->first()->keterangan);

        app(StockBanController::class)->updateVelgUsageDate(
            Request::create('/', 'PATCH', ['tanggal_keluar' => '2026-09-30']), 'velg', 1, 1
        );
        $this->assertDatabaseHas('stock_ban_dalam_usages', [
            'id' => 1, 'tanggal_keluar' => '2026-09-30', 'tanggal_digunakan' => '2026-09-30',
        ]);

        app(StockBanController::class)->destroyVelgUsage('velg', 1, 1);
        $this->assertDatabaseHas('stock_velgs', ['id' => 1, 'qty' => 2]);
        $this->assertDatabaseMissing('stock_ban_dalam_usages', ['id' => 1]);
        $this->assertDatabaseHas('stock_ban_dalam_usages', ['id' => 2, 'keterangan' => 'Cat dipakai']);

        $ringView = app(StockBanController::class)->showVelgHistory('ring-velg', 1);
        $this->assertSame('RING VELG LOBANG 8', $ringView->getData()['item']->namaStockBan->nama);
        $this->assertCount(1, $ringView->getData()['usages']);
        app(StockBanController::class)->destroyVelgUsage('ring-velg', 1, 3);
        $this->assertDatabaseHas('stock_ring_velgs', ['id' => 1, 'qty' => 3]);
        $this->assertDatabaseHas('stock_ban_dalams', ['id' => 1, 'qty' => 0]);
    }
}

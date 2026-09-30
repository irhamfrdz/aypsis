<?php

namespace Tests\Feature;

use App\Http\Controllers\StockBanController;
use Illuminate\Database\Schema\Blueprint;
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
        foreach (['stock_velgs', 'stock_ban_dalams'] as $tableName) {
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
            $table->timestamps();
        });

        $catId = DB::table('nama_stock_bans')->insertGetId(['nama' => 'CAT BIRU']);
        $velgId = DB::table('nama_stock_bans')->insertGetId(['nama' => 'VELG LOBANG 8']);
        DB::table('stock_ban_dalams')->insert(['id' => 1, 'nama_stock_ban_id' => $catId]);
        DB::table('stock_velgs')->insert(['id' => 1, 'nama_stock_ban_id' => $velgId]);
        DB::table('stock_ban_dalam_usages')->insert([
            ['qty' => 2, 'stock_ban_dalam_id' => null, 'keterangan' => '[Velg ID: 1] Dipakai'],
            ['qty' => 5, 'stock_ban_dalam_id' => 1, 'keterangan' => 'Cat dipakai'],
        ]);

        $view = app(StockBanController::class)->showVelgHistory('velg', 1);

        $this->assertSame('VELG LOBANG 8', $view->getData()['item']->namaStockBan->nama);
        $this->assertCount(1, $view->getData()['usages']);
        $this->assertSame('[Velg ID: 1] Dipakai', $view->getData()['usages']->first()->keterangan);
    }
}

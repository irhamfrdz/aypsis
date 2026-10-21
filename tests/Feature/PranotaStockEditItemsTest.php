<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaStockEditItemsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        Schema::create('pranota_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->date('tanggal_pranota');
            $table->string('lokasi');
            $table->string('nomor_accurate')->nullable();
            $table->string('vendor')->nullable();
            $table->string('bank')->nullable();
            $table->string('rekening')->nullable();
            $table->string('penerima')->nullable();
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->json('items');
            $table->string('status')->default('approved');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('master_nama_barang_amprahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
        });
        Schema::create('stock_amprahans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bukti')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->string('type_amprahan')->nullable();
            $table->string('status_pranota')->nullable();
            $table->unsignedBigInteger('master_nama_barang_amprahan_id')->nullable();
            $table->string('nama_barang')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('satuan')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_amprahan_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_amprahan_id');
            $table->decimal('jumlah', 15, 2);
            $table->softDeletes();
        });

        DB::table('pranota_stocks')->insert([
            'id' => 1,
            'nomor_pranota' => 'PTP-001',
            'tanggal_pranota' => '2026-10-21',
            'lokasi' => 'Jakarta',
            'items' => json_encode([['id' => 1, 'nama_barang' => 'Barang Lama', 'harga' => 10, 'jumlah' => 5, 'adjustment' => 0]]),
        ]);
        DB::table('stock_amprahans')->insert([
            ['id' => 1, 'nomor_bukti' => 'B-1', 'nama_barang' => 'Barang Lama', 'status_pranota' => 'Sudah', 'harga_satuan' => 10, 'adjustment' => 0, 'jumlah' => 5, 'lokasi' => 'KANTOR AYP JAKARTA'],
            ['id' => 2, 'nomor_bukti' => 'B-2', 'nama_barang' => 'Barang Baru', 'status_pranota' => 'Belum', 'harga_satuan' => 20, 'adjustment' => 1, 'jumlah' => 3, 'lokasi' => 'KANTOR AYP JAKARTA'],
            ['id' => 3, 'nomor_bukti' => 'B-3', 'nama_barang' => 'Barang Terpakai', 'status_pranota' => 'Sudah', 'harga_satuan' => 30, 'adjustment' => 0, 'jumlah' => 1, 'lokasi' => 'KANTOR AYP JAKARTA'],
            ['id' => 4, 'nomor_bukti' => 'B-4', 'nama_barang' => 'Barang Batam', 'status_pranota' => 'Belum', 'harga_satuan' => 40, 'adjustment' => 0, 'jumlah' => 1, 'lokasi' => 'KANTOR AYP BATAM'],
        ]);
        DB::table('stock_amprahan_usages')->insert(['stock_amprahan_id' => 2, 'jumlah' => 2]);
    }

    public function test_search_and_update_remove_and_add_items(): void
    {
        $this->getJson(route('pranota-stock.available-items', ['id' => 1, 'search' => 'Barang']))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', 2)
            ->assertJsonPath('items.0.jumlah', 5);

        $this->putJson(route('pranota-stock.update', 1), $this->payload([['id' => 2]]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $items = json_decode(DB::table('pranota_stocks')->where('id', 1)->value('items'), true);
        $this->assertCount(1, $items);
        $this->assertSame(2, $items[0]['id']);
        $this->assertSame(5, $items[0]['jumlah']);
        $this->assertEquals(20, $items[0]['harga']);
        $this->assertSame('Belum', DB::table('stock_amprahans')->where('id', 1)->value('status_pranota'));
        $this->assertSame('Sudah', DB::table('stock_amprahans')->where('id', 2)->value('status_pranota'));
    }

    public function test_unavailable_item_rejects_update_without_changing_existing_items(): void
    {
        $this->putJson(route('pranota-stock.update', 1), $this->payload([['id' => 1], ['id' => 3]]))
            ->assertStatus(500)
            ->assertJsonPath('success', false);

        $items = json_decode(DB::table('pranota_stocks')->where('id', 1)->value('items'), true);
        $this->assertCount(1, $items);
        $this->assertSame(1, $items[0]['id']);
        $this->assertSame('Sudah', DB::table('stock_amprahans')->where('id', 1)->value('status_pranota'));
    }

    private function payload(array $items): array
    {
        return [
            'nomor_pranota' => 'PTP-001',
            'tanggal_pranota' => '2026-10-21',
            'nomor_accurate' => null,
            'vendor' => null,
            'bank' => null,
            'rekening' => null,
            'penerima' => null,
            'adjustment' => 0,
            'keterangan' => null,
            'items' => $items,
        ];
    }
}

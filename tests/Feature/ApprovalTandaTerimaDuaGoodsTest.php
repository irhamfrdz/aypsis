<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApprovalTandaTerimaDuaGoodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->json('dimensi_items')->nullable();
            $table->json('dimensi_details')->nullable();
            $table->json('nama_barang')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran')->nullable();
            foreach (['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'] as $field) {
                $table->decimal($field, 15, 3)->nullable();
            }
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
            $table->integer('item_number')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('keterangan_barang')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran')->nullable();
            foreach (['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'] as $field) {
                $table->decimal($field, 15, 3)->nullable();
            }
            $table->timestamps();
        });

        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->integer('jumlah_barang')->nullable();
            $table->string('satuan_barang')->nullable();
            $table->string('ukuran')->nullable();
            $table->decimal('meter_kubik', 15, 3)->nullable();
            $table->decimal('tonase', 15, 3)->nullable();
            $table->text('keterangan_barang')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('tanda_terima_dimensi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_tanpa_surat_jalan_id');
            $table->integer('item_order')->nullable();
            $table->string('nama_barang')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran')->nullable();
            foreach (['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase'] as $field) {
                $table->decimal($field, 15, 3)->nullable();
            }
            $table->timestamps();
        });
    }

    public function test_fcl_goods_update_keeps_json_and_summary_in_sync(): void
    {
        $id = DB::table('tanda_terimas')->insertGetId([
            'dimensi_items' => json_encode([['nama_barang' => 'Lama', 'jumlah' => 1, 'satuan' => 'Koli']]),
            'dimensi_details' => json_encode([['nama_barang' => 'Lama', 'jumlah' => 1, 'satuan' => 'Koli']]),
            'nama_barang' => json_encode(['Lama']),
        ]);

        TandaTerima::withoutEvents(fn () => app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', ['goods' => [[
                'original_index' => 0, 'nama_barang' => 'Mesin', 'jumlah' => 2, 'satuan' => 'Unit', 'meter_kubik' => 3.5,
            ]]]), 'fcl', $id
        ));

        $receipt = TandaTerima::findOrFail($id);
        $this->assertSame('Mesin', $receipt->dimensi_items[0]['nama_barang']);
        $this->assertSame('Mesin', $receipt->dimensi_details[0]['nama_barang']);
        $this->assertSame(['Mesin'], $receipt->nama_barang);
        $this->assertSame(2, $receipt->jumlah);
    }

    public function test_lcl_goods_update_only_changes_items_of_selected_receipt(): void
    {
        $receiptId = DB::table('tanda_terimas_lcl')->insertGetId([]);
        $otherId = DB::table('tanda_terimas_lcl')->insertGetId([]);
        $keepId = DB::table('tanda_terima_lcl_items')->insertGetId(['tanda_terima_lcl_id' => $receiptId, 'nama_barang' => 'Lama']);
        $removeId = DB::table('tanda_terima_lcl_items')->insertGetId(['tanda_terima_lcl_id' => $receiptId, 'nama_barang' => 'Hapus']);
        $otherItemId = DB::table('tanda_terima_lcl_items')->insertGetId(['tanda_terima_lcl_id' => $otherId, 'nama_barang' => 'Lain']);

        TandaTerimaLcl::withoutEvents(fn () => app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', ['goods' => [
                ['id' => $keepId, 'nama_barang' => 'Baru', 'jumlah' => 4],
                ['nama_barang' => 'Tambahan', 'jumlah' => 1],
            ]]), 'lcl', $receiptId
        ));

        $this->assertDatabaseHas('tanda_terima_lcl_items', ['id' => $keepId, 'nama_barang' => 'Baru']);
        $this->assertDatabaseMissing('tanda_terima_lcl_items', ['id' => $removeId]);
        $this->assertDatabaseHas('tanda_terima_lcl_items', ['id' => $otherItemId, 'nama_barang' => 'Lain']);
        $this->assertDatabaseHas('tanda_terima_lcl_items', ['tanda_terima_lcl_id' => $receiptId, 'nama_barang' => 'Tambahan']);
    }

    public function test_ttsj_goods_update_refreshes_parent_summary(): void
    {
        $id = DB::table('tanda_terima_tanpa_surat_jalan')->insertGetId(['nama_barang' => 'Lama']);

        TandaTerimaTanpaSuratJalan::withoutEvents(fn () => app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', [
                'goods' => [['nama_barang' => 'Barang A', 'jumlah' => 3, 'satuan' => 'Koli', 'meter_kubik' => 2.5]],
                'keterangan_barang' => 'Rapuh',
            ]), 'ttsj', $id
        ));

        $this->assertDatabaseHas('tanda_terima_dimensi_items', ['tanda_terima_tanpa_surat_jalan_id' => $id, 'nama_barang' => 'Barang A']);
        $this->assertDatabaseHas('tanda_terima_tanpa_surat_jalan', [
            'id' => $id, 'nama_barang' => 'Barang A', 'jumlah_barang' => 3, 'keterangan_barang' => 'Rapuh',
        ]);
    }
}

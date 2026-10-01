<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use App\Models\ApprovalTandaTerimaDuaGoods;
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
        Schema::create('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->json('goods');
            $table->text('keterangan_barang')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['source_type', 'source_id']);
        });
    }

    public function test_fcl_goods_update_is_saved_separately_without_changing_original_json(): void
    {
        $id = DB::table('tanda_terimas')->insertGetId([
            'dimensi_items' => json_encode([['nama_barang' => 'Lama', 'jumlah' => 1, 'satuan' => 'Koli']]),
            'dimensi_details' => json_encode([['nama_barang' => 'Lama', 'jumlah' => 1, 'satuan' => 'Koli']]),
            'nama_barang' => json_encode(['Lama']),
        ]);

        TandaTerima::withoutEvents(fn () => app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', ['goods' => [[
                'original_index' => 0, 'nama_barang' => 'Mesin', 'hs_code' => '8429.51.00',
                'jumlah' => 2, 'satuan' => 'Unit', 'meter_kubik' => 3.5,
            ]]]), 'fcl', $id
        ));

        $receipt = TandaTerima::findOrFail($id);
        $this->assertSame('Lama', $receipt->dimensi_items[0]['nama_barang']);
        $this->assertSame('Lama', $receipt->dimensi_details[0]['nama_barang']);
        $this->assertSame(['Lama'], $receipt->nama_barang);
        $this->assertNull($receipt->jumlah);
        $override = ApprovalTandaTerimaDuaGoods::where('source_type', 'fcl')->where('source_id', $id)->firstOrFail();
        $this->assertSame('Mesin', $override->goods[0]['nama_barang']);
        $this->assertSame('8429.51.00', $override->goods[0]['hs_code']);
        $this->assertSame(2, $override->goods[0]['jumlah']);

        app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', ['goods' => [
                ['nama_barang' => 'Mesin Revisi', 'jumlah' => 3],
                ['nama_barang' => 'Barang Tambahan', 'jumlah' => 1],
            ]]), 'fcl', $id
        );

        $this->assertDatabaseCount('approval_tanda_terima_2_goods', 1);
        $this->assertSame(
            ['Mesin Revisi', 'Barang Tambahan'],
            array_column($override->fresh()->goods, 'nama_barang')
        );
        $this->assertSame('Lama', $receipt->fresh()->dimensi_items[0]['nama_barang']);
    }

    public function test_lcl_goods_update_does_not_change_or_delete_original_items(): void
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

        $this->assertDatabaseHas('tanda_terima_lcl_items', ['id' => $keepId, 'nama_barang' => 'Lama']);
        $this->assertDatabaseHas('tanda_terima_lcl_items', ['id' => $removeId, 'nama_barang' => 'Hapus']);
        $this->assertDatabaseHas('tanda_terima_lcl_items', ['id' => $otherItemId, 'nama_barang' => 'Lain']);
        $this->assertDatabaseMissing('tanda_terima_lcl_items', ['tanda_terima_lcl_id' => $receiptId, 'nama_barang' => 'Tambahan']);
        $override = ApprovalTandaTerimaDuaGoods::where('source_type', 'lcl')->where('source_id', $receiptId)->firstOrFail();
        $this->assertSame(['Baru', 'Tambahan'], array_column($override->goods, 'nama_barang'));
    }

    public function test_ttsj_goods_update_keeps_parent_summary_and_items_unchanged(): void
    {
        $id = DB::table('tanda_terima_tanpa_surat_jalan')->insertGetId(['nama_barang' => 'Lama']);
        $originalItemId = DB::table('tanda_terima_dimensi_items')->insertGetId([
            'tanda_terima_tanpa_surat_jalan_id' => $id, 'nama_barang' => 'Barang Asli',
        ]);

        TandaTerimaTanpaSuratJalan::withoutEvents(fn () => app(ApprovalTandaTerimaDuaController::class)->updateGoods(
            Request::create('/', 'PUT', [
                'goods' => [['nama_barang' => 'Barang A', 'jumlah' => 3, 'satuan' => 'Koli', 'meter_kubik' => 2.5]],
                'keterangan_barang' => 'Rapuh',
            ]), 'ttsj', $id
        ));

        $this->assertDatabaseHas('tanda_terima_dimensi_items', ['id' => $originalItemId, 'nama_barang' => 'Barang Asli']);
        $this->assertDatabaseMissing('tanda_terima_dimensi_items', ['tanda_terima_tanpa_surat_jalan_id' => $id, 'nama_barang' => 'Barang A']);
        $this->assertDatabaseHas('tanda_terima_tanpa_surat_jalan', [
            'id' => $id, 'nama_barang' => 'Lama', 'jumlah_barang' => null, 'keterangan_barang' => null,
        ]);
        $override = ApprovalTandaTerimaDuaGoods::where('source_type', 'ttsj')->where('source_id', $id)->firstOrFail();
        $this->assertSame('Barang A', $override->goods[0]['nama_barang']);
        $this->assertSame('Rapuh', $override->keterangan_barang);
    }
}

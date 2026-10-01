<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApprovalTandaTerimaDuaDestinationFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
        });
        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tanda_terima')->nullable();
            $table->string('nomor_tanda_terima')->nullable();
            $table->date('tanggal_tanda_terima')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
        });
        Schema::create('master_tujuan_kirim', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tujuan');
        });
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima')->nullable();
            $table->date('tanggal_tanda_terima')->nullable();
            $table->unsignedBigInteger('tujuan_pengiriman_id')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
            $table->string('nomor_kontainer')->nullable();
        });
        Schema::create('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
        });
        Schema::create('tanda_terima_dimensi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_tanpa_surat_jalan_id');
        });
        Schema::create('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->json('goods');
        });
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
        });
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prospek_id')->nullable();
            $table->string('nomor_tanda_terima')->nullable();
        });
    }

    public function test_destination_filter_uses_the_correct_source_for_each_receipt_type(): void
    {
        DB::table('tanda_terimas')->insert([
            ['tujuan_pengiriman' => 'Pelabuhan Jakarta'],
            ['tujuan_pengiriman' => 'Batam'],
        ]);
        DB::table('tanda_terima_tanpa_surat_jalan')->insert([
            ['tujuan_pengiriman' => 'Tanjung Pinang'],
            ['tujuan_pengiriman' => 'Jakarta'],
        ]);
        $batamId = DB::table('master_tujuan_kirim')->insertGetId(['nama_tujuan' => 'Batam']);
        $pinangId = DB::table('master_tujuan_kirim')->insertGetId(['nama_tujuan' => 'Tanjungpinang']);
        DB::table('tanda_terimas_lcl')->insert([
            ['tujuan_pengiriman_id' => $batamId],
            ['tujuan_pengiriman_id' => $pinangId],
        ]);

        $controller = new ApprovalTandaTerimaDuaController;
        foreach ([
            ['fcl', 'jakarta', 'Pelabuhan Jakarta'],
            ['lcl', 'batam', 'Batam'],
            ['lcl', 'tanjung-pinang', 'Tanjungpinang'],
            ['ttsj', 'tanjung-pinang', 'Tanjung Pinang'],
        ] as [$type, $destination, $expected]) {
            $items = $controller->index(Request::create('/', 'GET', [
                'type' => $type,
                'destination' => $destination,
            ]))->getData()['items'];

            $this->assertSame(1, $items->total());
            $actual = $type === 'lcl'
                ? $items->first()->tujuanPengiriman->nama_tujuan
                : $items->first()->tujuan_pengiriman;
            $this->assertSame($expected, $actual);
        }
    }

    public function test_receipts_already_used_in_a_manifest_are_hidden(): void
    {
        $visibleFclId = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-VISIBLE']);
        $linkedFclId = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-LINKED']);
        $numberedFclId = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-NUMBERED']);
        $prospekId = DB::table('prospek')->insertGetId(['tanda_terima_id' => $linkedFclId]);
        DB::table('manifests')->insert([
            ['prospek_id' => $prospekId, 'nomor_tanda_terima' => null],
            ['prospek_id' => null, 'nomor_tanda_terima' => 'SJ-NUMBERED'],
        ]);

        $fclItems = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'fcl'])
        )->getData()['items'];

        $this->assertEqualsCanonicalizing([$visibleFclId], $fclItems->pluck('id')->all());
        $this->assertNotContains($linkedFclId, $fclItems->pluck('id')->all());
        $this->assertNotContains($numberedFclId, $fclItems->pluck('id')->all());

        $visibleLclId = DB::table('tanda_terimas_lcl')->insertGetId(['nomor_tanda_terima' => 'LCL-VISIBLE']);
        DB::table('tanda_terimas_lcl')->insert(['nomor_tanda_terima' => 'LCL-MANIFEST']);
        DB::table('manifests')->insert(['nomor_tanda_terima' => 'LCL-MANIFEST']);

        $lclItems = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'lcl'])
        )->getData()['items'];
        $this->assertEqualsCanonicalizing([$visibleLclId], $lclItems->pluck('id')->all());

        $visibleTtsjId = DB::table('tanda_terima_tanpa_surat_jalan')->insertGetId([
            'nomor_tanda_terima' => 'TTSJ-VISIBLE',
        ]);
        DB::table('tanda_terima_tanpa_surat_jalan')->insert([
            'no_tanda_terima' => null,
            'nomor_tanda_terima' => 'TTSJ-MANIFEST',
        ]);
        DB::table('manifests')->insert(['nomor_tanda_terima' => 'TTSJ-MANIFEST']);

        $ttsjItems = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'ttsj'])
        )->getData()['items'];
        $this->assertEqualsCanonicalizing([$visibleTtsjId], $ttsjItems->pluck('id')->all());
    }

    public function test_receipts_from_2025_are_hidden_for_all_types(): void
    {
        DB::table('tanda_terimas')->insert([
            ['no_surat_jalan' => 'SJ-2025', 'tanggal_surat_jalan' => '2025-12-31'],
            ['no_surat_jalan' => 'SJ-2026', 'tanggal_surat_jalan' => '2026-01-01'],
            ['no_surat_jalan' => 'SJ-TANPA-TANGGAL', 'tanggal_surat_jalan' => null],
        ]);

        $items = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'fcl'])
        )->getData()['items'];

        $this->assertEqualsCanonicalizing(
            ['SJ-2026', 'SJ-TANPA-TANGGAL'],
            $items->pluck('no_surat_jalan')->all()
        );

        DB::table('tanda_terimas_lcl')->insert([
            ['nomor_tanda_terima' => 'LCL-2025', 'tanggal_tanda_terima' => '2025-01-01'],
            ['nomor_tanda_terima' => 'LCL-2026', 'tanggal_tanda_terima' => '2026-01-01'],
        ]);
        $lclItems = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'lcl'])
        )->getData()['items'];
        $this->assertEqualsCanonicalizing(['LCL-2026'], $lclItems->pluck('nomor_tanda_terima')->all());

        DB::table('tanda_terima_tanpa_surat_jalan')->insert([
            ['no_tanda_terima' => 'TTSJ-2025', 'tanggal_tanda_terima' => '2025-06-15'],
            ['no_tanda_terima' => 'TTSJ-2026', 'tanggal_tanda_terima' => '2026-06-15'],
        ]);
        $ttsjItems = (new ApprovalTandaTerimaDuaController)->index(
            Request::create('/', 'GET', ['type' => 'ttsj'])
        )->getData()['items'];
        $this->assertEqualsCanonicalizing(['TTSJ-2026'], $ttsjItems->pluck('no_tanda_terima')->all());
    }
}

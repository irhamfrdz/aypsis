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
            $table->string('tujuan_pengiriman')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
        });
        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('tujuan_pengiriman')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
        });
        Schema::create('master_tujuan_kirim', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tujuan');
        });
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tujuan_pengiriman_id')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
            $table->string('nomor_kontainer')->nullable();
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
}

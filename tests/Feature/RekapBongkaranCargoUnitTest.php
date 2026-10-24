<?php

namespace Tests\Feature;

use App\Http\Controllers\BiayaKapalController;
use App\Http\Controllers\BlController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RekapBongkaranCargoUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            foreach (['nama_kapal', 'no_voyage', 'tipe_kontainer', 'size_kontainer', 'pengirim', 'nama_barang', 'nomor_kontainer', 'nomor_bl', 'rekap_bongkaran_unit', 'satuan', 'pelabuhan_asal', 'pelabuhan_tujuan', 'pelabuhan_muat', 'pelabuhan_bongkar'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['tonnage', 'volume', 'tonnage_perincian', 'volume_perincian'] as $column) {
                $table->decimal($column, 12, 3)->nullable();
            }
            $table->integer('kuantitas')->nullable();
            $table->timestamps();
        });
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            foreach (['tipe', 'no_voyage', 'nomor_kontainer', 'ukuran', 'nama_kapal'] as $column) {
                $table->string($column)->nullable();
            }
        });
    }

    public function test_saved_rekap_units_control_buruh_cargo_without_changing_default_maximum(): void
    {
        $cargo = [
            ['nama_barang' => 'Cargo 1', 'tonnage' => 88.980, 'volume' => 82.300],
            ['nama_barang' => 'Cargo 2', 'tonnage' => 129.880, 'volume' => 247.253],
            ['nama_barang' => 'Cargo 3', 'tonnage' => 9.511, 'volume' => 19.081],
        ];
        foreach ($cargo as $item) {
            DB::table('manifests')->insert(array_merge($item, [
                'nama_kapal' => 'KM TEST', 'no_voyage' => 'JB01', 'tipe_kontainer' => 'CARGO',
            ]));
        }
        DB::table('manifests')->insert([
            'nama_kapal' => 'KM OTHER', 'no_voyage' => 'JB01', 'tipe_kontainer' => 'CARGO',
            'nama_barang' => 'Cargo 1', 'tonnage' => 100, 'volume' => 200,
        ]);

        $before = app(BiayaKapalController::class)->getContainerCounts(Request::create('/', 'POST', [
            'kapal' => 'KM TEST', 'voyage' => 'JB01',
        ]))->getData(true);
        $this->assertSame(355, $before['counts']['cargo_buruh_sum']);

        app(BlController::class)->saveRekapBongkaranUnits(Request::create('/', 'POST', [
            'nama_kapal' => 'KM TEST', 'no_voyage' => 'JB01',
            'choices' => [
                md5('cargo|nama|CARGO 1|colly') => 'ton',
                md5('cargo|nama|CARGO 2|colly') => 'ton',
                md5('cargo|nama|CARGO 3|colly') => 'm3',
            ],
        ]));

        $after = app(BiayaKapalController::class)->getContainerCounts(Request::create('/', 'POST', [
            'kapal' => 'KM TEST', 'voyage' => 'JB01',
        ]))->getData(true);
        $this->assertTrue($after['success']);
        $this->assertSame(238, $after['counts']['cargo_buruh_sum']);
        $this->assertSame(355, $after['counts']['cargo_max_tv_sum']);
        $this->assertSame('ton', DB::table('manifests')->where('nama_barang', 'Cargo 1')->where('nama_kapal', 'KM TEST')->value('rekap_bongkaran_unit'));
        $this->assertNull(DB::table('manifests')->where('nama_kapal', 'KM OTHER')->value('rekap_bongkaran_unit'));
    }
}

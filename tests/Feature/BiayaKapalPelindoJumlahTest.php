<?php

namespace Tests\Feature;

use App\Http\Controllers\BiayaKapalController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BiayaKapalPelindoJumlahTest extends TestCase
{
    public function test_cargo_uses_total_tonnage_while_lcl_container_is_counted_once(): void
    {
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('nomor_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('tonnage')->nullable();
        });

        DB::table('manifests')->insert([
            ['nama_kapal' => 'KM. UJI', 'no_voyage' => 'V001', 'nomor_kontainer' => null, 'size_kontainer' => null, 'tipe_kontainer' => 'CARGO', 'nama_barang' => 'Besi', 'tonnage' => '1.250'],
            ['nama_kapal' => 'KM. UJI', 'no_voyage' => 'V001', 'nomor_kontainer' => null, 'size_kontainer' => null, 'tipe_kontainer' => 'CARGO', 'nama_barang' => 'Besi', 'tonnage' => '2,375'],
            ['nama_kapal' => 'KM. UJI', 'no_voyage' => 'V001', 'nomor_kontainer' => 'LCL123', 'size_kontainer' => '20', 'tipe_kontainer' => 'LCL', 'nama_barang' => 'Barang', 'tonnage' => '5.000'],
            ['nama_kapal' => 'KM. UJI', 'no_voyage' => 'V001', 'nomor_kontainer' => 'LCL123', 'size_kontainer' => '20', 'tipe_kontainer' => 'LCL', 'nama_barang' => 'Barang', 'tonnage' => '3.000'],
            ['nama_kapal' => 'KM UJI', 'no_voyage' => 'V002', 'nomor_kontainer' => null, 'size_kontainer' => null, 'tipe_kontainer' => 'CARGO', 'nama_barang' => 'Besi', 'tonnage' => '10.000'],
        ]);

        $response = app(BiayaKapalController::class)->getPelindoJumlah(
            Request::create('/biaya-kapal/pelindo-jumlah', 'GET', ['kapal' => 'KM UJI', 'voyage' => 'V001'])
        );

        $this->assertSame(3.625, $response->getData(true)['counts']['|cargo']);
        $this->assertSame(1, $response->getData(true)['counts']['20|full']);
    }
}

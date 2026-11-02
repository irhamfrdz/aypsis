<?php

namespace Tests\Unit;

use App\Services\VehicleAmprahanUsageService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VehicleAmprahanUsageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('master_nama_barang_amprahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
        });
        Schema::create('stock_amprahans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_nama_barang_amprahan_id')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('type_barang')->nullable();
            $table->string('nomor_bukti')->nullable();
            $table->string('satuan')->nullable();
        });
        Schema::create('stock_amprahan_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_amprahan_id');
            $table->unsignedBigInteger('kendaraan_id')->nullable();
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->unsignedBigInteger('buntut_id')->nullable();
            $table->date('tanggal_pengambilan');
            $table->decimal('jumlah', 12, 2);
            $table->string('keterangan')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function test_usage_is_grouped_by_vehicle_and_usage_date_including_direct_usage(): void
    {
        $stockId = DB::table('stock_amprahans')->insertGetId([
            'nama_barang' => 'Oli Mesin', 'type_barang' => 'Barang', 'nomor_bukti' => 'B-001', 'satuan' => 'liter',
        ]);
        $serviceId = DB::table('stock_amprahans')->insertGetId([
            'nama_barang' => 'Servis Rem', 'type_barang' => 'Servis', 'nomor_bukti' => 'S-001', 'satuan' => 'kali',
        ]);
        foreach ([
            ['stock_amprahan_id' => $stockId, 'kendaraan_id' => 10, 'tanggal_pengambilan' => '2026-11-01', 'jumlah' => 2, 'keterangan' => 'Langsung pakai'],
            ['stock_amprahan_id' => $serviceId, 'truck_id' => 10, 'tanggal_pengambilan' => '2026-11-03', 'jumlah' => 1, 'keterangan' => 'Saat dipakai'],
            ['stock_amprahan_id' => $stockId, 'buntut_id' => 20, 'tanggal_pengambilan' => '2026-11-02', 'jumlah' => 1],
            ['stock_amprahan_id' => $stockId, 'kendaraan_id' => 10, 'truck_id' => 10, 'tanggal_pengambilan' => '2026-11-02', 'jumlah' => 1],
            ['stock_amprahan_id' => $stockId, 'kendaraan_id' => 10, 'tanggal_pengambilan' => '2026-10-31', 'jumlah' => 1],
            ['stock_amprahan_id' => $stockId, 'kendaraan_id' => 10, 'tanggal_pengambilan' => '2026-11-02', 'jumlah' => 1, 'deleted_at' => now()],
        ] as $usage) {
            DB::table('stock_amprahan_usages')->insert($usage);
        }

        $result = (new VehicleAmprahanUsageService)->forVehicles([10, 20], '2026-11-01', '2026-11-03');

        $this->assertCount(3, $result->get(10));
        $this->assertCount(1, $result->get(20));
        $this->assertSame('Servis Rem', $result->get(10)->first()['nama_barang']);
        $this->assertSame('Truck', $result->get(10)->first()['posisi']);
        $this->assertSame('Buntut', $result->get(20)->first()['posisi']);
        $this->assertSame('Kendaraan / Truck', $result->get(10)[1]['posisi']);
        $this->assertSame('Langsung pakai', $result->get(10)->last()['keterangan']);
    }
}

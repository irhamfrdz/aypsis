<?php

namespace Tests\Unit;

use App\Services\KaryawanAssetService;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class KaryawanAssetServiceTest extends TestCase
{
    public function test_only_active_assets_assigned_to_the_employee_are_collected_from_multiple_tables(): void
    {
        $capsule = new Manager;
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        DB::swap($capsule->getDatabaseManager());

        try {
            $schema = $capsule->getConnection()->getSchemaBuilder();
            foreach (['mobils', 'inventaris'] as $name) {
                $schema->create($name, function (Blueprint $table) use ($name) {
                    $table->id();
                    $table->integer('karyawan_id');
                    $table->boolean('is_asset_karyawan');
                    $table->string('kode_barang');
                    $table->string('nama_barang');
                    $table->string('catatan')->default('Nama dari catatan');
                    if ($name === 'mobils') {
                        $table->string('jenis')->default('SEPEDA MOTOR');
                        $table->string('kategori_asset')->default('Kendaraan');
                        $table->date('tanggal_tanda_terima_asset')->default('2026-10-23');
                        $table->string('kondisi_fisik')->default('Baik');
                    }
                    $table->softDeletes();
                });
                DB::table($name)->insert([
                    ['karyawan_id' => 7, 'is_asset_karyawan' => 1, 'kode_barang' => 'ACTIVE', 'nama_barang' => 'Asset aktif', 'deleted_at' => null],
                    ['karyawan_id' => 8, 'is_asset_karyawan' => 1, 'kode_barang' => 'OTHER', 'nama_barang' => 'Milik karyawan lain', 'deleted_at' => null],
                    ['karyawan_id' => 7, 'is_asset_karyawan' => 0, 'kode_barang' => 'OFF', 'nama_barang' => 'Tidak digunakan', 'deleted_at' => null],
                    ['karyawan_id' => 7, 'is_asset_karyawan' => 1, 'kode_barang' => 'DELETED', 'nama_barang' => 'Dihapus', 'deleted_at' => '2026-10-23 00:00:00'],
                ]);
            }
            $schema->create('unassigned_assets', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_asset_karyawan');
            });
            DB::table('unassigned_assets')->insert(['is_asset_karyawan' => 1]);
            $schema->create('ordinary_records', function (Blueprint $table) {
                $table->id();
                $table->integer('karyawan_id');
            });

            $service = new KaryawanAssetService;
            $assets = $service->forKaryawan(7);
            $this->assertCount(2, $assets);
            $this->assertSame(['ACTIVE', 'ACTIVE'], $assets->pluck('kode')->all());
            $this->assertSame(['Nama dari catatan', 'Nama dari catatan'], $assets->pluck('nama')->all());
            foreach ($assets as $asset) {
                $this->assertStringNotContainsString('Catatan:', $asset['detail']);
            }
            $this->assertContains('Mobil', $assets->pluck('sumber')->all());
            $mobil = $assets->firstWhere('sumber', 'Mobil');
            $this->assertSame('Kendaraan', $mobil['jenis']);
            $this->assertSame('2026-10-23', $mobil['tanggal_tanda_terima_asset']);
            $this->assertSame('Baik', $mobil['kondisi_fisik']);
            $this->assertStringNotContainsString('Kondisi Fisik:', $mobil['detail']);
            $withoutMetadata = $assets->first(fn ($asset) => $asset['sumber'] !== 'Mobil');
            $this->assertSame('-', $withoutMetadata['jenis']);
            $this->assertNull($withoutMetadata['tanggal_tanda_terima_asset']);
            $this->assertSame('-', $withoutMetadata['kondisi_fisik']);
            $this->assertTrue($service->forKaryawan(99)->isEmpty());
        } finally {
            $capsule->getDatabaseManager()->disconnect();
            DB::clearResolvedInstance('db');
        }
    }
}

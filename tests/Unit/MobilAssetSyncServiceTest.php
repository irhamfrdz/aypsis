<?php

namespace Tests\Unit;

use App\Models\Asset;
use App\Models\Mobil;
use App\Services\MobilAssetSyncService;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;

class MobilAssetSyncServiceTest extends TestCase
{
    public function test_activation_creates_one_asset_and_subsequent_changes_update_the_same_asset(): void
    {
        $capsule = new Manager;
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $previousResolver = Model::getConnectionResolver();
        $previousDispatcher = Model::getEventDispatcher();
        $capsule->bootEloquent();
        Model::unsetEventDispatcher();
        DB::swap($capsule->getDatabaseManager());
        $schema = $capsule->getConnection()->getSchemaBuilder();
        Schema::swap($schema);

        try {
            $schema->create('mobils', function (Blueprint $table) {
                $table->id();
                $table->string('kode_no');
                $table->boolean('is_asset_karyawan')->default(false);
                $table->unsignedBigInteger('karyawan_id')->nullable();
                foreach (['catatan', 'merek', 'jenis', 'kategori_asset', 'status_operasional', 'kondisi_fisik', 'lokasi', 'nomor_polisi', 'no_mesin'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->date('tanggal_tanda_terima_asset')->nullable();
                $table->timestamps();
            });
            $schema->create('assets', function (Blueprint $table) {
                $table->id();
                $table->string('kode_asset')->unique();
                $table->string('lokasi_asset', 3)->nullable();
                foreach (['nama_asset', 'nama_merk', 'kategori', 'kondisi', 'status', 'nomor_seri', 'lokasi', 'keterangan'] as $column) {
                    $table->string($column)->nullable();
                }
                foreach (['karyawan_id', 'kapal_id', 'created_by', 'updated_by'] as $column) {
                    $table->unsignedBigInteger($column)->nullable();
                }
                $table->date('tanggal_tanda_terima')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            $migration = require __DIR__.'/../../database/migrations/2026_10_23_160000_add_source_reference_to_assets_table.php';
            $migration->up();

            $mobil = Mobil::create([
                'kode_no' => 'AT-001', 'catatan' => 'MTR-JKT.019', 'merek' => 'HONDA',
                'jenis' => 'SEPEDA MOTOR', 'kategori_asset' => 'Kendaraan', 'karyawan_id' => 7,
                'tanggal_tanda_terima_asset' => '2026-10-23', 'kondisi_fisik' => 'Baik',
                'status_operasional' => 'Digunakan', 'lokasi' => 'JKT', 'no_mesin' => 'MESIN-1',
            ]);
            $service = new MobilAssetSyncService;
            $this->assertNull($service->sync($mobil));
            $this->assertSame(0, Asset::count());

            $mobil->update(['is_asset_karyawan' => true]);
            $asset = $service->sync($mobil, 1);
            $this->assertSame('AT-001', $asset->kode_asset);
            $this->assertSame('MTR-JKT.019', $asset->nama_asset);
            $this->assertSame('Kendaraan', $asset->kategori);
            $this->assertSame('2026-10-23', $asset->tanggal_tanda_terima->format('Y-m-d'));
            $this->assertSame(7, $asset->karyawan_id);
            $this->assertSame('Di bawa oleh karyawan', $asset->lokasi);
            $this->assertSame('JKT', $asset->lokasi_asset);
            $this->assertSame('MESIN-1', $asset->nomor_seri);
            $this->assertSame($asset->id, $service->sync($mobil, 1)->id);
            $this->assertSame(1, Asset::count());

            $mobil->update(['karyawan_id' => 8, 'catatan' => 'Nama diperbarui', 'kode_no' => 'AT-002']);
            $updated = $service->sync($mobil);
            $this->assertSame($asset->id, $updated->id);
            $this->assertSame(8, $updated->karyawan_id);
            $this->assertSame('Nama diperbarui', $updated->nama_asset);
            $this->assertSame('AT-002', $updated->kode_asset);

            $mobil->update(['is_asset_karyawan' => false]);
            $released = $service->sync($mobil);
            $this->assertSame(1, Asset::count());
            $this->assertNull($released->karyawan_id);
            $this->assertSame('Tersedia', $released->status);
            $this->assertSame('Jakarta', $released->lokasi);
            $this->assertSame('JKT', $released->lokasi_asset);

            $released->delete();
            $mobil->update(['is_asset_karyawan' => true]);
            $restored = $service->sync($mobil);
            $this->assertSame($asset->id, $restored->id);
            $this->assertFalse($restored->trashed());
            $this->assertSame(1, Asset::withTrashed()->count());
            $migration->down();
            $this->assertFalse($schema->hasColumn('assets', 'source_id'));
        } finally {
            $capsule->getDatabaseManager()->disconnect();
            $previousResolver ? Model::setConnectionResolver($previousResolver) : Model::unsetConnectionResolver();
            $previousDispatcher ? Model::setEventDispatcher($previousDispatcher) : Model::unsetEventDispatcher();
            DB::clearResolvedInstance('db');
            Schema::clearResolvedInstance('db.schema');
        }
    }
}

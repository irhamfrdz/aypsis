<?php

namespace Tests\Unit;

use App\Models\Mobil;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;

class MobilAssetInformationTest extends TestCase
{
    public function test_asset_information_is_persisted_and_migration_can_be_rolled_back(): void
    {
        $capsule = new Manager;
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $previousResolver = Mobil::getConnectionResolver();
        $capsule->bootEloquent();
        $schema = $capsule->getConnection()->getSchemaBuilder();
        Schema::swap($schema);

        try {
            $schema->create('mobils', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
            $id = $capsule->getConnection()->table('mobils')->insertGetId([]);
            $migration = require __DIR__.'/../../database/migrations/2026_10_23_140000_add_asset_information_to_mobils_table.php';
            $migration->up();

            $mobil = Mobil::findOrFail($id);
            $this->assertNull($mobil->kategori_asset);
            $mobil->fill([
                'tanggal_tanda_terima_asset' => '2026-10-23',
                'kategori_asset' => 'Kendaraan',
                'status_operasional' => 'Digunakan',
                'kondisi_fisik' => 'Baik',
            ])->saveQuietly();
            $saved = $mobil->fresh();
            $this->assertSame('2026-10-23', $saved->tanggal_tanda_terima_asset->format('Y-m-d'));
            $this->assertSame('Kendaraan', $saved->kategori_asset);
            $this->assertSame('Digunakan', $saved->status_operasional);
            $this->assertSame('Baik', $saved->kondisi_fisik);

            $migration->down();
            $this->assertFalse($schema->hasColumn('mobils', 'kategori_asset'));
        } finally {
            $capsule->getDatabaseManager()->disconnect();
            if ($previousResolver) {
                Mobil::setConnectionResolver($previousResolver);
            } else {
                Mobil::unsetConnectionResolver();
            }
            Schema::clearResolvedInstance('db.schema');
        }
    }
}

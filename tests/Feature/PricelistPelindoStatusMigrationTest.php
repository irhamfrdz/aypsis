<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PricelistPelindoStatusMigrationTest extends TestCase
{
    public function test_mobil_and_alat_berat_statuses_are_supported_by_schema(): void
    {
        Schema::dropIfExists('tagihan_pelindo_items');
        Schema::dropIfExists('pricelist_pelindos');

        Schema::create('pricelist_pelindos', function (Blueprint $table) {
            $table->id();
            $table->enum('status_kontainer', [
                'empty', 'full', 'cargo', 'barang_berbahaya', 'lpg', 'besi_beton',
            ])->nullable();
        });

        $migration = require database_path('migrations/2026_11_07_160000_add_mobil_alat_berat_to_pricelist_pelindo_status.php');
        $migration->up();

        DB::table('pricelist_pelindos')->insert([
            ['id' => 1, 'status_kontainer' => 'mobil'],
            ['id' => 2, 'status_kontainer' => 'alat_berat'],
        ]);

        $this->assertSame(['mobil', 'alat_berat'], DB::table('pricelist_pelindos')->orderBy('id')->pluck('status_kontainer')->all());

        $migration->down();

        $this->assertSame([null, null], DB::table('pricelist_pelindos')->orderBy('id')->pluck('status_kontainer')->all());
    }

    public function test_renaming_b3_preserves_existing_pricelist_and_tagihan_items(): void
    {
        Schema::dropIfExists('tagihan_pelindo_items');
        Schema::dropIfExists('pricelist_pelindos');

        Schema::create('pricelist_pelindos', function (Blueprint $table) {
            $table->id();
            $table->enum('status_kontainer', ['empty', 'full', 'cargo', 'b3', 'lpg', 'besi_beton'])->nullable();
        });

        Schema::create('tagihan_pelindo_items', function (Blueprint $table) {
            $table->id();
            $table->string('status_kontainer')->nullable();
        });

        DB::table('pricelist_pelindos')->insert(['id' => 1, 'status_kontainer' => 'b3']);
        DB::table('tagihan_pelindo_items')->insert(['id' => 1, 'status_kontainer' => 'B3']);

        $migration = require database_path('migrations/2026_11_07_150000_rename_b3_to_barang_berbahaya_in_pelindo_status.php');
        $migration->up();

        $this->assertSame('barang_berbahaya', DB::table('pricelist_pelindos')->value('status_kontainer'));
        $this->assertSame('Barang Berbahaya', DB::table('tagihan_pelindo_items')->value('status_kontainer'));

        $migration->down();

        $this->assertSame('b3', DB::table('pricelist_pelindos')->value('status_kontainer'));
        $this->assertSame('B3', DB::table('tagihan_pelindo_items')->value('status_kontainer'));
    }
}

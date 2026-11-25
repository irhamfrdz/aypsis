<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FixObMuatTemasMobilPanjangRateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('naik_kapal', function (Blueprint $table) {
            $table->id();
            $table->boolean('ob_mobil_panjang')->default(false);
        });
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            foreach (['kegiatan', 'kapal', 'voyage', 'nomor_kontainer', 'nama_supir'] as $column) $table->string($column);
            $table->unsignedBigInteger('naik_kapal_id')->nullable();
            $table->boolean('is_ckls_mobil_panjang')->default(false);
            $table->boolean('is_combo')->default(false);
            $table->decimal('biaya');
            $table->timestamps();
        });
        foreach (['pranota_ob_muat_temas_items', 'pranota_ob_antar_gudang_items'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tagihan_ob_id');
            });
        }
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
        });
        foreach (range(1, 7) as $id) {
            DB::table('naik_kapal')->insert(['id' => $id]);
            DB::table('tagihan_ob')->insert([
                'id' => $id, 'naik_kapal_id' => $id, 'kegiatan' => 'MUAT TEMAS',
                'kapal' => 'KM. MERATUS SORONG', 'voyage' => 'MERATUSJB497N',
                'nomor_kontainer' => 'TEST'.$id, 'nama_supir' => 'SUPIR UJI',
                'is_ckls_mobil_panjang' => true, 'biaya' => 125000,
            ]);
        }
        DB::table('tagihan_ob')->where('id', 2)->update(['biaya' => 325000]);
        DB::table('tagihan_ob')->where('id', 3)->update(['is_ckls_mobil_panjang' => false]);
        DB::table('tagihan_ob')->where('id', 4)->update(['voyage' => 'OTHER']);
        DB::table('pranota_ob_muat_temas_items')->insert(['tagihan_ob_id' => 5]);
        DB::table('pranota_ob_items')->insert(['item_type' => \App\Models\NaikKapal::class, 'item_id' => 6]);
        DB::table('tagihan_ob')->where('id', 7)->update(['is_ckls_mobil_panjang' => false]);
        DB::table('naik_kapal')->where('id', 7)->update(['ob_mobil_panjang' => true]);
    }

    private function commandOptions(): array
    {
        return ['--kapal' => 'KM MERATUS SORONG', '--voyage' => 'MERATUSJB497N'];
    }

    public function test_preview_does_not_write_and_requires_ship_and_voyage(): void
    {
        $this->artisan('ob:fix-mobil-panjang-rate')->assertFailed();
        $this->artisan('ob:fix-mobil-panjang-rate', $this->commandOptions())->expectsOutputToContain('Kontainer cocok: 2')->assertSuccessful();
        $this->assertDatabaseHas('tagihan_ob', ['id' => 1, 'biaya' => 125000]);
        $this->assertDatabaseHas('tagihan_ob', ['id' => 7, 'biaya' => 125000]);
    }

    public function test_apply_only_changes_unbilled_long_vehicle_rows_and_is_repeatable(): void
    {
        $this->artisan('ob:fix-mobil-panjang-rate', $this->commandOptions() + ['--apply' => true])->assertSuccessful();
        foreach ([1, 7] as $id) $this->assertDatabaseHas('tagihan_ob', ['id' => $id, 'biaya' => 250000]);
        foreach ([3, 4, 5, 6] as $id) $this->assertDatabaseHas('tagihan_ob', ['id' => $id, 'biaya' => 125000]);
        $this->assertDatabaseHas('tagihan_ob', ['id' => 2, 'biaya' => 325000]);
        $this->artisan('ob:fix-mobil-panjang-rate', $this->commandOptions() + ['--apply' => true])->expectsOutputToContain('Kontainer cocok: 0')->assertSuccessful();
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CleanupDuplicateBlAp14Bj26Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('bls', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bl')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('no_voyage');
            $table->string('nama_barang')->nullable();
            $table->timestamps();
        });
        Schema::create('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bl_id')->nullable();
        });
        Schema::create('biaya_kapal_thcs', function (Blueprint $table) {
            $table->id();
            $table->text('kontainer_ids')->nullable();
        });

        DB::table('bls')->insert([
            ['id' => 1, 'nomor_bl' => 'BL-01', 'nomor_kontainer' => 'CONT-01', 'no_voyage' => 'AP14BJ26', 'nama_barang' => 'Barang A'],
            ['id' => 2, 'nomor_bl' => 'BL-01 ', 'nomor_kontainer' => 'CONT-01', 'no_voyage' => 'AP14BJ26', 'nama_barang' => 'Barang A'],
            ['id' => 3, 'nomor_bl' => 'BL-02', 'nomor_kontainer' => 'CONT-02', 'no_voyage' => 'AP14BJ26', 'nama_barang' => 'Barang B'],
            ['id' => 4, 'nomor_bl' => 'BL-02', 'nomor_kontainer' => 'CONT-02', 'no_voyage' => 'AP14BJ26', 'nama_barang' => 'Barang B'],
            ['id' => 5, 'nomor_bl' => 'BL-01', 'nomor_kontainer' => 'CONT-01', 'no_voyage' => 'OTHER', 'nama_barang' => 'Barang A'],
        ]);
        DB::table('surat_jalan_bongkarans')->insert([
            ['bl_id' => 2],
            ['bl_id' => 3],
        ]);
        DB::table('biaya_kapal_thcs')->insert([
            ['kontainer_ids' => json_encode([['bl_id' => 1]])],
            ['kontainer_ids' => json_encode([['bl_id' => 4]])],
        ]);
    }

    public function test_preview_and_execute_keep_linked_row_and_only_touch_target_voyage(): void
    {
        $this->artisan('bl:hapus-duplikat-ap14bj26')->assertSuccessful();
        $this->assertEquals(5, DB::table('bls')->count());

        $this->artisan('bl:hapus-duplikat-ap14bj26 --execute')->assertSuccessful();
        $this->assertEquals([1, 3, 5], DB::table('bls')->orderBy('id')->pluck('id')->all());
        $this->assertEquals([1, 3], DB::table('surat_jalan_bongkarans')->orderBy('id')->pluck('bl_id')->all());
        $this->assertEquals([1, 3], DB::table('biaya_kapal_thcs')->orderBy('id')->pluck('kontainer_ids')
            ->map(fn ($value) => json_decode($value, true)[0]['bl_id'])->all());
    }

    public function test_ambiguous_group_aborts_all_deletions(): void
    {
        DB::table('bls')->where('id', 4)->update(['nomor_kontainer' => 'DIFFERENT']);

        $this->artisan('bl:hapus-duplikat-ap14bj26 --execute')->assertFailed();
        $this->assertEquals(5, DB::table('bls')->count());
    }
}

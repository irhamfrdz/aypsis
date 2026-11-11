<?php

namespace Tests\Feature;

use App\Http\Controllers\PengisianTanggalDokumenController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PengisianTanggalDokumenMultiSelectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang');
        });
        Schema::create('master_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->softDeletes();
        });
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('nomor_bl');
            $table->string('nomor_tanda_terima');
        });
        Schema::create('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id');
            $table->unsignedBigInteger('kapal_id');
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('jenis_dokumen');
            $table->string('nomor_dokumen');
            $table->string('identifier_hash')->unique();
            $table->date('tanggal_terima_dokumen_packinglist')->nullable();
            $table->timestamps();
        });
        DB::table('cabangs')->insert(['id' => 1, 'nama_cabang' => 'Batam']);
        DB::table('master_kapals')->insert(['id' => 1, 'nama_kapal' => 'Kapal Test']);
        DB::table('manifests')->insert(['nama_kapal' => 'Kapal Test', 'no_voyage' => 'V1', 'nomor_bl' => 'SAMA', 'nomor_tanda_terima' => 'SAMA']);
        Route::post('/test-document-multiple', [PengisianTanggalDokumenController::class, 'store']);
    }

    private function payload(): array
    {
        return [
            'cabang_id' => 1, 'kapal_id' => 1, 'no_voyage' => 'V1',
            'jenis_dokumen' => ['BL', 'TANDA_TERIMA'],
            'nomor_dokumen' => ['BL:SAMA', 'TANDA_TERIMA:SAMA'],
            'tanggal_terima_dokumen_packinglist' => '2026-11-11',
        ];
    }

    public function test_saves_both_types_with_same_number_and_updates_without_duplicates(): void
    {
        $this->post('/test-document-multiple', $this->payload())->assertRedirect();
        $this->assertDatabaseCount('pengisian_tanggal_dokumens', 2);
        foreach (['BL', 'TANDA_TERIMA'] as $type) {
            $this->assertDatabaseHas('pengisian_tanggal_dokumens', [
                'jenis_dokumen' => $type, 'nomor_dokumen' => 'SAMA',
                'tanggal_terima_dokumen_packinglist' => '2026-11-11 00:00:00',
            ]);
        }
        $this->post('/test-document-multiple', array_replace($this->payload(), [
            'tanggal_terima_dokumen_packinglist' => '2026-11-12',
        ]))->assertRedirect();
        $this->assertDatabaseCount('pengisian_tanggal_dokumens', 2);
        $this->assertSame(2, DB::table('pengisian_tanggal_dokumens')->whereDate('tanggal_terima_dokumen_packinglist', '2026-11-12')->count());
    }

    public function test_rejects_numbers_from_unselected_types_without_partial_save(): void
    {
        $this->post('/test-document-multiple', array_replace($this->payload(), [
            'jenis_dokumen' => ['BL'],
        ]))->assertSessionHasErrors('nomor_dokumen');
        $this->assertDatabaseCount('pengisian_tanggal_dokumens', 0);
    }

    public function test_rejects_number_outside_selected_voyage_without_partial_save(): void
    {
        $this->post('/test-document-multiple', array_replace($this->payload(), [
            'nomor_dokumen' => ['BL:SAMA', 'TANDA_TERIMA:TIDAK-ADA'],
        ]))->assertSessionHasErrors('nomor_dokumen');
        $this->assertDatabaseCount('pengisian_tanggal_dokumens', 0);
    }
}

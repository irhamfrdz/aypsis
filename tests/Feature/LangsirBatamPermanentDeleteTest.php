<?php

namespace Tests\Feature;

use App\Http\Controllers\LangsirBatamController;
use App\Models\LangsirBatam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LangsirBatamPermanentDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->string('no_surat_jalan')->nullable()->unique();
            $table->string('no_kontainer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->text('keterangan');
            $table->date('tanggal_kegiatan');
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->timestamps();
        });
        foreach (['kontainers', 'stock_kontainers'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('nomor_seri_gabungan');
                $table->unsignedBigInteger('gudangs_id')->nullable();
                $table->timestamps();
            });
        }
        foreach ([1, 2, 3, 4] as $id) {
            DB::table('langsir_batams')->insert([
                'id' => $id, 'no_transaksi' => 'LNG-20261125-000'.$id,
                'no_surat_jalan' => $id === 2 ? '0034007-1' : 'SJ'.$id,
                'no_kontainer' => 'TEST'.$id, 'deleted_at' => in_array($id, [2, 3]) ? now() : null,
            ]);
            DB::table('history_kontainers')->insert([
                'nomor_kontainer' => 'TEST'.$id, 'keterangan' => '[No Transaksi: LNG-20261125-000'.$id.']',
                'tanggal_kegiatan' => '2026-11-25', 'gudang_id' => 2,
            ]);
        }
        DB::table('kontainers')->insert(['nomor_seri_gabungan' => 'TEST2', 'gudangs_id' => 2]);
        DB::table('history_kontainers')->insert(['nomor_kontainer' => 'TEST2', 'keterangan' => 'Riwayat sebelumnya', 'tanggal_kegiatan' => '2026-11-24', 'gudang_id' => 1]);
    }

    public function test_single_and_bulk_delete_are_permanent(): void
    {
        (new LangsirBatamController)->destroy(1);
        $this->assertDatabaseMissing('langsir_batams', ['id' => 1]);
        $this->assertDatabaseMissing('history_kontainers', ['nomor_kontainer' => 'TEST1']);
        (new LangsirBatamController)->bulkDelete(Request::create('/', 'POST', ['ids' => [4]]));
        $this->assertDatabaseMissing('langsir_batams', ['id' => 4]);
        $this->assertDatabaseHas('langsir_batams', ['id' => 2]);
    }

    public function test_cleanup_preview_and_filtered_apply_release_the_duplicate_number(): void
    {
        $options = ['--no-surat-jalan' => '0034007-1'];
        $this->artisan('langsir-batam:purge-deleted', $options)->assertSuccessful();
        $this->assertDatabaseHas('langsir_batams', ['id' => 2]);
        $this->artisan('langsir-batam:purge-deleted', $options + ['--apply' => true])->assertSuccessful();
        $this->assertDatabaseMissing('langsir_batams', ['id' => 2]);
        foreach ([1, 3, 4] as $id) $this->assertDatabaseHas('langsir_batams', ['id' => $id]);
        $this->assertDatabaseHas('kontainers', ['nomor_seri_gabungan' => 'TEST2', 'gudangs_id' => 1]);
        LangsirBatam::createWithAutomaticNumber(['no_surat_jalan' => '0034007-1']);
        $this->assertDatabaseHas('langsir_batams', ['no_surat_jalan' => '0034007-1', 'deleted_at' => null]);
    }

    public function test_cleanup_without_filter_only_removes_soft_deleted_data(): void
    {
        $this->artisan('langsir-batam:purge-deleted', ['--apply' => true])->assertSuccessful();
        $this->assertDatabaseMissing('langsir_batams', ['id' => 2]);
        $this->assertDatabaseMissing('langsir_batams', ['id' => 3]);
        $this->assertDatabaseHas('langsir_batams', ['id' => 1]);
        $this->assertDatabaseHas('langsir_batams', ['id' => 4]);
    }
}

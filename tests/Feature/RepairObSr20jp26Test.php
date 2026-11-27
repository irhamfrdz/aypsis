<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RepairObSr20jp26Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['naik_kapal', 'bls'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->string('no_voyage');
                $table->string('nama_kapal');
                $table->string('nomor_kontainer');
                $table->unsignedBigInteger('prospek_id')->nullable();
                $table->boolean('sudah_ob')->default(false);
                $table->unsignedBigInteger('supir_id')->nullable();
                $table->timestamp('tanggal_ob')->nullable();
                $table->string('status_bongkar')->default('Belum Bongkar');
            });
        }
        foreach (['surat_jalan_bongkarans', 'tagihan_ob'] as $table) {
            Schema::create($table, fn (Blueprint $table) => $table->unsignedBigInteger('bl_id'));
        }
        for ($i = 1; $i <= 126; $i++) {
            $row = ['no_voyage' => 'SR20JP26', 'nama_kapal' => 'KM. SRIWIJAYA RAYA',
                'nomor_kontainer' => 'TEST'.$i, 'prospek_id' => $i];
            DB::table('naik_kapal')->insert($row);
            DB::table('bls')->insert($row);
        }
    }

    private function addImports(): void
    {
        for ($i = 0; $i < 200; $i++) {
            DB::table('bls')->insert(['no_voyage' => 'SR20JP26', 'nama_kapal' => 'KM SRIWIJAYA RAYA',
                'nomor_kontainer' => 'IMPORT'.$i]);
        }
    }

    public function test_different_production_counts_are_accepted_in_preview(): void
    {
        $this->addImports();
        $this->artisan('ob:repair-sr20jp26')->expectsOutputToContain('hapus 200')
            ->assertSuccessful();
        $this->assertSame(326, DB::table('bls')->count());
    }

    public function test_referenced_import_is_never_deleted(): void
    {
        $this->addImports();
        DB::table('tagihan_ob')->insert(['bl_id' => 127]);
        $this->artisan('ob:repair-sr20jp26', ['--apply' => true])
            ->expectsOutputToContain('ID BL: 127')->assertFailed();
        $this->assertSame(326, DB::table('bls')->count());
    }

    public function test_apply_removes_only_imports_and_is_repeatable(): void
    {
        $this->addImports();
        File::shouldReceive('ensureDirectoryExists')->once();
        File::shouldReceive('put')->once()->withArgs(function ($path, $json) {
            $backup = json_decode($json, true);

            return str_contains($path, 'ob-sr20jp26-')
                && count($backup['bongkar']) === 326
                && count($backup['remove_ids']) === 200;
        })->andReturn(100);
        $this->artisan('ob:repair-sr20jp26', ['--apply' => true])->assertSuccessful();
        $this->assertSame(126, DB::table('bls')->count());
        $this->assertSame(126, DB::table('naik_kapal')->count());
        $this->artisan('ob:repair-sr20jp26', ['--apply' => true])
            ->expectsOutputToContain('Data sudah sesuai')->assertSuccessful();
    }

    public function test_already_repaired_data_is_unchanged(): void
    {
        $this->artisan('ob:repair-sr20jp26', ['--apply' => true])
            ->expectsOutputToContain('Data sudah sesuai')->assertSuccessful();
        $this->assertSame(126, DB::table('bls')->count());
    }
}

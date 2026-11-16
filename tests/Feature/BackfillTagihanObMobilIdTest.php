<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BackfillTagihanObMobilIdTest extends TestCase
{
    public function test_backfill_previews_then_updates_only_unambiguous_empty_vehicle_ids(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
            $table->integer('karyawan_id')->nullable();
        });
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan');
        });
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->integer('mobil_id')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->string('nama_supir')->nullable();
        });
        DB::table('karyawans')->insert(['id' => 1, 'nama_lengkap' => 'SUPIR SATU', 'nama_panggilan' => 'SATU']);
        DB::table('mobils')->insert([
            ['id' => 1, 'nomor_polisi' => 'B 1234 AA', 'karyawan_id' => 1],
            ['id' => 2, 'nomor_polisi' => 'B 9999 ZZ', 'karyawan_id' => null],
            ['id' => 3, 'nomor_polisi' => 'B9999ZZ', 'karyawan_id' => null],
        ]);
        foreach ([1 => 'b-1234 aa', 2 => null, 3 => 'B 9999 ZZ', 4 => 'UNKNOWN', 5 => 'B1234AA'] as $id => $plate) {
            DB::table('tagihan_ob')->insert(['id' => $id, 'mobil_id' => $id === 5 ? 2 : null, 'nomor_polisi' => $plate, 'nama_supir' => ' satu ']);
        }
        $this->artisan('tagihan-ob:backfill-mobil-id')->assertSuccessful();
        $this->assertSame(4, DB::table('tagihan_ob')->whereNull('mobil_id')->count());
        $this->artisan('tagihan-ob:backfill-mobil-id', ['--apply' => true])->assertSuccessful();
        $this->assertSame(1, DB::table('tagihan_ob')->where('id', 1)->value('mobil_id'));
        $this->assertNull(DB::table('tagihan_ob')->where('id', 2)->value('mobil_id'));
        $this->artisan('tagihan-ob:backfill-mobil-id', ['--apply' => true, '--match-driver' => true])->assertSuccessful();
        $this->assertSame(1, DB::table('tagihan_ob')->where('id', 2)->value('mobil_id'));
        $this->assertNull(DB::table('tagihan_ob')->where('id', 3)->value('mobil_id'));
        $this->assertNull(DB::table('tagihan_ob')->where('id', 4)->value('mobil_id'));
        $this->assertSame(2, DB::table('tagihan_ob')->where('id', 5)->value('mobil_id'));
        $this->artisan('tagihan-ob:backfill-mobil-id', ['--apply' => true, '--match-driver' => true])->assertSuccessful();
        $this->assertSame(2, DB::table('tagihan_ob')->whereNull('mobil_id')->count());
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BiayaKapalPerijinanHeaderBackfillTest extends TestCase
{
    public function test_backfill_uses_first_section_value_only_for_empty_header_fields(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor')->nullable();
            $table->string('nomor_referensi')->nullable();
        });
        Schema::create('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->string('vendor')->nullable();
            $table->string('nomor_referensi')->nullable();
        });

        DB::table('biaya_kapals')->insert([
            ['id' => 1, 'nama_vendor' => null, 'nomor_referensi' => ''],
            ['id' => 2, 'nama_vendor' => 'Vendor Header', 'nomor_referensi' => 'REF-HEADER'],
        ]);
        DB::table('biaya_kapal_perijinan')->insert([
            ['id' => 1, 'biaya_kapal_id' => 1, 'vendor' => 'Vendor Lama', 'nomor_referensi' => null],
            ['id' => 2, 'biaya_kapal_id' => 1, 'vendor' => 'Vendor Kedua', 'nomor_referensi' => 'REF-LAMA'],
            ['id' => 3, 'biaya_kapal_id' => 2, 'vendor' => 'Vendor Section', 'nomor_referensi' => 'REF-SECTION'],
        ]);

        $migration = require database_path('migrations/2026_10_20_000002_backfill_biaya_kapal_perijinan_header_fields.php');
        $migration->up();

        $this->assertDatabaseHas('biaya_kapals', ['id' => 1, 'nama_vendor' => 'Vendor Lama', 'nomor_referensi' => 'REF-LAMA']);
        $this->assertDatabaseHas('biaya_kapals', ['id' => 2, 'nama_vendor' => 'Vendor Header', 'nomor_referensi' => 'REF-HEADER']);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Manifest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManifestTonnageStorageTest extends TestCase
{
    public function test_imported_tonnage_keeps_its_numeric_value_without_fixed_decimal_padding(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->decimal('tonnage', 12, 3)->nullable();
        });

        DB::table('manifests')->insert(['tonnage' => 192]);

        $migration = require database_path('migrations/2026_11_02_150000_change_manifest_tonnage_to_double.php');
        $migration->up();

        DB::table('manifests')->insert(['tonnage' => 192.125]);

        $this->assertSame('double', Schema::getColumnType('manifests', 'tonnage'));
        $this->assertSame('192', (string) Manifest::find(1)->tonnage);
        $this->assertSame('192.125', (string) Manifest::find(2)->tonnage);
    }
}

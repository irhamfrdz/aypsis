<?php

namespace Tests\Unit;

use App\Models\Manifest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManifestTonnageStorageTest extends TestCase
{
    public function test_imported_tonnage_is_stored_and_displayed_with_three_decimal_places(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->double('tonnage')->nullable();
        });

        DB::table('manifests')->insert(['tonnage' => 192]);

        $migration = require database_path('migrations/2026_11_02_160600_restore_manifest_tonnage_decimal.php');
        $migration->up();

        DB::table('manifests')->insert(['tonnage' => 192.125]);

        $this->assertSame('numeric', Schema::getColumnType('manifests', 'tonnage'));
        $this->assertSame('192.000', (string) Manifest::find(1)->tonnage);
        $this->assertSame('192.125', (string) Manifest::find(2)->tonnage);
    }
}

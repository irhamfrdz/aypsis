<?php

namespace Tests\Unit;

use App\Models\Manifest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManifestTonnageStorageTest extends TestCase
{
    public function test_old_tonnage_keeps_three_decimals_and_new_tonnage_keeps_input_format(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->double('tonnage')->nullable();
        });

        DB::table('manifests')->insert(['tonnage' => 192]);
        DB::table('manifests')->insert(['tonnage' => 192.125]);

        $migration = require database_path('migrations/2026_11_02_160600_restore_manifest_tonnage_decimal.php');
        $migration->up();

        $migration = require database_path('migrations/2026_11_02_170000_preserve_manifest_tonnage_format.php');
        $migration->up();

        DB::table('manifests')->insert(['tonnage' => '192']);
        DB::table('manifests')->insert(['tonnage' => '192.5']);

        $this->assertSame('varchar', Schema::getColumnType('manifests', 'tonnage'));
        $this->assertSame('192.000', DB::table('manifests')->where('id', 1)->value('tonnage'));
        $this->assertSame('192.000', Manifest::find(1)->tonnage);
        $this->assertSame('192.125', Manifest::find(2)->tonnage);
        $this->assertSame('192', DB::table('manifests')->where('id', 3)->value('tonnage'));
        $this->assertSame('192', Manifest::find(3)->tonnage);
        $this->assertSame('192.5', Manifest::find(4)->tonnage);
    }
}

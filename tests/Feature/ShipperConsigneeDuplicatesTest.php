<?php

namespace Tests\Feature;

use App\Http\Controllers\ShipperConsigneeController;
use App\Models\ShipperConsignee;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShipperConsigneeDuplicatesTest extends TestCase
{
    public function test_duplicate_filter_compares_every_master_field_and_includes_all_matching_rows(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            foreach ((new ShipperConsignee)->getFillable() as $field) {
                $field === 'status' ? $table->boolean($field)->nullable() : $table->text($field)->nullable();
            }
            $table->timestamps();
        });

        $base = ['shipper' => 'Pengirim A', 'consignee' => 'Penerima A', 'alamat_shipper' => 'Alamat A', 'status' => 1];
        DB::table('shipper_consignees')->insert([
            ['id' => 1, ...$base],
            ['id' => 2, ...$base],
            ['id' => 3, ...$base, 'alamat_shipper' => 'Alamat berbeda'],
            ['id' => 4, ...$base, 'status' => 0],
            ['id' => 5, ...$base, 'shipper' => 'Pengirim B'],
            ['id' => 6, ...$base, 'shipper' => 'Pengirim B'],
        ]);

        $controller = new ShipperConsigneeController;
        $duplicates = $controller->index(Request::create('/master/shipper-consignee', 'GET', ['duplicates' => 1]))
            ->getData()['shipperConsignees'];
        $this->assertSame([6, 5, 2, 1], $duplicates->pluck('id')->all());

        $searched = $controller->index(Request::create('/master/shipper-consignee', 'GET', ['duplicates' => 1, 'search' => 'Pengirim A']))
            ->getData()['shipperConsignees'];
        $this->assertSame([2, 1], $searched->pluck('id')->all());
    }
}

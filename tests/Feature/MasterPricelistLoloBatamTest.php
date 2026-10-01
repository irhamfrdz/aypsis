<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterPricelistLoloBatamController;
use App\Models\MasterPricelistLoloBatam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterPricelistLoloBatamTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('nama_biaya');
            $table->string('size', 10);
            $table->decimal('tarif', 15, 2);
            $table->string('status', 20);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_pricelist_lolo_batam_can_be_created_updated_and_deleted(): void
    {
        $controller = app(MasterPricelistLoloBatamController::class);

        MasterPricelistLoloBatam::withoutEvents(fn () => $controller->store(Request::create('/', 'POST', [
            'vendor' => 'PT Batam Terminal',
            'nama_biaya' => 'Lift On',
            'size' => '20',
            'tarif' => 750000,
            'status' => 'aktif',
            'keterangan' => 'Tarif terminal Batu Ampar',
        ])));

        $pricelist = MasterPricelistLoloBatam::firstOrFail();
        $this->assertSame('PT Batam Terminal', $pricelist->vendor);
        $this->assertSame('750000.00', $pricelist->tarif);

        MasterPricelistLoloBatam::withoutEvents(fn () => $controller->update(Request::create('/', 'PUT', [
            'vendor' => 'PT Batam Terminal',
            'nama_biaya' => 'Lift Off',
            'size' => '40',
            'tarif' => 1250000,
            'status' => 'non-aktif',
            'keterangan' => null,
        ]), $pricelist));

        $this->assertDatabaseHas('master_pricelist_lolo_batams', [
            'id' => $pricelist->id,
            'nama_biaya' => 'Lift Off',
            'size' => '40',
            'status' => 'non-aktif',
        ]);

        MasterPricelistLoloBatam::withoutEvents(fn () => $controller->destroy($pricelist));
        $this->assertSoftDeleted('master_pricelist_lolo_batams', ['id' => $pricelist->id]);
    }
}

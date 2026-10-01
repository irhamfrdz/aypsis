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
            'size' => '20',
            'tarif' => 750000,
            'status' => 'aktif',
            'keterangan' => 'Tarif terminal Batu Ampar',
        ])));

        $pricelist = MasterPricelistLoloBatam::firstOrFail();
        $this->assertSame('750000.00', $pricelist->tarif);

        MasterPricelistLoloBatam::withoutEvents(fn () => $controller->update(Request::create('/', 'PUT', [
            'size' => '40',
            'tarif' => 1250000,
            'status' => 'non-aktif',
            'keterangan' => null,
        ]), $pricelist));

        $this->assertDatabaseHas('master_pricelist_lolo_batams', [
            'id' => $pricelist->id,
            'size' => '40',
            'status' => 'non-aktif',
        ]);

        MasterPricelistLoloBatam::withoutEvents(fn () => $controller->destroy($pricelist));
        $this->assertSoftDeleted('master_pricelist_lolo_batams', ['id' => $pricelist->id]);
    }

    public function test_vendor_and_nama_biaya_columns_are_removed_by_migration(): void
    {
        Schema::table('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->string('vendor')->nullable();
            $table->string('nama_biaya')->nullable();
            $table->index('vendor');
        });

        $migration = require database_path('migrations/2026_10_01_120000_drop_vendor_and_nama_biaya_from_master_pricelist_lolo_batams_table.php');
        $migration->up();

        $this->assertFalse(Schema::hasColumn('master_pricelist_lolo_batams', 'vendor'));
        $this->assertFalse(Schema::hasColumn('master_pricelist_lolo_batams', 'nama_biaya'));

        $migration->down();

        $this->assertTrue(Schema::hasColumn('master_pricelist_lolo_batams', 'vendor'));
        $this->assertTrue(Schema::hasColumn('master_pricelist_lolo_batams', 'nama_biaya'));
    }
}

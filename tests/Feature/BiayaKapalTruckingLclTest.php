<?php

namespace Tests\Feature;

use App\Http\Controllers\BiayaKapalController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class BiayaKapalTruckingLclTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('master_pricelist_biaya_trucking', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor');
            $table->string('size');
            $table->decimal('biaya', 15, 2);
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('size_kontainer')->nullable();
            $table->string('tipe_kontainer')->nullable();
        });
    }

    public function test_lcl_container_uses_lcl_pricelist_instead_of_physical_size_pricelist(): void
    {
        DB::table('master_pricelist_biaya_trucking')->insert([
            [
                'nama_vendor' => 'Vendor A',
                'size' => '20ft',
                'biaya' => 1600000,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_vendor' => 'Vendor A',
                'size' => 'LCL',
                'biaya' => 500000,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $manifestId = DB::table('manifests')->insertGetId([
            'size_kontainer' => '20',
            'tipe_kontainer' => 'LCL',
        ]);

        $method = new ReflectionMethod(BiayaKapalController::class, 'calculateTruckingContainerTotals');
        $totals = $method->invoke(new BiayaKapalController, [
            'nama_vendor' => 'Vendor A',
            'no_bl' => [$manifestId],
        ]);

        $this->assertSame(['20ft' => 500000.0, '40ft' => 0], $totals);
    }
}

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

        $this->assertSame([
            '20ft' => 500000.0,
            '40ft' => 0,
            'subtotal' => 500000.0,
            'adjustments' => [],
        ], $totals);
    }

    public function test_container_adjustment_changes_the_container_and_subtotal_amounts(): void
    {
        DB::table('master_pricelist_biaya_trucking')->insert([
            'nama_vendor' => 'Vendor A',
            'size' => '20ft',
            'biaya' => 1600000,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $manifestId = DB::table('manifests')->insertGetId([
            'size_kontainer' => '20ft',
            'tipe_kontainer' => 'FCL',
        ]);

        $method = new ReflectionMethod(BiayaKapalController::class, 'calculateTruckingContainerTotals');
        $totals = $method->invoke(new BiayaKapalController, [
            'nama_vendor' => 'Vendor A',
            'no_bl' => [$manifestId],
            'container_adjustments' => [$manifestId => -100000],
        ]);

        $this->assertSame(1500000.0, $totals['20ft']);
        $this->assertSame(1500000.0, $totals['subtotal']);
        $this->assertSame(-100000.0, $totals['adjustments'][(string) $manifestId]);
    }
}

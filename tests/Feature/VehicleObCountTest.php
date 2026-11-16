<?php

namespace Tests\Feature;

use App\Services\VehicleObCountService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VehicleObCountTest extends TestCase
{
    public function test_counts_by_id_with_plate_fallback_without_double_counting_or_driver_matching(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->integer('mobil_id')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->date('tanggal_ob');
            $table->string('nama_supir')->default('SUPIR SAMA');
        });
        foreach ([[1, 'B123AA', '2026-11-01'], [1, 'PLAT LAMA', '2026-11-30'],
            [null, 'b 123-aa', '2026-11-15'], [2, 'B123AA', '2026-11-15'],
            [99, 'B123AA', '2026-11-15'], [null, null, '2026-11-15'],
            [1, 'B123AA', '2026-10-31'], [1, 'B123AA', '2026-12-01'],
            [null, 'DUPLIKAT', '2026-11-15']] as [$id, $plate, $date]) {
            DB::table('tagihan_ob')->insert(['mobil_id' => $id, 'nomor_polisi' => $plate, 'tanggal_ob' => $date]);
        }
        $vehicles = collect([
            (object) ['id' => 1, 'nomor_polisi' => 'B 123 AA'],
            (object) ['id' => 2, 'nomor_polisi' => 'B 456 BB'],
            (object) ['id' => 3, 'nomor_polisi' => 'DUPLIKAT'],
            (object) ['id' => 4, 'nomor_polisi' => 'DUPLIKAT'],
        ]);
        $counts = (new VehicleObCountService)->forVehicles($vehicles, '2026-11-01', '2026-11-30');
        $this->assertSame([1 => 3, 2 => 1, 3 => 0, 4 => 0], $counts->all());
    }
}

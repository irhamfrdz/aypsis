<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardPerjalananKendaraanController;
use App\Services\RitSuratJalanService;
use App\Services\VehicleAmprahanUsageService;
use App\Services\VehicleTireReplacementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardPerjalananVehicleSizeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
            $table->string('ukuran_tractor_head')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
        });
        Schema::create('gps_daily_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->date('date');
            $table->decimal('distance_km', 12, 3);
            $table->unsignedInteger('point_count');
            $table->unsignedInteger('idle_seconds')->nullable();
        });

        foreach (['panjang', ' PANJANG ', 'pendek', null, 'lainnya'] as $index => $size) {
            DB::table('mobils')->insert([
                'id' => $index + 1,
                'nomor_polisi' => 'UNIT'.($index + 1),
                'ukuran_tractor_head' => $size,
            ]);
            DB::table('gps_daily_distances')->insert([
                'mobil_id' => $index + 1,
                'date' => $index === 2 ? '2026-11-02' : '2026-11-01',
                'distance_km' => [100, 50, 900, 20, 10][$index],
                'point_count' => 2,
                'idle_seconds' => 60,
            ]);
        }

        $this->mock(VehicleAmprahanUsageService::class)->shouldReceive('forVehicles')->andReturn(collect());
        $this->mock(VehicleTireReplacementService::class)->shouldReceive('summarize')->andReturn(collect());
    }

    public static function vehicleSizeCases(): array
    {
        return [
            'existing combined view' => [[], [3, 1, 2, 4, 5], 1080, 2],
            'long units only' => [['ukuran' => 'panjang'], [1, 2], 150, 1],
            'short units only' => [['ukuran' => 'pendek'], [3], 900, 1],
            'unclassified retained' => [['ukuran' => 'belum_diisi'], [4, 5], 30, 1],
            'search within group' => [['ukuran' => 'panjang', 'search' => 'UNIT2'], [2], 50, 1],
            'empty group search' => [['ukuran' => 'panjang', 'search' => 'UNIT3'], [], 0, 0],
        ];
    }

    #[DataProvider('vehicleSizeCases')]
    public function test_group_applies_to_units_rankings_and_daily_totals(array $filters, array $ids, int $distance, int $days): void
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereNotNull', 'where', 'selectRaw', 'groupBy')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect());
        $rit = Mockery::mock(RitSuratJalanService::class);
        $rit->shouldReceive('regular', 'bongkaran')->andReturn($query);

        $request = Request::create('/', 'GET', $filters + [
            'tanggal_dari' => '2026-11-01',
            'tanggal_sampai' => '2026-11-02',
        ]);
        $data = app(DashboardPerjalananKendaraanController::class)->index($request, $rit)->getData();

        $this->assertSame($ids, $data['vehicleStats']->pluck('mobil_id')->all());
        $this->assertEquals($distance, $data['totalDistance']);
        $this->assertEquals($distance, $data['dailyTotals']->sum('total_km'));
        $this->assertCount($days, $data['dailyTotals']);
        $this->assertSame(array_map(fn ($id) => 'UNIT'.$id, $ids), $data['rankings']['total_km']['rows']->pluck('vehicle')->all());
        $this->assertSame($ids ? range(1, count($ids)) : [], $data['rankings']['total_km']['rows']->pluck('rank')->all());
    }
}

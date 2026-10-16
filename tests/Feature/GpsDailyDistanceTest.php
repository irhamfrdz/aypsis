<?php

namespace Tests\Feature;

use App\Services\GpsDailyDistanceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GpsDailyDistanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('gps_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->integer('speed')->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('gps_daily_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->date('date');
            $table->decimal('distance_km', 12, 3)->default(0);
            $table->unsignedInteger('point_count')->default(0);
            $table->timestamps();
            $table->unique(['mobil_id', 'date']);
        });
    }

    public function test_daily_distance_is_saved_once_per_car_and_day(): void
    {
        $this->point(1, '-6.20000000', '106.80000000', '2026-10-16 08:00:00');
        $this->point(1, '-6.20000000', '106.81000000', '2026-10-16 08:05:00', 20);
        $this->point(1, '-6.20000000', '106.81000000', '2026-10-16 08:10:00');
        $this->point(1, '-6.20000000', '109.81000000', '2026-10-16 08:15:00');
        $this->point(1, '-6.20000000', '106.80000000', '2026-10-17 08:00:00');
        $this->point(2, '-6.20000000', '106.80000000', '2026-10-16 08:00:00');
        $this->point(2, '-6.20000000', '106.81000000', '2026-10-16 08:05:00');

        $service = app(GpsDailyDistanceService::class);
        $service->recalculate(1, '2026-10-16');
        $service->recalculate(1, '2026-10-16');
        $service->recalculate(1, '2026-10-17');
        $service->recalculate(2, '2026-10-16');

        $this->assertSame(3, DB::table('gps_daily_distances')->count());
        $this->assertEqualsWithDelta(1.105, (float) DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-16')->value('distance_km'), 0.02);
        $this->assertEquals(0, DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-17')->value('distance_km'));
        $this->assertEquals(0, DB::table('gps_daily_distances')->where('mobil_id', 2)->where('date', '2026-10-16')->value('distance_km'));
    }

    private function point(int $mobilId, string $lat, string $lng, string $recordedAt, int $speed = 0): void
    {
        DB::table('gps_histories')->insert([
            'mobil_id' => $mobilId,
            'lat' => $lat,
            'lng' => $lng,
            'speed' => $speed,
            'recorded_at' => $recordedAt,
            'created_at' => $recordedAt,
            'updated_at' => $recordedAt,
        ]);
    }
}

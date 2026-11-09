<?php

use App\Models\GpsDailyDistance;
use App\Services\GpsIdleDurationService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_daily_distances', function (Blueprint $table) {
            $table->unsignedInteger('idle_seconds')->nullable()->after('distance_km');
        });

        // Riwayat GPS mentah hanya disimpan 14 hari. Hari ke-14 mungkin
        // sudah terpotong, jadi isi hanya hari yang riwayatnya masih utuh.
        $idleService = app(GpsIdleDurationService::class);
        GpsDailyDistance::where('date', '>=', now()->subDays(13)->toDateString())
            ->where('point_count', '>', 0)
            ->whereNull('idle_seconds')
            ->chunkById(200, function ($days) use ($idleService) {
                foreach ($days as $day) {
                    $seconds = $idleService->forDay($day->mobil_id, $day->date);
                    if ($seconds !== null) {
                        $day->update(['idle_seconds' => $seconds]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('gps_daily_distances', function (Blueprint $table) {
            $table->dropColumn('idle_seconds');
        });
    }
};

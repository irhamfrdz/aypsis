<?php

namespace App\Services;

use App\Models\GpsDailyDistance;
use App\Models\GpsHistory;

class GpsDailyDistanceService
{
    public function recalculate(int $mobilId, string $date): void
    {
        $points = GpsHistory::where('mobil_id', $mobilId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->orderBy('id')
            ->get(['lat', 'lng', 'speed', 'recorded_at']);

        $distanceKm = 0.0;
        $previous = null;

        foreach ($points as $point) {
            if (! $this->validCoordinates($point->lat, $point->lng)) {
                $previous = null;
                continue;
            }

            if ($previous !== null) {
                $seconds = $previous->recorded_at->diffInSeconds($point->recorded_at);

                // Long gaps cannot describe the route; ignore GPS drift and implausible jumps.
                if ($seconds > 0 && $seconds <= 1800
                    && ((float) $previous->speed > 0 || (float) $point->speed > 0)) {
                    $segmentKm = $this->haversineKm(
                        (float) $previous->lat,
                        (float) $previous->lng,
                        (float) $point->lat,
                        (float) $point->lng
                    );

                    if ($segmentKm >= 0.03 && $segmentKm / ($seconds / 3600) <= 140) {
                        $distanceKm += $segmentKm;
                    }
                }
            }

            $previous = $point;
        }

        if ($points->isNotEmpty()) {
            GpsDailyDistance::updateOrCreate(
                ['mobil_id' => $mobilId, 'date' => $date],
                ['distance_km' => round($distanceKm, 3), 'point_count' => $points->count()]
            );
        }
    }

    private function validCoordinates($lat, $lng): bool
    {
        return is_numeric($lat) && is_numeric($lng)
            && abs((float) $lat) <= 90 && abs((float) $lng) <= 180
            && ! ((float) $lat === 0.0 && (float) $lng === 0.0);
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return 6371.0088 * 2 * atan2(sqrt($a), sqrt(max(0, 1 - $a)));
    }
}

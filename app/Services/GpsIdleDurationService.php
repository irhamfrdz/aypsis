<?php

namespace App\Services;

use App\Models\GpsHistory;
use Illuminate\Support\Collection;

class GpsIdleDurationService
{
    public function forDay(int $mobilId, string $date): ?int
    {
        $points = GpsHistory::where('mobil_id', $mobilId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->orderBy('id')
            ->get(['speed', 'status', 'recorded_at']);

        return $points->isEmpty() ? null : $this->calculate($points);
    }

    public function calculate(Collection $points): int
    {
        $seconds = 0;
        $previous = null;

        foreach ($points as $point) {
            if ($previous !== null && $this->isIdle($previous) && $this->isIdle($point)) {
                $gap = $previous->recorded_at->diffInSeconds($point->recorded_at);
                if ($gap > 0 && $gap <= 1800) {
                    $seconds += $gap;
                }
            }

            $previous = $point;
        }

        return $seconds;
    }

    private function isIdle(GpsHistory $point): bool
    {
        return (int) $point->speed === 0 && $point->status === 'Mesin Menyala';
    }
}

<?php

namespace App\Services;

use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{
    private ?array $activeSchedules = null;

    /**
     * Return the configured start/end times and grace period for an employee's department.
     * Legacy defaults are retained when no schedule exists (or Saturday hours are unset).
     */
    public function scheduleFor(Karyawan $karyawan, CarbonInterface|string $date): array
    {
        $day = Carbon::parse($date);
        $isSaturday = $day->isSaturday();
        $schedule = $this->scheduleForDepartment($karyawan->departemen);

        if ($isSaturday) {
            $start = $schedule->jam_masuk_sabtu ?? null;
            $end = $schedule->jam_keluar_sabtu ?? null;

            if (! $start || ! $end) {
                return [
                    'start' => $day->copy()->setTime(8, 0),
                    'end' => $day->copy()->setTime(13, 0),
                    'grace_minutes' => 5,
                ];
            }

            return [
                'start' => $day->copy()->setTimeFromTimeString($start),
                'end' => $day->copy()->setTimeFromTimeString($end),
                'grace_minutes' => (int) ($schedule->toleransi_keterlambatan_sabtu ?? 0),
            ];
        }

        if (! $schedule) {
            return [
                'start' => $day->copy()->setTime(9, 0),
                'end' => $day->copy()->setTime(17, 0),
                'grace_minutes' => 5,
            ];
        }

        return [
            'start' => $day->copy()->setTimeFromTimeString($schedule->jam_masuk),
            'end' => $day->copy()->setTimeFromTimeString($schedule->jam_keluar),
            'grace_minutes' => (int) ($schedule->toleransi_keterlambatan ?? 0),
        ];
    }

    /** Return late minutes from scheduled start, or zero when within the allowed grace period. */
    public function lateMinutes(Karyawan $karyawan, CarbonInterface|string $date, CarbonInterface|string $clockIn): int
    {
        $schedule = $this->scheduleFor($karyawan, $date);
        $actual = $clockIn instanceof CarbonInterface
            ? Carbon::parse($clockIn)
            : Carbon::parse(Carbon::parse($date)->toDateString().' '.$clockIn);
        $threshold = $schedule['start']->copy()->addMinutes($schedule['grace_minutes']);

        if ($actual->lte($threshold)) {
            return 0;
        }

        return (int) round($schedule['start']->diffInMinutes($actual));
    }

    private function scheduleForDepartment(?string $department): ?object
    {
        if (! $department) {
            return null;
        }

        if ($this->activeSchedules === null) {
            $this->activeSchedules = DB::table('jam_kerjas')
                ->where('is_active', 1)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(fn ($schedule) => [mb_strtolower(trim($schedule->departemen)) => $schedule])
                ->all();
        }

        return $this->activeSchedules[mb_strtolower(trim($department))] ?? null;
    }
}

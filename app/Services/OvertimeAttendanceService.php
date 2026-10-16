<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OvertimeAttendanceService
{
    private const START_TYPES = ['lembur masuk', 'mulai lembur', 'lembur'];

    private const END_TYPES = ['lembur pulang', 'selesai lembur', 'lembur keluar'];

    /** Return overtime start/end pairs grouped by employee and work date. */
    public function dailyForEmployees(Collection $employees, Carbon $startDate, Carbon $endDate): Collection
    {
        $ids = $employees->pluck('id')->filter()->unique()->values()->all();
        if ($ids === []) {
            return collect();
        }

        $niks = $employees->pluck('nik')->filter()->unique()->values()->all();
        $idsSet = array_fill_keys($ids, true);
        $idsByNik = [];
        foreach ($employees as $employee) {
            if ($employee->nik) {
                $idsByNik[$employee->nik][] = $employee->id;
            }
        }

        $windowStart = $startDate->copy()->subDay()->startOfDay();
        $windowEnd = $endDate->copy()->addDays(2)->startOfDay();
        $typeSql = "LOWER(REPLACE(tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur', 'lembur pulang', 'selesai lembur', 'lembur keluar')";

        $events = DB::table('absensis')
            ->select('id', 'karyawan_id', 'nik', 'waktu', 'tipe')
            ->where('waktu', '>=', $windowStart)
            ->where('waktu', '<', $windowEnd)
            ->whereRaw($typeSql)
            ->where(function ($query) use ($ids, $niks) {
                $query->whereIn('karyawan_id', $ids);
                if ($niks !== []) {
                    $query->orWhereIn('nik', $niks);
                }
            })
            ->orderBy('waktu')
            ->orderBy('id')
            ->get();

        $firstDate = $startDate->toDateString();
        $lastDate = $endDate->toDateString();
        $outerStart = $startDate->copy()->startOfDay()->toDateTimeString();
        $lastStart = [];
        $lastEnd = [];
        $daily = [];

        // End events at the same timestamp cannot cancel one another's start.
        foreach ($events->groupBy('waktu') as $timestamp => $sameTimeEvents) {
            $ends = [];
            $starts = [];

            foreach ($sameTimeEvents as $event) {
                $type = strtolower(str_replace('_', ' ', $event->tipe));
                $targets = [];
                if ($event->karyawan_id !== null && isset($idsSet[$event->karyawan_id])) {
                    $targets[$event->karyawan_id] = true;
                }
                foreach ($idsByNik[$event->nik] ?? [] as $id) {
                    $targets[$id] = true;
                }

                if (in_array($type, self::END_TYPES, true)) {
                    $ends[] = [$event, array_keys($targets)];
                } elseif (in_array($type, self::START_TYPES, true)) {
                    $starts[] = [$event, array_keys($targets)];
                }
            }

            foreach ($ends as [$event, $targets]) {
                $employeeId = $event->karyawan_id;
                if ($employeeId !== null && isset($idsSet[$employeeId]) && $event->waktu >= $outerStart) {
                    $start = $lastStart[$employeeId] ?? null;
                    $hasMatchedStart = $start !== null
                        && $start < $event->waktu
                        && (! isset($lastEnd[$employeeId]) || $lastEnd[$employeeId] < $start)
                        && Carbon::parse($start)->gte(Carbon::parse($event->waktu)->subDay());
                    $date = substr($hasMatchedStart ? $start : $event->waktu, 0, 10);

                    if ($date >= $firstDate && $date <= $lastDate) {
                        $daily[$employeeId][$date] ??= (object) [
                            'karyawan_id' => $employeeId,
                            'tanggal' => $date,
                            'waktu_lembur_masuk' => null,
                            'waktu_lembur_pulang' => null,
                        ];
                        if ($daily[$employeeId][$date]->waktu_lembur_pulang === null
                            || $event->waktu > $daily[$employeeId][$date]->waktu_lembur_pulang) {
                            $daily[$employeeId][$date]->waktu_lembur_pulang = $event->waktu;
                        }
                    }
                }
            }

            foreach ($ends as [, $targets]) {
                foreach ($targets as $id) {
                    unset($lastStart[$id]);
                    $lastEnd[$id] = $timestamp;
                }
            }

            foreach ($starts as [$event, $targets]) {
                foreach ($targets as $id) {
                    $lastStart[$id] = $event->waktu;
                }

                $employeeId = $event->karyawan_id;
                if ($employeeId !== null && isset($idsSet[$employeeId]) && $event->waktu >= $outerStart) {
                    $date = substr($event->waktu, 0, 10);
                    if ($date >= $firstDate && $date <= $lastDate) {
                        $daily[$employeeId][$date] ??= (object) [
                            'karyawan_id' => $employeeId,
                            'tanggal' => $date,
                            'waktu_lembur_masuk' => null,
                            'waktu_lembur_pulang' => null,
                        ];
                        if ($daily[$employeeId][$date]->waktu_lembur_masuk === null
                            || $event->waktu < $daily[$employeeId][$date]->waktu_lembur_masuk) {
                            $daily[$employeeId][$date]->waktu_lembur_masuk = $event->waktu;
                        }
                    }
                }
            }
        }

        return collect($daily)->map(fn ($days) => collect(array_values($days)));
    }
}

<?php

namespace App\Services;

use App\Http\Controllers\PerhitunganLemburController;
use App\Models\Karyawan;
use App\Models\PranotaLemburKaryawan;
use App\Models\PranotaLemburKaryawanHeader;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PranotaLemburEmployeeService
{
    public function period(PranotaLemburKaryawanHeader $pranota): array
    {
        return [
            $pranota->periode_mulai ?? $pranota->tanggal_pranota->copy()->startOfMonth(),
            $pranota->periode_selesai ?? $pranota->tanggal_pranota->copy()->endOfMonth(),
        ];
    }

    public function candidates(PranotaLemburKaryawanHeader $pranota, ?array $ids = null, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        [$defaultStart, $defaultEnd] = $this->period($pranota);
        $start ??= $defaultStart;
        $end ??= $defaultEnd;
        $employees = Karyawan::whereNotIn('id', $pranota->karyawans()->select('karyawan_id'))
            ->when($ids !== null, fn ($query) => $query->whereIn('id', $ids))
            ->whereHas('absensi', function ($query) use ($start, $end) {
                $query->where('waktu', '>=', $start->copy()->startOfDay())
                    ->where('waktu', '<', $end->copy()->addDays(2)->startOfDay())
                    ->whereIn(DB::raw("LOWER(REPLACE(tipe, '_', ' '))"), ['lembur masuk', 'mulai lembur', 'lembur']);
            })->orderBy('nama_lengkap')->get();
        if ($employees->isEmpty()) {
            return collect();
        }

        // Explicit dates are authoritative. Legacy details reserve their full period.
        $excluded = [];
        $existing = PranotaLemburKaryawan::with('pranotaLemburKaryawanHeader')
            ->whereIn('karyawan_id', $employees->pluck('id'))
            ->whereHas('pranotaLemburKaryawanHeader')->get();
        foreach ($existing as $detail) {
            if (is_array($detail->tanggal_lembur) && count($detail->tanggal_lembur) > 0) {
                foreach ($detail->tanggal_lembur as $date) {
                    $excluded[$detail->karyawan_id][$date] = true;
                }
                continue;
            }
            $header = $detail->pranotaLemburKaryawanHeader;
            [$headerStart, $headerEnd] = $this->period($header);
            $from = Carbon::parse($detail->periode_mulai ?? $headerStart)->max($start);
            $to = Carbon::parse($detail->periode_selesai ?? $headerEnd)->min($end);
            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $excluded[$detail->karyawan_id][$day->toDateString()] = true;
            }
        }
        $calculated = PerhitunganLemburController::calculateAttendanceForKaryawans(
            $employees->pluck('id'), $start->toDateString(), $end->toDateString(), $excluded
        );

        return $employees->map(function ($employee) use ($calculated) {
            $result = $calculated[$employee->id] ?? null;
            if (! $result || $result['total_jam'] <= 0 || empty($result['tanggal_lembur'])) {
                return null;
            }
            return array_merge($result, ['nama_lengkap' => $employee->nama_lengkap, 'nik' => $employee->nik]);
        })->filter()->values();
    }
}

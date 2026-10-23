<?php

namespace App\Services;

use App\Helpers\AttendanceEventType;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class OvertimeCheckoutClassifier
{
    public function resolve(string $nik, string $waktu, string $type, ?int $employeeId = null): string
    {
        $type = AttendanceEventType::normalize($type) ?? $type;
        if ($type !== 'Pulang' || ($nik === '' && ! $employeeId)) {
            return $type;
        }
        $time = Carbon::parse($waktu);
        $employeeLogs = function () use ($nik, $employeeId): Builder {
            return DB::table('absensis')->where(function (Builder $query) use ($nik, $employeeId) {
                if ($nik !== '') {
                    $query->where('nik', $nik);
                } else {
                    $query->where('karyawan_id', $employeeId);
                }
            });
        };
        $start = $employeeLogs()->whereRaw("LOWER(REPLACE(tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')")
            ->where('waktu', '>=', $time->copy()->subDay()->toDateTimeString())
            ->where('waktu', '<', $time->toDateTimeString())->orderByDesc('waktu')->orderByDesc('id')->first();
        if (! $start) {
            return $type;
        }

        // Do not infer overtime from the clock alone: require a regular checkout
        // before an open overtime session, with no new regular shift in between.
        $closedOrNewShift = $employeeLogs()->where('waktu', '>', $start->waktu)
            ->where('waktu', '<', $time->toDateTimeString())
            ->whereRaw("LOWER(REPLACE(tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar', 'pulang', 'keluar', 'check out', 'masuk', 'check in')")->exists();
        if ($closedOrNewShift) {
            return $type;
        }
        $regularCheckout = $employeeLogs()->where('waktu', '>=', Carbon::parse($start->waktu)->startOfDay()->toDateTimeString())
            ->where('waktu', '<', $start->waktu)
            ->whereRaw("LOWER(tipe) IN ('pulang', 'keluar', 'check out')")->exists();

        return $regularCheckout ? 'lembur_pulang' : $type;
    }
}

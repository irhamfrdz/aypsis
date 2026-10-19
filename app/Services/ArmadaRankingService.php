<?php

namespace App\Services;

use App\Models\ArmadaUnavailability;
use App\Models\GpsDailyDistance;
use App\Models\Mobil;
use App\Models\SuratJalan;
use App\Models\SuratJalanBatam;
use App\Models\SuratJalanBongkaran;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\SuratJalanTarikKosongBatam;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArmadaRankingService
{
    public function build(Collection $mobils, CarbonInterface $endDate, int $periodDays = 7): Collection
    {
        if ($mobils->isEmpty()) {
            return collect();
        }
        $startDate = $endDate->copy()->startOfDay()->subDays($periodDays - 1);
        $dates = collect(range(0, $periodDays - 1))->map(fn ($day) => $startDate->copy()->addDays($day)->toDateString());
        $plateExpression = "UPPER(REPLACE(REPLACE(TRIM(nomor_polisi), ' ', ''), '-', ''))";
        $plateKeys = $mobils->map(fn ($mobil) => $this->plateKey($mobil->nomor_polisi))->unique();
        $plateCounts = Mobil::whereIn(DB::raw($plateExpression), $plateKeys)
            ->selectRaw($plateExpression.' AS plate_key, COUNT(*) AS total')
            ->groupByRaw($plateExpression)->pluck('total', 'plate_key');
        $validPlates = $plateKeys->filter(fn ($plate) => $plate !== '' && (int) $plateCounts->get($plate) === 1);
        $distances = GpsDailyDistance::whereIn('mobil_id', $mobils->pluck('id'))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()->groupBy('mobil_id')->map(fn ($items) => $items->keyBy('date'));
        $unavailable = ArmadaUnavailability::whereIn('mobil_id', $mobils->pluck('id'))
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $startDate->toDateString()))
            ->get()->groupBy('mobil_id');
        $trips = $this->completedTrips($validPlates, $startDate, $endDate)
            ->groupBy(fn ($trip) => $this->plateKey($trip->no_plat));

        $rows = $mobils->map(function ($mobil) use ($validPlates, $dates, $distances, $unavailable, $trips, $periodDays) {
            $plateValid = $validPlates->contains($this->plateKey($mobil->nomor_polisi));
            $vehicleTrips = $plateValid ? $trips->get($this->plateKey($mobil->nomor_polisi), collect()) : collect();
            $tripsByDate = $vehicleTrips->groupBy(fn ($trip) => substr($trip->completed_on, 0, 10));
            $gpsByDate = $distances->get($mobil->id, collect());
            $periods = $unavailable->get($mobil->id, collect());
            $metrics = array_fill_keys(['available_days', 'unavailable_days', 'productive_days', 'moving_without_rit_days', 'idle_days', 'unknown_days', 'gps_missing_days', 'conflict_days'], 0);
            $gpsKm = 0.0;
            $gpsKnownDays = 0;
            $availableTrips = collect();
            $daily = collect();
            foreach ($dates as $date) {
                $dayTrips = $tripsByDate->get($date, collect());
                $gps = $gpsByDate->get($date);
                $distance = $gps !== null && $gps->point_count > 0 && (float) $gps->distance_km >= 0 ? (float) $gps->distance_km : null;
                $isUnavailable = $periods->contains(fn ($period) => $date >= $period->start_date->toDateString()
                    && ($period->end_date === null || $date <= $period->end_date->toDateString()));
                if ($isUnavailable) {
                    $metrics['unavailable_days']++;
                    if ($dayTrips->isNotEmpty() || $distance > 0) {
                        $metrics['conflict_days']++;
                    }
                    $state = 'Tidak tersedia';
                } else {
                    $metrics['available_days']++;
                    $availableTrips = $availableTrips->concat($dayTrips);
                    if ($distance === null) {
                        $metrics['gps_missing_days']++;
                    } else {
                        $gpsKnownDays++;
                        $gpsKm += $distance;
                    }
                    if ($dayTrips->isNotEmpty()) {
                        $metrics['productive_days']++;
                        $state = 'Menghasilkan rit';
                    } elseif ($distance === null) {
                        $metrics['unknown_days']++;
                        $state = 'Data belum lengkap';
                    } elseif ($distance > 0) {
                        $metrics['moving_without_rit_days']++;
                        $state = 'Bergerak tanpa rit selesai';
                    } else {
                        $metrics['idle_days']++;
                        $state = 'Tanpa aktivitas tercatat';
                    }
                }
                $daily->push(['date' => $date, 'state' => $state, 'rit' => $dayTrips->count(), 'gps_km' => $distance]);
            }
            $availableDays = $metrics['available_days'];
            $reason = match (true) {
                ! $plateValid => 'Plat kosong atau duplikat; periksa master kendaraan.',
                $availableDays === 0 => 'Tidak tersedia selama seluruh periode.',
                $metrics['conflict_days'] > 0 => 'Ada aktivitas pada hari tidak tersedia; periksa pencatatan.',
                $gpsKnownDays === 0 && $availableTrips->isEmpty() => 'Belum ada data aktivitas pada periode ini.',
                default => null,
            };
            $lokasi = strtoupper(trim($mobil->lokasi ?? '')) ?: 'TANPA LOKASI';
            $jenis = strtoupper(trim($mobil->jenis ?? '')) ?: 'JENIS BELUM DIISI';

            return array_merge($metrics, [
                'mobil' => $mobil, 'plate_valid' => $plateValid,
                'group' => $lokasi.' / '.$jenis,
                'period_days' => $periodDays,
                'rit' => $plateValid ? $availableTrips->count() : null,
                'rit_per_supir' => $availableTrips->filter(fn ($trip) => trim($trip->supir ?? '') !== '')
                    ->groupBy(fn ($trip) => strtoupper(trim($trip->supir)))->map(fn ($items) => $items->count()),
                'gps_km' => $gpsKnownDays > 0 ? $gpsKm : null,
                'utilization' => $plateValid && $availableDays > 0 ? $metrics['productive_days'] / $availableDays * 100 : null,
                'average_rit' => $plateValid && $availableDays > 0 ? $availableTrips->count() / $availableDays : null,
                'gps_coverage' => $availableDays > 0 ? $gpsKnownDays / $availableDays * 100 : null,
                'daily' => $daily,
                'unranked_reason' => $reason,
                'data_note' => $metrics['gps_missing_days'] > 0 ? 'Data GPS belum lengkap; utilisasi dihitung dari rit yang tercatat pada seluruh hari tersedia.' : null,
            ]);
        })->sort(function ($a, $b) {
            return (($a['unranked_reason'] !== null) <=> ($b['unranked_reason'] !== null))
                ?: (($b['utilization'] ?? -1) <=> ($a['utilization'] ?? -1))
                ?: (($b['average_rit'] ?? -1) <=> ($a['average_rit'] ?? -1))
                ?: strnatcasecmp($a['mobil']->nomor_polisi ?? '', $b['mobil']->nomor_polisi ?? '')
                ?: ($a['mobil']->id <=> $b['mobil']->id);
        })->values();
        $rank = 0;
        $isProvisional = $rows->whereNull('unranked_reason')->contains(fn ($row) => $row['gps_missing_days'] > 0);

        return $rows->map(function ($row) use (&$rank, $isProvisional) {
            $row['rank'] = $row['unranked_reason'] === null ? ++$rank : null;
            $row['rank_is_provisional'] = $row['rank'] !== null && $isProvisional;

            return $row;
        });
    }

    private function completedTrips(Collection $validPlates, CarbonInterface $startDate, CarbonInterface $endDate): Collection
    {
        $trips = collect();
        if ($validPlates->isEmpty()) {
            return $trips;
        }
        foreach ([
            [SuratJalan::class, 'tanggal'],
            [SuratJalanBongkaran::class, 'tanggal_tanda_terima'],
            [SuratJalanBatam::class, 'tanggal'],
            [SuratJalanBongkaranBatam::class, 'tanggal_tanda_terima'],
            [SuratJalanTarikKosongBatam::class, 'tanggal_tanda_terima'],
        ] as [$modelClass, $dateColumn]) {
            $model = new $modelClass;
            $table = $model->getTable();
            $relation = $model->tandaTerima();
            $receipt = $relation->getRelated();
            $completedDate = $receipt->newQuery()->selectRaw('MIN('.$receipt->qualifyColumn($dateColumn).')')
                ->whereColumn($relation->getQualifiedForeignKeyName(), $relation->getQualifiedParentKeyName());
            $source = $model->newQuery()->select([$table.'.no_plat', $table.'.supir'])
                ->selectSub($completedDate, 'completed_on')
                ->whereIn(DB::raw("UPPER(REPLACE(REPLACE(TRIM(no_plat), ' ', ''), '-', ''))"), $validPlates)
                ->where(fn ($query) => $query->whereNull('status')->orWhereNotIn(
                    DB::raw('LOWER(TRIM(status))'), ['batal', 'dibatalkan', 'cancelled', 'canceled', 'rejected']
                ))
                ->where(fn ($query) => $query->whereNull('rit')->orWhere('rit', '!=', 'tidak_menggunakan_rit'));
            $trips = $trips->concat(DB::query()->fromSub($source->toBase(), 'completed_trips')
                ->whereDate('completed_on', '>=', $startDate->toDateString())
                ->whereDate('completed_on', '<=', $endDate->toDateString())->get());
        }

        return $trips;
    }

    private function plateKey(?string $plate): string
    {
        return strtoupper(str_replace([' ', '-'], '', trim($plate ?? '')));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\GpsDailyDistance;
use App\Services\RitSuratJalanService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPerjalananKendaraanController extends Controller
{
    public function index(Request $request, RitSuratJalanService $ritService)
    {
        $hasReport = $request->hasAny(['tanggal_dari', 'tanggal_sampai']);
        $tanggalDari = (string) $request->query('tanggal_dari', '');
        $tanggalSampai = (string) $request->query('tanggal_sampai', '');
        $vehicleStats = collect();
        $dailyTotals = collect();
        $totalDistance = 0.0;
        $maxVehicleDistance = 0.0;
        $maxDailyDistance = 0.0;

        if ($hasReport) {
            $dates = $request->validate([
                'tanggal_dari' => ['required', 'date_format:Y-m-d'],
                'tanggal_sampai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_dari'],
            ]);

            $tanggalDari = $dates['tanggal_dari'];
            $tanggalSampai = $dates['tanggal_sampai'];

            $vehicleStats = GpsDailyDistance::query()
                ->select('mobil_id')
                ->selectRaw('SUM(distance_km) as total_km, COUNT(*) as days_with_data, SUM(CASE WHEN distance_km > 0 THEN 1 ELSE 0 END) as moving_days')
                ->whereBetween('date', [$tanggalDari, $tanggalSampai])
                ->where('point_count', '>', 0)
                ->groupBy('mobil_id')
                ->with('mobil.karyawan')
                ->get()
                ->sortByDesc(fn ($row) => (float) $row->total_km)
                ->values();

            if ($vehicleStats->isNotEmpty()) {
                $start = Carbon::parse($tanggalDari)->startOfDay();
                $end = Carbon::parse($tanggalSampai)->endOfDay();
                $ritCounts = [];
                $plateVariants = [];

                foreach ([$ritService->regular($start, $end), $ritService->bongkaran($start, $end)] as $query) {
                    $counts = $query
                        ->whereNotNull('no_plat')
                        ->where('no_plat', '!=', '')
                        ->selectRaw('no_plat, COUNT(*) as rit_count')
                        ->groupBy('no_plat')
                        ->get();

                    foreach ($counts as $count) {
                        $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($count->no_plat));
                        if ($plate !== '') {
                            $ritCounts[$plate] = ($ritCounts[$plate] ?? 0) + (int) $count->rit_count;
                            $plateVariants[$plate][] = $count->no_plat;
                        }
                    }
                }

                $vehiclePlates = $vehicleStats
                    ->map(fn ($row) => preg_replace('/[^A-Z0-9]/', '', strtoupper($row->mobil?->nomor_polisi ?? '')))
                    ->filter()
                    ->unique();
                $matchingPlates = $vehiclePlates
                    ->flatMap(fn ($plate) => $plateVariants[$plate] ?? [])
                    ->unique()
                    ->values()
                    ->all();
                $ritDetails = [];

                if ($matchingPlates !== []) {
                    foreach (['regular', 'bongkaran'] as $type) {
                        $suratJalans = $ritService->{$type}($start, $end)
                            ->whereIn('no_plat', $matchingPlates)
                            ->select($type === 'regular'
                                ? ['id', 'no_plat', 'tanggal_checkpoint', 'tanggal_tanda_terima', 'tanggal_surat_jalan', 'no_surat_jalan', 'kegiatan', 'status', 'supir', 'supir2', 'pengirim', 'tujuan_pengambilan']
                                : ['id', 'no_plat', 'tanggal_checkpoint', 'nomor_surat_jalan', 'kegiatan', 'supir', 'supir2', 'pengirim', 'tujuan_pengambilan'])
                            ->with($type === 'regular' ? ['tandaTerima', 'pengirimRelation'] : ['tandaTerima'])
                            ->get();

                        foreach ($suratJalans as $suratJalan) {
                            $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($suratJalan->no_plat));
                            $dateCandidates = [
                                $suratJalan->tanggal_checkpoint,
                                $type === 'regular' ? $suratJalan->tandaTerima?->tanggal : $suratJalan->tandaTerima?->tanggal_tanda_terima,
                                $type === 'regular' && $suratJalan->kegiatan === 'bongkaran' ? $suratJalan->tanggal_tanda_terima : null,
                                $type === 'regular' && $suratJalan->status === 'approved' ? $suratJalan->tanggal_surat_jalan : null,
                            ];
                            $date = collect($dateCandidates)->first(fn ($candidate) => $candidate
                                && Carbon::parse($candidate)->betweenIncluded($start, $end));

                            $ritDetails[$plate][] = [
                                'tanggal' => $date ? Carbon::parse($date)->format('d/m/Y') : '-',
                                'tanggal_sort' => $date ? Carbon::parse($date)->toDateString() : '',
                                'nomor' => $type === 'regular' ? $suratJalan->no_surat_jalan : $suratJalan->nomor_surat_jalan,
                                'jenis' => $type === 'regular' ? 'Surat jalan' : 'Bongkaran',
                                'kegiatan' => $suratJalan->kegiatan,
                                'supir' => $suratJalan->supir ?: $suratJalan->supir2,
                                'pengirim' => $type === 'regular'
                                    ? ($suratJalan->pengirimRelation?->nama_pengirim ?: $suratJalan->pengirim)
                                    : $suratJalan->pengirim,
                                'tujuan_ambil' => $suratJalan->tujuan_pengambilan,
                            ];
                        }
                    }
                }

                foreach ($vehicleStats as $row) {
                    $plate = preg_replace('/[^A-Z0-9]/', '', strtoupper($row->mobil?->nomor_polisi ?? ''));
                    $row->rit_count = $ritCounts[$plate] ?? 0;
                    $row->rit_details = collect($ritDetails[$plate] ?? [])
                        ->sortByDesc('tanggal_sort')
                        ->values();
                }
            }

            $dailyTotals = GpsDailyDistance::query()
                ->select('date')
                ->selectRaw('SUM(distance_km) as total_km, COUNT(*) as vehicle_count')
                ->whereBetween('date', [$tanggalDari, $tanggalSampai])
                ->where('point_count', '>', 0)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $totalDistance = (float) $vehicleStats->sum('total_km');
            $maxVehicleDistance = (float) $vehicleStats->max('total_km');
            $maxDailyDistance = (float) $dailyTotals->max('total_km');
        }

        return view('dashboard-perjalanan-kendaraan.index', compact(
            'hasReport', 'tanggalDari', 'tanggalSampai', 'vehicleStats', 'dailyTotals',
            'totalDistance', 'maxVehicleDistance', 'maxDailyDistance'
        ));
    }
}

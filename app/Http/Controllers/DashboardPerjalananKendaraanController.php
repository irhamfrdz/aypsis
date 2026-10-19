<?php

namespace App\Http\Controllers;

use App\Models\GpsDailyDistance;
use Illuminate\Http\Request;

class DashboardPerjalananKendaraanController extends Controller
{
    public function index(Request $request)
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

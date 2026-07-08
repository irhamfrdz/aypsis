<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Mesin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Display a listing of attendance logs.
     */
    /**
     * Parse date safely from multiple possible formats.
     */
    private function parseDateSafe($dateStr, $default)
    {
        if (empty($dateStr)) {
            return Carbon::parse($default);
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $dateStr);
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/Y', $dateStr);
            } catch (\Exception $e2) {
                try {
                    return Carbon::parse($dateStr);
                } catch (\Exception $e3) {
                    return Carbon::parse($default);
                }
            }
        }
    }

    public function index(Request $request)
    {
        $query = Absensi::with(['karyawan']);

        // Filter by Date Range safely
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd = Carbon::now()->endOfMonth()->toDateString();

        $startDateObj = $this->parseDateSafe($request->input('start_date'), $defaultStart);
        $endDateObj = $this->parseDateSafe($request->input('end_date'), $defaultEnd);

        $startDate = $startDateObj->toDateString();
        $endDate = $endDateObj->toDateString();

        $query->whereBetween('waktu', [
            $startDateObj->startOfDay(),
            $endDateObj->endOfDay(),
        ]);

        // Filter by Search (Employee Name, NIK)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function ($kQ) use ($search) {
                        $kQ->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nama_panggilan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Pekerjaan
        if ($request->filled('pekerjaan')) {
            $pekerjaan = $request->pekerjaan;
            $query->whereHas('karyawan', function ($kQ) use ($pekerjaan) {
                $kQ->where('pekerjaan', $pekerjaan);
            });
        }

        // Filter by Divisi
        if ($request->filled('divisi')) {
            $divisi = $request->divisi;
            $query->whereHas('karyawan', function ($kQ) use ($divisi) {
                $kQ->where('divisi', $divisi);
            });
        }

        // Build the daily grouped query
        $query->selectRaw('
            karyawan_id,
            nik,
            DATE(waktu) as tanggal,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN waktu ELSE NULL END) as waktu_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN waktu ELSE NULL END) as waktu_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN mesin_id ELSE NULL END) as mesin_id_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN mesin_id ELSE NULL END) as mesin_id_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN device ELSE NULL END) as device_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN device ELSE NULL END) as device_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN latitude ELSE NULL END) as latitude_masuk,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN longitude ELSE NULL END) as longitude_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN latitude ELSE NULL END) as latitude_pulang,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN longitude ELSE NULL END) as longitude_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN detail_lokasi ELSE NULL END) as lokasi_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN detail_lokasi ELSE NULL END) as lokasi_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN foto ELSE NULL END) as foto_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN foto ELSE NULL END) as foto_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN status ELSE NULL END) as status_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN status ELSE NULL END) as status_pulang,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN keterangan ELSE NULL END) as keterangan_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN keterangan ELSE NULL END) as keterangan_pulang
        ')
        ->groupBy('karyawan_id', 'nik', \DB::raw('DATE(waktu)'));

        // Filter by Type (Masuk/Pulang) on aggregate having clause
        if ($request->filled('tipe')) {
            $tipe = strtolower($request->tipe);
            if ($tipe === 'masuk') {
                $query->havingRaw('waktu_masuk IS NOT NULL');
            } elseif ($tipe === 'pulang') {
                $query->havingRaw('waktu_pulang IS NOT NULL');
            }
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(25)->withQueryString();
        $pekerjaans = Karyawan::whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $mesins = Mesin::all()->keyBy('id');

        return view('absensi.index', compact('absensis', 'pekerjaans', 'divisis', 'startDate', 'endDate', 'mesins'));
    }

    /**
     * Display rekapitulasi of attendance logs.
     */
    public function rekap(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get all employees
        $karyawansQuery = Karyawan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $karyawansQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nama_panggilan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pekerjaan')) {
            $karyawansQuery->where('pekerjaan', $request->pekerjaan);
        }

        if ($request->filled('divisi')) {
            $karyawansQuery->where('divisi', $request->divisi);
        }

        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->paginate(15)->withQueryString();
        $pekerjaans = Karyawan::whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');

        // Calculate normal workdays in the selected month (excluding weekends)
        $normalWorkdays = 0;
        $daysInMonth = $startDate->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            if (!$date->isWeekend()) {
                $normalWorkdays++;
            }
        }

        // Fetch all attendance records for this month to group in PHP (avoiding N+1 queries)
        $attendance = Absensi::whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get()
            ->groupBy('karyawan_id');

        // Calculate rekap statistics for each employee
        $rekapData = [];
        foreach ($karyawans as $karyawan) {
            $logs = $attendance->get($karyawan->id, collect());

            // Group logs by Date to count unique active days
            $activeDays = $logs->groupBy(function ($log) {
                return Carbon::parse($log->waktu)->toDateString();
            })->count();

            // Calculate absent days (Tidak Hadir)
            $tidakHadir = max(0, $normalWorkdays - $activeDays);

            $rekapData[$karyawan->id] = [
                'total_masuk' => $activeDays, // Hadir = Unique active days
                'total_pulang' => $tidakHadir, // Tidak Hadir = Absent days
                'active_days' => $activeDays,
            ];
        }

        return view('absensi.rekap', compact('karyawans', 'rekapData', 'pekerjaans', 'divisis', 'month', 'year'));
    }

    /**
     * Export rekapitulasi of attendance logs to Excel.
     */
    public function exportRekap(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $search = $request->input('search');
        $pekerjaan = $request->input('pekerjaan');
        $divisi = $request->input('divisi');

        $fileName = 'rekap-absensi-' . $month . '-' . $year . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiRekapExport($month, $year, $search, $pekerjaan, $divisi),
            $fileName
        );
    }
}

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
            $startDateObj->copy()->setTime(6, 0, 0),
            $endDateObj->copy()->addDays(1)->setTime(5, 59, 59),
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
            DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal,
            MIN(CASE WHEN LOWER(tipe) = "masuk" THEN waktu ELSE NULL END) as waktu_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN waktu ELSE NULL END) as waktu_pulang,
            MAX(CASE WHEN LOWER(tipe) = "istirahat_keluar" THEN waktu ELSE NULL END) as waktu_istirahat_keluar,
            MAX(CASE WHEN LOWER(tipe) = "istirahat_masuk" THEN waktu ELSE NULL END) as waktu_istirahat_masuk,
            MIN(CASE WHEN LOWER(tipe) = "lembur_masuk" THEN waktu ELSE NULL END) as waktu_lembur_masuk,
            MAX(CASE WHEN LOWER(tipe) = "lembur_pulang" THEN waktu ELSE NULL END) as waktu_lembur_pulang,
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
        ->groupBy('karyawan_id', 'nik', \DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'));

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
     * Delete an attendance log by NIK, Date, and Type.
     */
    public function deleteLog(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:Masuk,Pulang'
        ]);

        Absensi::where('nik', $request->nik)
               ->whereDate('waktu', $request->tanggal)
               ->where('tipe', $request->tipe)
               ->delete();

        return back()->with('success', "Data jam {$request->tipe} berhasil dihapus dari sistem.");
    }

    /**
     * Display rekapitulasi of attendance logs.
     */
    public function rekap(Request $request)
    {
        if ($request->has('export')) {
            return $this->exportRekap($request);
        }

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get all active employees (exclude those who have resigned)
        $karyawansQuery = Karyawan::whereNull('tanggal_berhenti');

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
        $pekerjaans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $cabangs = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('cabang')->where('cabang', '!=', '')->distinct()->pluck('cabang');
        $penempatans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('penempatan')->where('penempatan', '!=', '')->distinct()->pluck('penempatan');

        // Calculate normal workdays in the selected month (excluding weekends)
        $normalWorkdays = 0;
        $daysInMonth = $startDate->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            if (!$date->isSunday()) {
                $normalWorkdays++;
            }
        }

        // Fetch all attendance records for this month to group in PHP (avoiding N+1 queries)
        $attendance = Absensi::whereBetween('waktu', [
                $startDate->copy()->setTime(6, 0, 0),
                $endDate->copy()->addDays(1)->setTime(5, 59, 59)
            ])
            ->get()
            ->groupBy('karyawan_id');

        // Fetch all approved permissions/leaves in the selected month
        $permissions = \Illuminate\Support\Facades\DB::table('permohonan_izins')
            ->where('status', 'APPROVED')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('tanggal_selesai', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('tanggal_mulai', '<=', $startDate->toDateString())
                          ->where('tanggal_selesai', '>=', $endDate->toDateString());
                  });
            })
            ->get()
            ->groupBy('karyawan_id');

        // Calculate rekap statistics for each employee
        $rekapData = [];
        foreach ($karyawans as $karyawan) {
            $logs = $attendance->get($karyawan->id, collect());
            $karyawanPermissions = $permissions->get($karyawan->id, collect());

            // Group logs by Date to get unique check-in dates
            $presentDates = $logs->groupBy(function ($log) {
                return Carbon::parse($log->waktu)->subHours(6)->toDateString();
            })->keys()->toArray();

            $hadir = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::createFromDate($year, $month, $d);
                if ($date->isSunday()) {
                    continue; // Skip Sundays
                }

                $dateStr = $date->toDateString();

                if (in_array($dateStr, $presentDates)) {
                    $hadir++;
                } else {
                    // Check if they had an approved permission on this day
                    $matchedPerm = $karyawanPermissions->first(function($perm) use ($dateStr) {
                        return $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                    });

                    if ($matchedPerm) {
                        $jenis = strtolower($matchedPerm->jenis_izin);
                        if ($jenis === 'sakit') {
                            $sakit++;
                        } else {
                            $izin++;
                        }
                    } else {
                        $alpha++;
                    }
                }
            }

            $rekapData[$karyawan->id] = [
                'total_masuk' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
            ];
        }

        return view('absensi.rekap', compact('karyawans', 'rekapData', 'pekerjaans', 'divisis', 'cabangs', 'penempatans', 'month', 'year'));
    }

    /**
     * Export rekapitulasi of attendance logs to Excel.
     */
    public function exportRekap(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->input('search');
        $pekerjaan = $request->input('pekerjaan');
        $divisi = $request->input('divisi');
        $cabang = $request->input('cabang');
        $tempat = $request->input('tempat');

        $fileName = 'rekap-absensi-' . $startDate . '-sd-' . $endDate . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiRekapExport($startDate, $endDate, $search, $pekerjaan, $divisi, $cabang, $tempat),
            $fileName
        );
    }
    public function exportDat(Request $request)
    {
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd = Carbon::now()->endOfMonth()->toDateString();

        $startDateObj = $this->parseDateSafe($request->input('start_date'), $defaultStart);
        $endDateObj = $this->parseDateSafe($request->input('end_date'), $defaultEnd);

        $query = Absensi::with(['karyawan']);

        $query->whereBetween('waktu', [
            $startDateObj->copy()->setTime(6, 0, 0),
            $endDateObj->copy()->addDays(1)->setTime(5, 59, 59),
        ]);

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

        if ($request->filled('pekerjaan')) {
            $pekerjaan = $request->pekerjaan;
            $query->whereHas('karyawan', function ($kQ) use ($pekerjaan) {
                $kQ->where('pekerjaan', $pekerjaan);
            });
        }

        if ($request->filled('divisi')) {
            $divisi = $request->divisi;
            $query->whereHas('karyawan', function ($kQ) use ($divisi) {
                $kQ->where('divisi', $divisi);
            });
        }

        if ($request->filled('mesin_id')) {
            $query->whereIn('mesin_id', (array) $request->mesin_id);
        }

        $absensis = $query->orderBy('waktu', 'asc')->get();

        $content = "";
        foreach ($absensis as $absensi) {
            $pin = $absensi->nik ?: ($absensi->karyawan_id ?: '0');
            $pin = ltrim($pin, '0') ?: '0';
            $paddedPin = str_pad($pin, 9, ' ', STR_PAD_LEFT);
            $waktu = Carbon::parse($absensi->waktu)->format('Y-m-d H:i:s');
            
            $status = '255';
            $tipeLower = strtolower($absensi->tipe);
            if (str_contains($tipeLower, 'masuk')) $status = '0';
            elseif (str_contains($tipeLower, 'pulang') || str_contains($tipeLower, 'keluar')) $status = '1';

            $content .= "{$paddedPin}\t{$waktu}\t1\t{$status}\t15\t0\r\n";
        }

        $fileName = '1_attlog.dat';
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}

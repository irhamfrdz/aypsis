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
     * Download User Data from fingerprint machine (MDB)
     */
    public function exportMachineUsers()
    {
        $mdbPath = env('MDB_PATH', 'C:\\Program Files (x86)\\Solution\\att2000.mdb');
        
        try {
            if (!function_exists('odbc_connect')) {
                throw new \Exception("Ekstensi ODBC tidak aktif di PHP web server.");
            }

            $conn = odbc_connect("Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$mdbPath;Uid=;Pwd=;", "", "");
            
            if (!$conn) {
                throw new \Exception(odbc_errormsg());
            }

            $query = "SELECT u.Badgenumber, u.Name, u.USERID FROM USERINFO u ORDER BY u.USERID ASC";
            $result = odbc_exec($conn, $query);
            
            $mdbUsers = [];
            while ($row = odbc_fetch_array($result)) {
                $mdbUsers[] = $row;
            }
            odbc_close($conn);

            $filename = 'data_user_mesin_finger_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($mdbUsers) {
                $file = fopen('php://output', 'w');
                // CSV Header
                fputcsv($file, ['No', 'USERID', 'Badgenumber (NIK)', 'Name']);

                $no = 1;
                foreach ($mdbUsers as $user) {
                    fputcsv($file, [
                        $no++,
                        $user['USERID'],
                        $user['Badgenumber'],
                        $user['Name']
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca database mesin finger: ' . $e->getMessage());
        }
    }
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

        // Filter by Status Absen (Tidak Masuk/Tidak Pulang/Lengkap/Lembur) on aggregate having clause
        if ($request->filled('status_absen')) {
            $status_absen = $request->status_absen;
            if ($status_absen === 'tidak_masuk') {
                $query->havingRaw('waktu_masuk IS NULL');
            } elseif ($status_absen === 'tidak_pulang') {
                $query->havingRaw('waktu_pulang IS NULL AND waktu_masuk IS NOT NULL'); // Jika tidak masuk, tidak dihitung tidak pulang
            } elseif ($status_absen === 'lengkap') {
                $query->havingRaw('waktu_masuk IS NOT NULL AND waktu_pulang IS NOT NULL');
            } elseif ($status_absen === 'ada_lembur') {
                $query->havingRaw('waktu_lembur_masuk IS NOT NULL OR waktu_lembur_pulang IS NOT NULL');
            }
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(25)->withQueryString();
        $pekerjaans = Karyawan::whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $mesins = Mesin::all()->keyBy('id');
        $karyawanList = Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);

        return view('absensi.index', compact('absensis', 'pekerjaans', 'divisis', 'startDate', 'endDate', 'mesins', 'karyawanList'));
    }

    /**
     * Store manual attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|date',
        ]);

        $nik = $request->nik;
        $tanggal = Carbon::parse($request->tanggal)->toDateString();
        
        $karyawan = Karyawan::where('nik', $nik)->first();
        $karyawan_id = $karyawan ? $karyawan->id : null;

        $times = [
            'Masuk' => $request->waktu_masuk,
            'Istirahat_Keluar' => $request->waktu_istirahat_keluar,
            'Istirahat_Masuk' => $request->waktu_istirahat_masuk,
            'Pulang' => $request->waktu_pulang,
            'Lembur_Masuk' => $request->waktu_lembur_masuk,
            'Lembur_Pulang' => $request->waktu_lembur_pulang,
        ];

        foreach ($times as $tipe => $time) {
            $startDateObj = Carbon::parse($tanggal)->setTime(6, 0, 0);
            $endDateObj = Carbon::parse($tanggal)->addDays(1)->setTime(5, 59, 59);

            $existingLog = Absensi::where('nik', $nik)
                ->where('tipe', $tipe)
                ->whereBetween('waktu', [$startDateObj, $endDateObj])
                ->first();

            if (!empty($time)) {
                $waktu = Carbon::parse($tanggal . ' ' . $time);
                if (in_array($tipe, ['Pulang', 'Lembur_Pulang']) && $time < '06:00') {
                    $waktu->addDay(); 
                }

                if ($existingLog) {
                    $existingLog->update([
                        'waktu' => $waktu,
                        'status' => 'Manual',
                        'keterangan' => 'Diedit secara manual (via Tambah)'
                    ]);
                } else {
                    Absensi::create([
                        'karyawan_id' => $karyawan_id,
                        'nik' => $nik,
                        'waktu' => $waktu,
                        'tipe' => $tipe,
                        'status' => 'Manual',
                        'keterangan' => $request->keterangan ?? 'Ditambahkan secara manual',
                    ]);
                }
            } else {
                if ($existingLog) {
                    $existingLog->delete();
                }
            }
        }

        return back()->with('success', 'Data absensi manual berhasil disimpan.');
    }

    /**
     * Update an attendance day record.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|date',
        ]);

        $nik = $request->nik;
        $tanggal = Carbon::parse($request->tanggal)->toDateString();
        
        // Find existing employee
        $karyawan = Karyawan::where('nik', $nik)->first();
        $karyawan_id = $karyawan ? $karyawan->id : null;

        $times = [
            'Masuk' => $request->waktu_masuk,
            'Istirahat_Keluar' => $request->waktu_istirahat_keluar,
            'Istirahat_Masuk' => $request->waktu_istirahat_masuk,
            'Pulang' => $request->waktu_pulang,
            'Lembur_Masuk' => $request->waktu_lembur_masuk,
            'Lembur_Pulang' => $request->waktu_lembur_pulang,
        ];

        // Process each type
        foreach ($times as $tipe => $time) {
            // Find existing log for this date and type
            // Note: The index groups by DATE(waktu - 6 hours), so we search in that range
            $startDateObj = Carbon::parse($tanggal)->setTime(6, 0, 0);
            $endDateObj = Carbon::parse($tanggal)->addDays(1)->setTime(5, 59, 59);

            $existingLog = Absensi::where('nik', $nik)
                ->where('tipe', $tipe)
                ->whereBetween('waktu', [$startDateObj, $endDateObj])
                ->first();

            if (!empty($time)) {
                $waktu = Carbon::parse($tanggal . ' ' . $time);
                if (in_array($tipe, ['Pulang', 'Lembur_Pulang']) && $time < '06:00') {
                    $waktu->addDay(); 
                }

                if ($existingLog) {
                    $existingLog->update([
                        'waktu' => $waktu,
                        'status' => 'Manual',
                        'keterangan' => 'Diedit secara manual'
                    ]);
                } else {
                    Absensi::create([
                        'karyawan_id' => $karyawan_id,
                        'nik' => $nik,
                        'waktu' => $waktu,
                        'tipe' => $tipe,
                        'status' => 'Manual',
                        'keterangan' => 'Ditambahkan dari edit manual',
                    ]);
                }
            } else {
                // If time is empty, delete if exists
                if ($existingLog) {
                    $existingLog->delete();
                }
            }
        }

        return back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Delete all attendance logs for a specific employee on a specific date.
     */
    public function destroyDay(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|date',
        ]);

        $nik = $request->nik;
        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        $startDateObj = Carbon::parse($tanggal)->setTime(6, 0, 0);
        $endDateObj = Carbon::parse($tanggal)->addDays(1)->setTime(5, 59, 59);

        Absensi::where('nik', $nik)
            ->whereBetween('waktu', [$startDateObj, $endDateObj])
            ->delete();

        return back()->with('success', 'Semua data absensi pada tanggal tersebut berhasil dihapus.');
    }

    /**
     * Get attendance data for a specific employee and date (for AJAX).
     */
    public function getData(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|date',
        ]);

        $nik = $request->nik;
        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        $startDateObj = Carbon::parse($tanggal)->setTime(6, 0, 0);
        $endDateObj = Carbon::parse($tanggal)->addDays(1)->setTime(5, 59, 59);

        $absensis = Absensi::where('nik', $nik)
            ->whereBetween('waktu', [$startDateObj, $endDateObj])
            ->get();

        $data = [
            'waktu_masuk' => '',
            'waktu_pulang' => '',
            'waktu_istirahat_keluar' => '',
            'waktu_istirahat_masuk' => '',
            'waktu_lembur_masuk' => '',
            'waktu_lembur_pulang' => '',
        ];

        foreach ($absensis as $absensi) {
            $time = Carbon::parse($absensi->waktu)->format('H:i');
            switch ($absensi->tipe) {
                case 'Masuk':
                    $data['waktu_masuk'] = $time;
                    break;
                case 'Pulang':
                    $data['waktu_pulang'] = $time;
                    break;
                case 'Istirahat_Keluar':
                    $data['waktu_istirahat_keluar'] = $time;
                    break;
                case 'Istirahat_Masuk':
                    $data['waktu_istirahat_masuk'] = $time;
                    break;
                case 'Lembur_Masuk':
                    $data['waktu_lembur_masuk'] = $time;
                    break;
                case 'Lembur_Pulang':
                    $data['waktu_lembur_pulang'] = $time;
                    break;
            }
        }

        return response()->json($data);
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

        // Handle Date Range
        $defaultEndDate = Carbon::now()->day >= 26 ? Carbon::now()->addMonth()->day(25) : Carbon::now()->day(25);
        $defaultStartDate = $defaultEndDate->copy()->subMonth()->day(26);

        $startDateStr = $request->input('start_date', $defaultStartDate->toDateString());
        $endDateStr = $request->input('end_date', $defaultEndDate->toDateString());

        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();

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

        if ($request->filled('kehadiran')) {
            $kehadiran = $request->kehadiran;
            $startObj = $startDate->copy()->setTime(6, 0, 0);
            $endObj = $endDate->copy()->addDays(1)->setTime(5, 59, 59);

            if ($kehadiran === '0_hari') {
                $karyawansQuery->whereDoesntHave('absensi', function ($q) use ($startObj, $endObj) {
                    $q->whereBetween('waktu', [$startObj, $endObj]);
                });
            } elseif ($kehadiran === 'ada_absen') {
                $karyawansQuery->whereHas('absensi', function ($q) use ($startObj, $endObj) {
                    $q->whereBetween('waktu', [$startObj, $endObj]);
                });
            }
        }

        if ($request->filled('tunjangan')) {
            $tunjangan = $request->tunjangan;
            $karyawansQuery->whereJsonContains('tunjangan', $tunjangan);
        }

        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->paginate(15)->withQueryString();
        $pekerjaans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $cabangs = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('cabang')->where('cabang', '!=', '')->distinct()->pluck('cabang');
        $penempatans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('penempatan')->where('penempatan', '!=', '')->distinct()->pluck('penempatan');

        $tunjangansList = [];
        $karyawansTunjangans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('tunjangan')->pluck('tunjangan');
        foreach ($karyawansTunjangans as $tunjanganArray) {
            if (is_array($tunjanganArray)) {
                foreach ($tunjanganArray as $t) {
                    if (!in_array($t, $tunjangansList)) {
                        $tunjangansList[] = $t;
                    }
                }
            }
        }
        sort($tunjangansList);

        // Calculate normal workdays in the selected range (excluding weekends)
        $normalWorkdays = 0;
        $tempDate = $startDate->copy();
        while ($tempDate->lte($endDate)) {
            if (!$tempDate->isSunday()) {
                $normalWorkdays++;
            }
            $tempDate->addDay();
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
            $logsByDate = $logs->groupBy(function ($log) {
                return Carbon::parse($log->waktu)->subHours(6)->toDateString();
            });
            $presentDates = $logsByDate->keys()->toArray();

            $hadir = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;
            $terlambatKali = 0;
            $terlambatMenit = 0;
            $pulangCepatKali = 0;
            $pulangCepatMenit = 0;
            $lemburJam = 0;
            $lemburKali = 0;

            $tempDate = $startDate->copy();
            while ($tempDate->lte($endDate)) {
                if ($tempDate->isSunday()) {
                    $tempDate->addDay();
                    continue; // Skip Sundays
                }

                $dateStr = $tempDate->toDateString();

                if (in_array($dateStr, $presentDates)) {
                    $hadir++;
                    $dayLogs = $logsByDate->get($dateStr);
                    
                    // Lateness
                    $masukLog = $dayLogs->where('tipe', 'Masuk')->first();
                    if ($masukLog) {
                        $waktuMasuk = Carbon::parse($masukLog->waktu);
                        $jamMasukNormal = Carbon::parse($dateStr . ' 09:00:00');
                        if ($waktuMasuk->gt($jamMasukNormal->copy()->addMinutes(5))) {
                            // Check for approved datang_terlambat permission
                            $hasLatePermission = $karyawanPermissions->contains(function($perm) use ($dateStr) {
                                return strtolower($perm->jenis_izin) === 'datang_terlambat' && 
                                       $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                            });
                            
                            if (!$hasLatePermission) {
                                $terlambatKali++;
                                $terlambatMenit += $jamMasukNormal->diffInMinutes($waktuMasuk);
                            }
                        }
                    }

                    // Early leave
                    $pulangLog = $dayLogs->where('tipe', 'Pulang')->first();
                    if ($pulangLog) {
                        $waktuPulang = Carbon::parse($pulangLog->waktu);
                        $jamPulangNormal = Carbon::parse($dateStr . ' 17:00:00');
                        // if clock out is next day (e.g. 02:00 AM), it's not early leave. 
                        // But if it's same day before 17:00
                        if ($waktuPulang->lt($jamPulangNormal) && $waktuPulang->format('Y-m-d') == $dateStr) {
                            // Check for approved pulang_cepat permission
                            $hasEarlyPermission = $karyawanPermissions->contains(function($perm) use ($dateStr) {
                                return strtolower($perm->jenis_izin) === 'pulang_cepat' && 
                                       $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                            });
                            
                            if (!$hasEarlyPermission) {
                                $pulangCepatKali++;
                                $pulangCepatMenit += $waktuPulang->diffInMinutes($jamPulangNormal);
                            }
                        }
                    }

                    // Overtime (Lembur)
                    $lemburMasuk = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_masuk'; });
                    $lemburPulang = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_pulang'; });
                    if ($lemburMasuk || $lemburPulang) {
                        $lemburKali++;
                        if ($lemburMasuk && $lemburPulang) {
                            $lm = Carbon::parse($lemburMasuk->waktu);
                            $lp = Carbon::parse($lemburPulang->waktu);
                            $lemburJam += $lm->diffInMinutes($lp) / 60;
                        }
                    }

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
                
                $tempDate->addDay();
            }

            $rekapData[$karyawan->id] = [
                'total_masuk' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'terlambat_kali' => $terlambatKali,
                'terlambat_menit' => $terlambatMenit,
                'pulang_cepat_kali' => $pulangCepatKali,
                'pulang_cepat_menit' => $pulangCepatMenit,
                'lembur_jam' => round($lemburJam, 1),
                'lembur_kali' => $lemburKali,
            ];
        }

        return view('absensi.rekap', compact('karyawans', 'rekapData', 'pekerjaans', 'divisis', 'cabangs', 'penempatans', 'startDateStr', 'endDateStr', 'normalWorkdays', 'tunjangansList'));
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

        $tempatSlug = $tempat ? \Illuminate\Support\Str::slug($tempat) . '-' : '';
        $fileName = 'rekap-absensi-' . $tempatSlug . $startDate . '-sd-' . $endDate . '.xlsx';

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

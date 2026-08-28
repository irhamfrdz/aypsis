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

        // Filter by Date (Single Date)
        $defaultDate = Carbon::now()->toDateString();

        $startDateObj = $this->parseDateSafe($request->input('start_date'), $defaultDate);
        
        // If end_date is provided (via URL or other forms), use it. Otherwise, match start_date.
        $endDateObj = $request->filled('end_date') 
            ? $this->parseDateSafe($request->input('end_date'), $startDateObj->toDateString())
            : $startDateObj->copy();

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

        // Filter by Penempatan
        if ($request->filled('penempatan')) {
            $penempatan = $request->penempatan;
            $query->whereHas('karyawan', function ($kQ) use ($penempatan) {
                $kQ->where('penempatan', $penempatan);
            });
        }

        // Filter by Divisi
        if ($request->filled('divisi')) {
            $divisi = $request->divisi;
            $query->whereHas('karyawan', function ($kQ) use ($divisi) {
                $kQ->where('divisi', $divisi);
            });
        }

        // Filter by Cabang
        if ($request->filled('cabang')) {
            $cabang = $request->cabang;
            $query->whereHas('karyawan', function ($kQ) use ($cabang) {
                $kQ->where('cabang', $cabang);
            });
        }

        // Build the daily grouped query
        $query->selectRaw('
            karyawan_id,
            nik,
            DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN waktu ELSE NULL END) as waktu_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN waktu ELSE NULL END) as waktu_pulang,
            MAX(CASE WHEN LOWER(tipe) IN ("istirahat_keluar", "istirahat keluar") THEN waktu ELSE NULL END) as waktu_istirahat_keluar,
            MAX(CASE WHEN LOWER(tipe) IN ("istirahat_masuk", "istirahat masuk") THEN waktu ELSE NULL END) as waktu_istirahat_masuk,
            MIN(CASE WHEN LOWER(tipe) IN ("lembur_masuk", "lembur masuk", "mulai lembur") THEN waktu ELSE NULL END) as waktu_lembur_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("lembur_pulang", "lembur pulang", "selesai lembur") THEN waktu ELSE NULL END) as waktu_lembur_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN mesin_id ELSE NULL END) as mesin_id_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN mesin_id ELSE NULL END) as mesin_id_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN device ELSE NULL END) as device_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN device ELSE NULL END) as device_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN latitude ELSE NULL END) as latitude_masuk,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN longitude ELSE NULL END) as longitude_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN latitude ELSE NULL END) as latitude_pulang,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN longitude ELSE NULL END) as longitude_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN detail_lokasi ELSE NULL END) as lokasi_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN detail_lokasi ELSE NULL END) as lokasi_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN foto ELSE NULL END) as foto_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN foto ELSE NULL END) as foto_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN status ELSE NULL END) as status_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN status ELSE NULL END) as status_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN keterangan ELSE NULL END) as keterangan_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN keterangan ELSE NULL END) as keterangan_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN verify_mode ELSE NULL END) as verify_mode_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN verify_mode ELSE NULL END) as verify_mode_pulang
        ')
        ->groupBy('karyawan_id', 'nik', \DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'));

        // Filter by Status Absen (Tidak Masuk/Tidak Pulang/Lengkap/Lembur) on aggregate having clause
        if ($request->filled('status_absen')) {
            $status_absen = $request->status_absen;
            if ($status_absen === 'tidak_masuk') {
                $query->havingRaw('waktu_masuk IS NULL');
            } elseif ($status_absen === 'tidak_pulang') {
                $query->havingRaw('waktu_pulang IS NULL AND waktu_masuk IS NOT NULL'); // Jika tidak masuk, tidak dihitung tidak pulang
            } elseif ($status_absen === 'tidak_istirahat') {
                $query->havingRaw('(waktu_istirahat_keluar IS NULL OR waktu_istirahat_masuk IS NULL) AND waktu_masuk IS NOT NULL');
            } elseif ($status_absen === 'ada_istirahat') {
                $query->havingRaw('waktu_istirahat_keluar IS NOT NULL OR waktu_istirahat_masuk IS NOT NULL');
            } elseif ($status_absen === 'lengkap') {
                $query->havingRaw('waktu_masuk IS NOT NULL AND waktu_pulang IS NOT NULL');
            } elseif ($status_absen === 'ada_lembur') {
                $query->havingRaw('waktu_lembur_masuk IS NOT NULL OR waktu_lembur_pulang IS NOT NULL');
            } elseif ($status_absen === 'luar_radius') {
                $query->havingRaw('lokasi_masuk LIKE "%(Di luar radius%" OR lokasi_pulang LIKE "%(Di luar radius%"');
            }
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(25)->withQueryString();
        $pekerjaans = Karyawan::whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $penempatans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('penempatan')->where('penempatan', '!=', '')->distinct()->pluck('penempatan');
        $cabangs = Karyawan::whereNotNull('cabang')->where('cabang', '!=', '')->distinct()->pluck('cabang');
        $mesins = Mesin::all()->keyBy('id');
        $karyawanList = Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);
        $nonKaryawanList = \App\Models\KaryawanTidakTetap::orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);

        return view('absensi.index', compact('absensis', 'pekerjaans', 'divisis', 'penempatans', 'cabangs', 'startDate', 'endDate', 'mesins', 'karyawanList', 'nonKaryawanList'));
    }

    /**
     * Tampilkan detail absensi untuk seorang karyawan pada tanggal tertentu.
     */
    public function show($nik, $tanggal)
    {
        // Cari karyawan
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            $karyawan = \App\Models\KaryawanTidakTetap::where('nik', $nik)->first();
        }

        // Ambil semua log absensi karyawan tersebut pada tanggal yang dipilih
        // (Waktu di database dikurangi 6 jam untuk shift, persis seperti logic index)
        $absensis = Absensi::where('nik', $nik)
            ->whereDate(\DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'), $tanggal)
            ->orderBy('waktu', 'asc')
            ->get();
            
        $mesins = Mesin::all()->keyBy('id');

        return view('absensi.show', compact('karyawan', 'tanggal', 'absensis', 'mesins', 'nik'));
    }

    public function exportExcel(Request $request)
    {
        $query = Absensi::with(['karyawan']);

        // Filter by Date Range safely
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd = Carbon::now()->endOfMonth()->toDateString();

        $startDateObj = $this->parseDateSafe($request->input('start_date'), $defaultStart);
        $endDateObj = $this->parseDateSafe($request->input('end_date'), $defaultEnd);

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

        // Filter by Penempatan
        if ($request->filled('penempatan')) {
            $penempatan = $request->penempatan;
            $query->whereHas('karyawan', function ($kQ) use ($penempatan) {
                $kQ->where('penempatan', $penempatan);
            });
        }

        // Filter by Divisi
        if ($request->filled('divisi')) {
            $divisi = $request->divisi;
            $query->whereHas('karyawan', function ($kQ) use ($divisi) {
                $kQ->where('divisi', $divisi);
            });
        }

        // Filter by Cabang
        if ($request->filled('cabang')) {
            $cabang = $request->cabang;
            $query->whereHas('karyawan', function ($kQ) use ($cabang) {
                $kQ->where('cabang', $cabang);
            });
        }

        // Build the daily grouped query
        $query->selectRaw('
            karyawan_id,
            nik,
            DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN waktu ELSE NULL END) as waktu_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN waktu ELSE NULL END) as waktu_pulang,
            MAX(CASE WHEN LOWER(tipe) IN ("istirahat_keluar", "istirahat keluar") THEN waktu ELSE NULL END) as waktu_istirahat_keluar,
            MAX(CASE WHEN LOWER(tipe) IN ("istirahat_masuk", "istirahat masuk") THEN waktu ELSE NULL END) as waktu_istirahat_masuk,
            MIN(CASE WHEN LOWER(tipe) IN ("lembur_masuk", "lembur masuk", "mulai lembur") THEN waktu ELSE NULL END) as waktu_lembur_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("lembur_pulang", "lembur pulang", "selesai lembur") THEN waktu ELSE NULL END) as waktu_lembur_pulang,
            MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN status ELSE NULL END) as status_masuk,
            MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN status ELSE NULL END) as status_pulang
        ')
        ->groupBy('karyawan_id', 'nik', \DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'));

        // Filter by Status Absen
        if ($request->filled('status_absen')) {
            $status_absen = $request->status_absen;
            if ($status_absen === 'tidak_masuk') {
                $query->havingRaw('waktu_masuk IS NULL');
            } elseif ($status_absen === 'tidak_pulang') {
                $query->havingRaw('waktu_pulang IS NULL AND waktu_masuk IS NOT NULL');
            } elseif ($status_absen === 'tidak_istirahat') {
                $query->havingRaw('(waktu_istirahat_keluar IS NULL OR waktu_istirahat_masuk IS NULL) AND waktu_masuk IS NOT NULL');
            } elseif ($status_absen === 'ada_istirahat') {
                $query->havingRaw('waktu_istirahat_keluar IS NOT NULL OR waktu_istirahat_masuk IS NOT NULL');
            } elseif ($status_absen === 'lengkap') {
                $query->havingRaw('waktu_masuk IS NOT NULL AND waktu_pulang IS NOT NULL');
            } elseif ($status_absen === 'ada_lembur') {
                $query->havingRaw('waktu_lembur_masuk IS NOT NULL OR waktu_lembur_pulang IS NOT NULL');
            }
        }

        $absensis = $query->orderBy('tanggal', 'desc')->get();

        $filters = [
            'start_date' => $startDateObj->format('d-m-Y'),
            'end_date' => $endDateObj->format('d-m-Y'),
            'search' => $request->input('search'),
            'pekerjaan' => $request->input('pekerjaan'),
            'penempatan' => $request->input('penempatan'),
            'divisi' => $request->input('divisi'),
            'cabang' => $request->input('cabang'),
            'status_absen' => $request->input('status_absen'),
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AbsensiExport($absensis, $filters), 'Laporan_Absensi_Karyawan_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Store manual attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required|array',
            'tanggal.*' => 'required|date',
        ]);

        $nik = $request->nik;
        
        $karyawan = Karyawan::where('nik', $nik)->first();
        $karyawan_id = $karyawan ? $karyawan->id : null;

        foreach ($request->tanggal as $index => $tglInput) {
            $tanggal = Carbon::parse($tglInput)->toDateString();
            
            $times = [
                'Masuk' => $request->waktu_masuk[$index] ?? null,
                'Istirahat_Keluar' => $request->waktu_istirahat_keluar[$index] ?? null,
                'Istirahat_Masuk' => $request->waktu_istirahat_masuk[$index] ?? null,
                'Pulang' => $request->waktu_pulang[$index] ?? null,
                'Lembur_Masuk' => $request->waktu_lembur_masuk[$index] ?? null,
                'Lembur_Pulang' => $request->waktu_lembur_pulang[$index] ?? null,
            ];
            
            foreach ($times as $tipe => $time) {
                if (empty($time)) continue;

                $startDateObj = Carbon::parse($tanggal)->setTime(6, 0, 0);
                $endDateObj = Carbon::parse($tanggal)->addDays(1)->setTime(5, 59, 59);

                $existingLog = Absensi::where('nik', $nik)
                    ->where('tipe', $tipe)
                    ->whereBetween('waktu', [$startDateObj, $endDateObj])
                    ->first();

                // Jika sudah ada data, jangan diubah atau dihapus (dikunci)
                if ($existingLog) {
                    continue;
                }

                $waktu = Carbon::parse($tanggal . ' ' . $time);
                if (in_array($tipe, ['Pulang', 'Lembur_Pulang']) && $time < '06:00') {
                    $waktu->addDay(); 
                }

                Absensi::create([
                    'karyawan_id' => $karyawan_id,
                    'nik' => $nik,
                    'waktu' => $waktu,
                    'tipe' => $tipe,
                    'status' => 'Manual',
                    'keterangan' => $request->keterangan ?? 'Ditambahkan secara manual',
                ]);
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

            $tipeLower = strtolower($tipe);
            
            $existingLogQuery = Absensi::where('nik', $nik)
                ->where(function($q) use ($tipeLower) {
                    if ($tipeLower === 'masuk') $q->whereRaw('LOWER(tipe) = ?', ['masuk']);
                    elseif (in_array($tipeLower, ['pulang', 'keluar'])) $q->whereIn(\DB::raw('LOWER(tipe)'), ['pulang', 'keluar']);
                    elseif (in_array($tipeLower, ['istirahat_keluar', 'istirahat keluar'])) $q->whereIn(\DB::raw('LOWER(tipe)'), ['istirahat_keluar', 'istirahat keluar']);
                    elseif (in_array($tipeLower, ['istirahat_masuk', 'istirahat masuk'])) $q->whereIn(\DB::raw('LOWER(tipe)'), ['istirahat_masuk', 'istirahat masuk']);
                    elseif (in_array($tipeLower, ['lembur_masuk', 'lembur masuk', 'mulai lembur'])) $q->whereIn(\DB::raw('LOWER(tipe)'), ['lembur_masuk', 'lembur masuk', 'mulai lembur']);
                    elseif (in_array($tipeLower, ['lembur_pulang', 'lembur pulang', 'selesai lembur'])) $q->whereIn(\DB::raw('LOWER(tipe)'), ['lembur_pulang', 'lembur pulang', 'selesai lembur']);
                    else $q->whereRaw('LOWER(tipe) = ?', [$tipeLower]);
                })
                ->whereBetween('waktu', [$startDateObj, $endDateObj]);

            if (in_array($tipeLower, ['pulang', 'keluar', 'istirahat_masuk', 'istirahat masuk', 'lembur_pulang', 'lembur pulang', 'selesai lembur'])) {
                $existingLog = $existingLogQuery->orderBy('waktu', 'desc')->first();
            } else {
                $existingLog = $existingLogQuery->orderBy('waktu', 'asc')->first();
            }

            // Update data jika sudah ada, atau hapus jika kosong
            if ($existingLog) {
                if (!empty($time)) {
                    $waktu = Carbon::parse($tanggal . ' ' . $time);
                    if (in_array($tipe, ['Pulang', 'Lembur_Pulang']) && $time < '06:00') {
                        $waktu->addDay(); 
                    }
                    
                    try {
                        $existingLog->update([
                            'waktu' => $waktu,
                            'status' => 'Manual',
                            'keterangan' => 'Diperbarui dari edit manual',
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        if ($e->errorInfo[1] == 1062) {
                            $conflictLog = Absensi::where('nik', $nik)
                                ->where('tipe', $existingLog->tipe)
                                ->where('waktu', $waktu)
                                ->where('id', '!=', $existingLog->id)
                                ->first();
                                
                            if ($conflictLog) {
                                $conflictLog->update([
                                    'status' => 'Manual',
                                    'keterangan' => 'Diperbarui dari edit manual',
                                ]);
                                $existingLog->delete();
                            }
                        } else {
                            throw $e;
                        }
                    }
                } else {
                    // Jika di-kosongkan dari form, hapus data jam tersebut
                    $existingLog->delete();
                }
                continue;
            }

            // Jika belum ada dan form diisi
            if (!empty($time)) {
                $waktu = Carbon::parse($tanggal . ' ' . $time);
                if (in_array($tipe, ['Pulang', 'Lembur_Pulang']) && $time < '06:00') {
                    $waktu->addDay(); 
                }

                try {
                    Absensi::create([
                        'karyawan_id' => $karyawan_id,
                        'nik' => $nik,
                        'waktu' => $waktu,
                        'tipe' => $tipe,
                        'status' => 'Manual',
                        'keterangan' => 'Ditambahkan dari edit manual',
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        Absensi::where('nik', $nik)
                            ->where('tipe', $tipe)
                            ->where('waktu', $waktu)
                            ->update([
                                'status' => 'Manual',
                                'keterangan' => 'Diperbarui dari edit manual',
                            ]);
                    } else {
                        throw $e;
                    }
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
            $tipeLower = strtolower($absensi->tipe);
            
            if ($tipeLower === 'masuk') {
                $data['waktu_masuk'] = $time;
            } elseif (in_array($tipeLower, ['pulang', 'keluar'])) {
                $data['waktu_pulang'] = $time;
            } elseif (in_array($tipeLower, ['istirahat_keluar', 'istirahat keluar'])) {
                $data['waktu_istirahat_keluar'] = $time;
            } elseif (in_array($tipeLower, ['istirahat_masuk', 'istirahat masuk'])) {
                $data['waktu_istirahat_masuk'] = $time;
            } elseif (in_array($tipeLower, ['lembur_masuk', 'lembur masuk', 'mulai lembur'])) {
                $data['waktu_lembur_masuk'] = $time;
            } elseif (in_array($tipeLower, ['lembur_pulang', 'lembur pulang', 'selesai lembur'])) {
                $data['waktu_lembur_pulang'] = $time;
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
            if ($request->export === 'pdf') {
                return $this->exportRekapPdf($request);
            }
            return $this->exportRekap($request);
        }

        // Handle Date Range
        $defaultStartDate = Carbon::now()->startOfMonth();
        $defaultEndDate = Carbon::now()->endOfMonth();

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

        if ($request->filled('penempatan')) {
            $karyawansQuery->where('penempatan', $request->penempatan);
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

            if ($kehadiran === 'tidak_absen_masuk') {
                $driver = \DB::connection()->getDriverName();
                $dateExpr = $driver === 'sqlite' ? "date(datetime(waktu, '-6 hours'))" : "DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))";

                $karyawansQuery->whereHas('absensi', function ($q) use ($startObj, $endObj, $dateExpr) {
                    $q->select(\DB::raw($dateExpr))
                      ->whereBetween('waktu', [$startObj, $endObj])
                      ->groupBy(\DB::raw($dateExpr))
                      ->havingRaw('SUM(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN 1 ELSE 0 END) = 0');
                });
            } elseif ($kehadiran === 'tidak_absen_pulang') {
                $driver = \DB::connection()->getDriverName();
                $dateExpr = $driver === 'sqlite' ? "date(datetime(waktu, '-6 hours'))" : "DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))";

                $karyawansQuery->whereHas('absensi', function ($q) use ($startObj, $endObj, $dateExpr) {
                    $q->select(\DB::raw($dateExpr))
                      ->whereBetween('waktu', [$startObj, $endObj])
                      ->groupBy(\DB::raw($dateExpr))
                      ->havingRaw('SUM(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN 1 ELSE 0 END) = 0');
                });
            } elseif ($kehadiran === 'tidak_absen_istirahat') {
                $driver = \DB::connection()->getDriverName();
                $dateExpr = $driver === 'sqlite' ? "date(datetime(waktu, '-6 hours'))" : "DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))";

                $karyawansQuery->whereHas('absensi', function ($q) use ($startObj, $endObj, $dateExpr) {
                    $q->select(\DB::raw($dateExpr))
                      ->whereBetween('waktu', [$startObj, $endObj])
                      ->groupBy(\DB::raw($dateExpr))
                      ->havingRaw('SUM(CASE WHEN LOWER(tipe) LIKE "%istirahat%" THEN 1 ELSE 0 END) = 0');
                });
            }
        }

        if ($request->filled('grup')) {
            $grupReq = $request->grup;
            if ($request->filled('sub_grup')) {
                $subGrupReq = $request->sub_grup;
                $searchStr = $grupReq . ':' . $subGrupReq;
                $karyawansQuery->where('grup', 'LIKE', '%"' . $searchStr . '"%');
            } else {
                $karyawansQuery->where(function($q) use ($grupReq) {
                    $q->where('grup', 'LIKE', '%"' . $grupReq . ':%')
                      ->orWhere('grup', 'LIKE', '%"' . $grupReq . '"%');
                });
            }
        } elseif ($request->filled('sub_grup')) {
            $subGrupReq = $request->sub_grup;
            $karyawansQuery->where('grup', 'LIKE', '%:' . $subGrupReq . '"%');
        }

        if ($request->filled('grup_bpjs')) {
            $grupBpjsReq = $request->grup_bpjs;
            if ($request->filled('sub_grup_bpjs')) {
                $subGrupBpjsReq = $request->sub_grup_bpjs;
                $searchStr = $grupBpjsReq . ':' . $subGrupBpjsReq;
                $karyawansQuery->where('grup_bpjs', 'LIKE', '%"' . $searchStr . '"%');
            } else {
                $karyawansQuery->where(function($q) use ($grupBpjsReq) {
                    $q->where('grup_bpjs', 'LIKE', '%"' . $grupBpjsReq . ':%')
                      ->orWhere('grup_bpjs', 'LIKE', '%"' . $grupBpjsReq . '"%');
                });
            }
        } elseif ($request->filled('sub_grup_bpjs')) {
            $subGrupBpjsReq = $request->sub_grup_bpjs;
            $karyawansQuery->where('grup_bpjs', 'LIKE', '%:' . $subGrupBpjsReq . '"%');
        }

        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->paginate(15)->withQueryString();
        $allKaryawans = Karyawan::whereNull('tanggal_berhenti')->where('status', 'active')->orderBy('nama_lengkap')->get();
        
        $pekerjaans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('pekerjaan')->where('pekerjaan', '!=', '')->distinct()->pluck('pekerjaan');
        $divisis = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi');
        $cabangs = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('cabang')->where('cabang', '!=', '')->distinct()->pluck('cabang');
        $penempatans = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('penempatan')->where('penempatan', '!=', '')->distinct()->pluck('penempatan');

        $grupMap = [
            'GAJI' => ['TUNAI', 'TRANSFER', 'ABK', 'MAGANG', 'HARIAN'],
            'UANG MAKAN' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
            'TRANSPORTASI' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
            'LEMBUR' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
            'CUTI' => []
        ];
        $karyawansGrups = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('grup')->pluck('grup');
        foreach ($karyawansGrups as $grupArray) {
            if (is_string($grupArray)) {
                $grupArray = json_decode($grupArray, true);
            }
            if (is_array($grupArray)) {
                foreach ($grupArray as $g) {
                    $parts = explode(':', $g, 2);
                    $main = $parts[0];
                    $sub = $parts[1] ?? '';
                    if ($main !== '') {
                        if (!isset($grupMap[$main])) {
                            $grupMap[$main] = [];
                        }
                        if ($sub !== '' && !in_array($sub, $grupMap[$main])) {
                            $grupMap[$main][] = $sub;
                        }
                    }
                }
            }
        }
        ksort($grupMap);
        foreach ($grupMap as &$subs) {
            sort($subs);
        }
        $grupsList = array_keys($grupMap);

        $grupBpjsMap = [
            'BPJS-TK' => ['BPU HL JAKSEL', 'BPU SUPIR JKT PLUIT', 'BPU ALEXINDO PLUIT', 'BPU CILANDAK HL', 'PPU JKT', 'PPU BTM'],
            'BPJS-JKN' => ['BPU REIMBURSMENT']
        ];
        $karyawansGrupsBpjs = Karyawan::whereNull('tanggal_berhenti')->whereNotNull('grup_bpjs')->pluck('grup_bpjs');
        foreach ($karyawansGrupsBpjs as $grupBpjsArray) {
            if (is_string($grupBpjsArray)) {
                $grupBpjsArray = json_decode($grupBpjsArray, true);
            }
            if (is_array($grupBpjsArray)) {
                foreach ($grupBpjsArray as $g) {
                    $parts = explode(':', $g, 2);
                    $main = $parts[0];
                    $sub = $parts[1] ?? '';
                    if ($main !== '') {
                        if (!isset($grupBpjsMap[$main])) {
                            $grupBpjsMap[$main] = [];
                        }
                        if ($sub !== '' && !in_array($sub, $grupBpjsMap[$main])) {
                            $grupBpjsMap[$main][] = $sub;
                        }
                    }
                }
            }
        }
        ksort($grupBpjsMap);
        foreach ($grupBpjsMap as &$subs) {
            sort($subs);
        }
        $grupsBpjsList = array_keys($grupBpjsMap);

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
        $cutis = \Illuminate\Support\Facades\DB::table('cutis')
            ->where('status', 'APPROVED')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('tanggal_selesai', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('tanggal_mulai', '<=', $startDate->toDateString())
                          ->where('tanggal_selesai', '>=', $endDate->toDateString());
                  });
            })
            ->select('karyawan_id', 'tanggal_mulai', 'tanggal_selesai', \Illuminate\Support\Facades\DB::raw("CONCAT('Cuti ', jenis_cuti) as jenis_izin"), 'keterangan as alasan');

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
            ->select('karyawan_id', 'tanggal_mulai', 'tanggal_selesai', 'jenis_izin', 'alasan')
            ->union($cutis)
            ->get()
            ->groupBy('karyawan_id');

        $hariLiburs = \App\Models\HariLibur::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->pluck('tanggal')
            ->toArray();

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
            $detail_hadir = [];
            $detail_lembur = [];
            $detail_terlambat = [];
            $detail_pulang_cepat = [];
            $sakit = 0;
            $detail_sakit = [];
            $izin = 0;
            $detail_izin = [];
            $cuti = 0;
            $detail_cuti = [];
            $alpha = 0;
            $detail_alpha = [];
            $terlambatKali = 0;
            $terlambatMenit = 0;
            $pulangCepatKali = 0;
            $pulangCepatMenit = 0;
            $lemburJam = 0;
            $lemburKali = 0;
            
            $tidakAbsenMasukKali = 0;
            $tidakAbsenPulangKali = 0;
            $tidakAbsenIstirahatKali = 0;
            $detail_tidak_absen_masuk = [];
            $detail_tidak_absen_pulang = [];
            $detail_tidak_absen_istirahat = [];

            $tempDate = $startDate->copy();
            while ($tempDate->lte($endDate)) {
                $dateStr = $tempDate->toDateString();

                // Check if they had an approved permission on this day FIRST
                $matchedPerm = $karyawanPermissions->first(function($perm) use ($dateStr) {
                    return $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                });

                $isFullDayPerm = false;
                if ($matchedPerm) {
                    $jenis = strtolower($matchedPerm->jenis_izin);
                    if (!str_contains($jenis, 'datang_terlambat') && !str_contains($jenis, 'pulang_cepat') && !str_contains($jenis, 'dinas_luar')) {
                        $isFullDayPerm = true;
                    }
                }

                if ($isFullDayPerm) {
                    $jenis = strtolower($matchedPerm->jenis_izin);
                    $alasan = $matchedPerm->alasan ?: '-';
                    $tanggal = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
                    
                    if (str_contains($jenis, 'sakit')) {
                        $sakit++;
                        $detail_sakit[] = ['tanggal' => $tanggal, 'jenis' => $matchedPerm->jenis_izin, 'alasan' => $alasan];
                    } elseif (str_contains($jenis, 'cuti')) {
                        $cuti++;
                        $detail_cuti[] = ['tanggal' => $tanggal, 'jenis' => $matchedPerm->jenis_izin, 'alasan' => $alasan];
                    } else {
                        $izin++;
                        $detail_izin[] = ['tanggal' => $tanggal, 'jenis' => $matchedPerm->jenis_izin, 'alasan' => $alasan];
                    }
                } elseif (in_array($dateStr, $presentDates)) {
                    $hadir++;
                    $detail_hadir[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
                    $dayLogs = $logsByDate->get($dateStr);
                    
                    // Lateness
                    $masukLog = $dayLogs->where('tipe', 'Masuk')->first();
                    if ($masukLog) {
                        $waktuMasuk = Carbon::parse($masukLog->waktu);
                        $jamMasukNormal = Carbon::parse($dateStr . ' 09:00:00');
                        if ($waktuMasuk->gt($jamMasukNormal->copy()->addMinutes(5))) {
                            // Cek apakah penempatan kebal terlambat
                            $isExempt = in_array(strtolower(trim($karyawan->penempatan)), ['pelabuhan', 'garasi', 'pelabuhan 1', '1']);

                            // Check for approved datang_terlambat permission
                            $hasLatePermission = $karyawanPermissions->contains(function($perm) use ($dateStr) {
                                return strtolower($perm->jenis_izin) === 'datang_terlambat' && 
                                       $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                            });
                            
                            if (!$hasLatePermission && !$isExempt) {
                                $terlambatKali++;
                                $terlambatMenit += $jamMasukNormal->diffInMinutes($waktuMasuk);
                                $detail_terlambat[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y') . ' (' . $jamMasukNormal->diffInMinutes($waktuMasuk) . ' mnt)';
                            }
                        }
                    } else {
                        $tidakAbsenMasukKali++;
                        $detail_tidak_absen_masuk[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
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
                                $detail_pulang_cepat[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y') . ' (' . $waktuPulang->diffInMinutes($jamPulangNormal) . ' mnt)';
                            }
                        }
                    } else {
                        $tidakAbsenPulangKali++;
                        $detail_tidak_absen_pulang[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
                    }

                    // Istirahat
                    $istirahatLog = $dayLogs->first(function($val) { return strpos(strtolower($val->tipe), 'istirahat') !== false; });
                    if (!$istirahatLog) {
                        $tidakAbsenIstirahatKali++;
                        $detail_tidak_absen_istirahat[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
                    }

                    // Overtime (Lembur)
                    $lemburMasuk = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_masuk'; });
                    $lemburPulang = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_pulang'; });
                    if ($lemburMasuk || $lemburPulang) {
                        $lemburKali++;
                        $jam = 0;
                        if ($lemburMasuk && $lemburPulang) {
                            $lm = Carbon::parse($lemburMasuk->waktu);
                            $lp = Carbon::parse($lemburPulang->waktu);
                            $jam = $lm->diffInMinutes($lp) / 60;
                            $lemburJam += $jam;
                        }
                        $detail_lembur[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y') . ($jam > 0 ? " (" . round($jam, 1) . " Jam)" : "");
                    }

                } else {
                    // No permission, no scan -> Alpha
                    // Jangan hitung alpha jika tanggalnya belum terjadi (future dates), atau jika hari libur (Minggu & Nasional)
                    if ($dateStr <= \Carbon\Carbon::today()->toDateString() && !$tempDate->isSunday() && !in_array($dateStr, $hariLiburs)) {
                        $alpha++;
                        $detail_alpha[] = \Carbon\Carbon::parse($dateStr)->translatedFormat('d M Y');
                    }
                }
                
                
                $tempDate->addDay();
            }

            $rekapData[$karyawan->id] = [
                'total_masuk' => $hadir,
                'detail_hadir' => $detail_hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'cuti' => $cuti,
                'alpha' => $alpha,
                'detail_alpha' => $detail_alpha,
                'terlambat_kali' => $terlambatKali,
                'terlambat_menit' => $terlambatMenit,
                'pulang_cepat_kali' => $pulangCepatKali,
                'pulang_cepat_menit' => $pulangCepatMenit,
                'lembur_jam' => round($lemburJam, 1),
                'lembur_kali' => $lemburKali,
                'tidak_absen_masuk_kali' => $tidakAbsenMasukKali,
                'tidak_absen_pulang_kali' => $tidakAbsenPulangKali,
                'detail_tidak_absen_pulang' => $detail_tidak_absen_pulang,
                'tidak_absen_istirahat_kali' => $tidakAbsenIstirahatKali,
                'detail_tidak_absen_istirahat' => $detail_tidak_absen_istirahat,
                'detail_sakit' => $detail_sakit,
                'detail_izin' => $detail_izin,
                'detail_cuti' => $detail_cuti,
                'detail_hadir' => $detail_hadir,
                'detail_lembur' => $detail_lembur,
                'detail_terlambat' => $detail_terlambat,
                'detail_pulang_cepat' => $detail_pulang_cepat,
                'detail_tidak_absen_masuk' => $detail_tidak_absen_masuk,
            ];
        }

        $allHariLiburs = \App\Models\HariLibur::orderBy('tanggal', 'desc')->get();

        return view('absensi.rekap', compact('karyawans', 'allKaryawans', 'rekapData', 'pekerjaans', 'divisis', 'cabangs', 'penempatans', 'startDateStr', 'endDateStr', 'normalWorkdays', 'grupsList', 'grupMap', 'grupsBpjsList', 'grupBpjsMap', 'allHariLiburs'));
    }

    /**
     * Store manual izin from rekap page.
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jenis_izin' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);

        \Illuminate\Support\Facades\DB::table('permohonan_izins')->insert([
            'karyawan_id' => $karyawan->id,
            'nik' => $karyawan->nik,
            'nama' => $karyawan->nama_lengkap,
            'divisi' => $karyawan->divisi ?? '-',
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'waktu' => null,
            'alasan' => $request->alasan,
            'status' => 'APPROVED',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Izin karyawan berhasil ditambahkan.');
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
        $grup = $request->input('grup');
        $subGrup = $request->input('sub_grup');

        $tempatSlug = $tempat ? \Illuminate\Support\Str::slug($tempat) . '-' : '';
        $fileName = 'rekap-absensi-' . $tempatSlug . $startDate . '-sd-' . $endDate . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiRekapExport($startDate, $endDate, $search, $pekerjaan, $divisi, $cabang, $tempat, $grup, $subGrup),
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

    public function exportRekapPdf(Request $request)
    {
        $startDateStr = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDateStr = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();
        
        $karyawansQuery = Karyawan::whereNull('tanggal_berhenti')->orderBy('nik', 'asc');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $karyawansQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nama_panggilan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('penempatan')) {
            $karyawansQuery->where('penempatan', $request->penempatan);
        }
        if ($request->filled('pekerjaan')) {
            $karyawansQuery->where('pekerjaan', $request->pekerjaan);
        }
        if ($request->filled('divisi')) {
            $karyawansQuery->where('divisi', $request->divisi);
        }
        if ($request->filled('grup')) {
            $grupReq = $request->grup;
            if ($request->filled('sub_grup')) {
                $subGrupReq = $request->sub_grup;
                $searchStr = $grupReq . ':' . $subGrupReq;
                $karyawansQuery->where('grup', 'LIKE', '%"' . $searchStr . '"%');
            } else {
                $karyawansQuery->where(function ($q) use ($grupReq) {
                    $q->where('grup', 'LIKE', '%"' . $grupReq . ':%')
                      ->orWhere('grup', 'LIKE', '%"' . $grupReq . '"%');
                });
            }
        } elseif ($request->filled('sub_grup')) {
            $subGrupReq = $request->sub_grup;
            $karyawansQuery->where('grup', 'LIKE', '%:' . $subGrupReq . '"%');
        }

        if ($request->filled('selected_karyawan') && is_array($request->selected_karyawan)) {
            $karyawansQuery->whereIn('id', $request->selected_karyawan);
        }

        $karyawans = $karyawansQuery->get();

        $attendance = Absensi::whereBetween('waktu', [
                $startDate->copy()->setTime(6, 0, 0),
                $endDate->copy()->addDays(1)->setTime(5, 59, 59)
            ])
            ->orderBy('waktu', 'asc')
            ->get()
            ->groupBy('karyawan_id');
            
        $periodDates = [];
        $tempDate = $startDate->copy();
        while ($tempDate->lte($endDate)) {
            $periodDates[] = $tempDate->copy();
            $tempDate->addDay();
        }

        $pdfData = [];
        $hariIndo = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        foreach ($karyawans as $karyawan) {
            $logs = $attendance->get($karyawan->id, collect());
            $dayLogs = [];
            
            foreach ($periodDates as $date) {
                $dateStart = $date->copy()->setTime(6, 0, 0);
                $dateEnd = $date->copy()->addDays(1)->setTime(5, 59, 59);
                
                $todayLogs = $logs->filter(function($log) use ($dateStart, $dateEnd) {
                    return Carbon::parse($log->waktu)->between($dateStart, $dateEnd);
                });
                
                $masuk = null;
                $pulang = null;
                
                if ($todayLogs->count() > 0) {
                    $masuk = Carbon::parse($todayLogs->first()->waktu)->format('H.i');
                    if ($todayLogs->count() > 1) {
                        $pulang = Carbon::parse($todayLogs->last()->waktu)->format('H.i');
                    }
                }
                
                $dayStr = $date->format('m/d') . ' ' . $hariIndo[$date->dayOfWeek];
                $scanStr = $masuk ? ($pulang ? "$masuk-$pulang" : "$masuk-") : '-';
                
                $dayLogs[] = [
                    'date_label' => $dayStr,
                    'scan' => $scanStr
                ];
            }
            
            $pdfData[] = [
                'karyawan' => $karyawan,
                'logs' => $dayLogs
            ];
        }

        $titleParts = [];
        if ($request->filled('penempatan')) {
            $titleParts[] = strtoupper($request->penempatan);
        }
        if ($request->filled('pekerjaan')) {
            $titleParts[] = strtoupper($request->pekerjaan);
        }
        if ($request->filled('divisi')) {
            $titleParts[] = strtoupper($request->divisi);
        }
        if ($request->filled('grup')) {
            $grupText = strtoupper($request->grup);
            if ($request->filled('sub_grup')) {
                $grupText .= ' - ' . strtoupper($request->sub_grup);
            }
            $titleParts[] = $grupText;
        } elseif ($request->filled('sub_grup')) {
            $titleParts[] = strtoupper($request->sub_grup);
        }
        if ($request->filled('grup_bpjs')) {
            $grupBpjsText = strtoupper($request->grup_bpjs);
            if ($request->filled('sub_grup_bpjs')) {
                $grupBpjsText .= ' - ' . strtoupper($request->sub_grup_bpjs);
            }
            $titleParts[] = $grupBpjsText;
        } elseif ($request->filled('sub_grup_bpjs')) {
            $titleParts[] = strtoupper($request->sub_grup_bpjs);
        }

        $filterTitle = !empty($titleParts) ? implode(', ', $titleParts) : 'Semua Karyawan';
        $filename = 'Data_Scan_Karyawan' . (!empty($titleParts) ? '_' . \Illuminate\Support\Str::slug(implode('_', $titleParts)) : '') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('absensi.rekap-pdf', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pdfData' => $pdfData,
            'cabangTitle' => $filterTitle,
        ])->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }
    
    /**
     * Store new Hari Libur from rekap page.
     */
    public function storeHariLibur(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);

        \App\Models\HariLibur::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Destroy Hari Libur from rekap page.
     */
    public function destroyHariLibur($id)
    {
        $libur = \App\Models\HariLibur::findOrFail($id);
        $libur->delete();

        return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
    }
}

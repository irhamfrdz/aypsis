<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\UangLembur;
use App\Models\UangLemburRule;
use Carbon\Carbon;

class PerhitunganLemburController extends Controller
{
    protected function parseDateSafe($dateString, $default)
    {
        if (empty($dateString)) {
            return Carbon::parse($default);
        }
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return Carbon::parse($default);
        }
    }

    public function index(Request $request)
    {
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd = Carbon::now()->endOfMonth()->toDateString();

        $startDateStr = $request->input('start_date', $defaultStart);
        $endDateStr = $request->input('end_date', $defaultEnd);

        $startDate = $this->parseDateSafe($startDateStr, $defaultStart);
        $endDate = $this->parseDateSafe($endDateStr, $defaultEnd);

        // Get all active employees (exclude those who have resigned)
        $karyawanQuery = Karyawan::whereNull('tanggal_berhenti');

        if ($request->filled('search')) {
            $search = $request->search;
            $karyawanQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nama_panggilan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('penempatan')) {
            $karyawanQuery->where('penempatan', $request->penempatan);
        }

        if ($request->filled('divisi')) {
            $karyawanQuery->where('divisi', $request->divisi);
        }

        if ($request->filled('kehadiran')) {
            $kehadiran = $request->kehadiran;
            $startObj = $startDate->copy()->setTime(6, 0, 0);
            $endObj = $endDate->copy()->addDays(1)->setTime(5, 59, 59);

            if ($kehadiran === '0_hari') {
                $karyawanQuery->whereDoesntHave('absensi', function ($q) use ($startObj, $endObj) {
                    $q->whereBetween('waktu', [$startObj, $endObj]);
                });
            } elseif ($kehadiran === 'ada_absen') {
                $karyawanQuery->whereHas('absensi', function ($q) use ($startObj, $endObj) {
                    $q->whereBetween('waktu', [$startObj, $endObj]);
                });
            } elseif ($kehadiran === 'tidak_lengkap') {
                $driver = \DB::connection()->getDriverName();
                $dateExpr = $driver === 'sqlite' ? "date(datetime(waktu, '-6 hours'))" : "DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))";

                $karyawanQuery->whereHas('absensi', function ($q) use ($startObj, $endObj, $dateExpr) {
                    $q->select(\DB::raw($dateExpr))
                      ->whereBetween('waktu', [$startObj, $endObj])
                      ->whereIn('tipe', ['Masuk', 'Pulang'])
                      ->groupBy(\DB::raw($dateExpr))
                      ->havingRaw('COUNT(DISTINCT tipe) = 1');
                });
            }
        }

        if ($request->filled('grup')) {
            $grupReq = $request->grup;
            if ($request->filled('sub_grup')) {
                $subGrupReq = $request->sub_grup;
                $searchStr = $grupReq . ':' . $subGrupReq;
                $karyawanQuery->where('grup', 'LIKE', '%"' . $searchStr . '"%');
            } else {
                $karyawanQuery->where(function($q) use ($grupReq) {
                    $q->where('grup', 'LIKE', '%"' . $grupReq . ':%')
                      ->orWhere('grup', 'LIKE', '%"' . $grupReq . '"%');
                });
            }
        }

        if ($request->filled('grup_bpjs')) {
            $grupBpjsReq = $request->grup_bpjs;
            if ($request->filled('sub_grup_bpjs')) {
                $subGrupBpjsReq = $request->sub_grup_bpjs;
                $searchStr = $grupBpjsReq . ':' . $subGrupBpjsReq;
                $karyawanQuery->where('grup_bpjs', 'LIKE', '%"' . $searchStr . '"%');
            } else {
                $karyawanQuery->where(function($q) use ($grupBpjsReq) {
                    $q->where('grup_bpjs', 'LIKE', '%"' . $grupBpjsReq . ':%')
                      ->orWhere('grup_bpjs', 'LIKE', '%"' . $grupBpjsReq . '"%');
                });
            }
        }

        $karyawans = $karyawanQuery->orderBy('nama_lengkap')->get();

        // Fetch all attendance records for this month
        $attendance = Absensi::whereBetween('waktu', [
                $startDate->copy()->setTime(6, 0, 0),
                $endDate->copy()->addDays(1)->setTime(5, 59, 59)
            ])
            ->whereIn('tipe', ['Lembur_Masuk', 'Lembur_Pulang'])
            ->get()
            ->groupBy('karyawan_id');

        // Fetch all UangLembur and Rules
        $uangLemburs = UangLembur::with('rules')->get();

        $rekapData = [];

        foreach ($karyawans as $karyawan) {
            $logs = $attendance->get($karyawan->id) ?? collect();

            // Find matching rule for this Karyawan
            $matchingUangLemburs = [];
            if (is_array($karyawan->grup)) {
                foreach ($karyawan->grup as $grupStr) {
                    $parts = explode(':', $grupStr);
                    if (count($parts) >= 2 && trim(strtoupper($parts[0])) === 'LEMBUR') {
                        $group = trim(strtoupper($parts[0]));
                        $sub_group = trim(strtoupper($parts[1]));
                        
                        // Find in DB
                        $ul = $uangLemburs->first(function($item) use ($group, $sub_group) {
                            return strtoupper($item->group) === $group && strtoupper($item->sub_group) === $sub_group;
                        });

                        if ($ul) {
                            $matchingUangLemburs[] = $ul;
                        }
                    }
                }
            }

            // Group logs by Date
            $logsByDate = $logs->groupBy(function ($log) {
                return Carbon::parse($log->waktu)->subHours(6)->toDateString();
            });

            $totalJamHariBiasa = 0;
            $totalJamHariLibur = 0;
            $totalNominal = 0;
            $detailPerhitungan = [];

            $tempDate = $startDate->copy();
            while ($tempDate->lte($endDate)) {
                $dateStr = $tempDate->toDateString();
                $isHoliday = $tempDate->isSunday(); // For now, Sunday is holiday
                $tipeHari = $isHoliday ? 'Hari Libur' : 'Hari Biasa';

                $dayLogs = $logsByDate->get($dateStr);
                
                if ($dayLogs) {
                    $lemburMasuk = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_masuk'; });
                    $lemburPulang = $dayLogs->first(function($val) { return strtolower($val->tipe) === 'lembur_pulang'; });

                    if ($lemburMasuk && $lemburPulang) {
                        $lm = Carbon::parse($lemburMasuk->waktu);
                        $lp = Carbon::parse($lemburPulang->waktu);
                        
                        // Jika jam pulang terekam lebih awal dari jam masuk (melewati tengah malam)
                        // asumsikan jam pulang adalah di keesokan harinya
                        if ($lp < $lm) {
                            $lp->addDay();
                        }
                        
                        $durationMinutes = $lm->diffInMinutes($lp);
                        $durasiJam = ceil($durationMinutes / 60);

                        $jamPulangTime = $lp->format('H:i:s');
                        
                        // Calculate nominal based on matched rules
                        $nominalHariIni = 0;
                        $ruleApplied = null;

                        foreach ($matchingUangLemburs as $ul) {
                            // Find rule for this tipe_hari that matches jam_pulang
                            foreach ($ul->rules as $rule) {
                                if ($rule->tipe_hari === $tipeHari) {
                                    $matchesTime = false;
                                    
                                    if (in_array(strtolower($rule->satuan), ['hari', 'borongan'])) {
                                        $matchesTime = true;
                                    } else if ($tipeHari === 'Hari Libur') {
                                        // Evaluasi khusus Hari Libur berdasarkan durasi lembur (threshold 10 jam)
                                        if ($rule->is_sampai_selesai) {
                                            if ($durasiJam > 10) {
                                                $matchesTime = true;
                                            }
                                        } else {
                                            if ($durasiJam <= 10) {
                                                $matchesTime = true;
                                            }
                                        }
                                    } else {
                                        if ($rule->jam_mulai) {
                                            // Build Carbon boundaries for the rule
                                            $ruleMulai = \Carbon\Carbon::parse($lm->format('Y-m-d') . ' ' . $rule->jam_mulai);
                                            
                                            // If rule jam_mulai is far before lembur masuk, it implies it's for the next day (e.g. masuk 17:00, rule 00:00)
                                            if ($ruleMulai->copy()->addHours(6) < $lm) {
                                                $ruleMulai->addDay();
                                            }

                                            if ($rule->is_sampai_selesai) {
                                                if ($lp >= $ruleMulai) {
                                                    $matchesTime = true;
                                                }
                                            } else if ($rule->jam_selesai) {
                                                $ruleSelesai = \Carbon\Carbon::parse($ruleMulai->format('Y-m-d') . ' ' . $rule->jam_selesai);
                                                // If rule jam_selesai is less than rule jam_mulai time, it crosses midnight
                                                if ($ruleSelesai < $ruleMulai) {
                                                    $ruleSelesai->addDay();
                                                }
                                                
                                                if ($lp >= $ruleMulai && $lp <= $ruleSelesai) {
                                                    $matchesTime = true;
                                                }
                                            }
                                        } else {
                                            // No time condition, matches by default
                                            $matchesTime = true;
                                        }
                                    }

                                    if ($matchesTime) {
                                        if (strtolower($rule->satuan) === 'jam') {
                                            $nominalHariIni = $durasiJam * $rule->nominal;
                                        } else { // Hari / Borongan
                                            $nominalHariIni = $rule->nominal;
                                        }
                                        $ruleApplied = $rule;
                                        break 2; // Stop searching once a rule matches
                                    }
                                }
                            }
                        }

                        if ($isHoliday) {
                            $totalJamHariLibur += $durasiJam;
                        } else {
                            $totalJamHariBiasa += $durasiJam;
                        }
                        
                        $totalNominal += $nominalHariIni;

                        $detailPerhitungan[] = [
                            'tanggal' => $dateStr,
                            'tipe_hari' => $tipeHari,
                            'durasi_jam' => $durasiJam,
                            'jam_pulang' => $jamPulangTime,
                            'nominal' => $nominalHariIni,
                            'rule' => $ruleApplied ? $ruleApplied->satuan . ' x ' . number_format($ruleApplied->nominal, 0, ',', '.') : 'Tidak ada rumus',
                        ];
                    }
                }

                $tempDate->addDay();
            }

            if ($totalJamHariBiasa > 0 || $totalJamHariLibur > 0 || $totalNominal > 0) {
                $rekapData[$karyawan->id] = [
                    'karyawan' => $karyawan,
                    'total_jam_biasa' => $totalJamHariBiasa,
                    'total_jam_libur' => $totalJamHariLibur,
                    'total_nominal' => $totalNominal,
                    'detail' => $detailPerhitungan,
                ];
            }
        }

        // Master Dropdowns matching AbsensiController
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
        foreach ($grupBpjsMap as &$subsBpjs) {
            sort($subsBpjs);
        }
        $grupsBpjsList = array_keys($grupBpjsMap);

        return view('payroll.perhitungan-lembur.index', compact(
            'rekapData', 
            'startDateStr', 
            'endDateStr', 
            'divisis',
            'penempatans',
            'pekerjaans',
            'cabangs',
            'grupMap',
            'grupsList',
            'grupBpjsMap',
            'grupsBpjsList'
        ));
    }
}

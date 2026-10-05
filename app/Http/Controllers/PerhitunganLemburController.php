<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Karyawan;
use App\Models\UangLembur;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
                $dateExpr = $driver === 'sqlite' ? "date(datetime(waktu, '-6 hours'))" : 'DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))';

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
                $searchStr = $grupReq.':'.$subGrupReq;
                $karyawanQuery->where('grup', 'LIKE', '%"'.$searchStr.'"%');
            } else {
                $karyawanQuery->where(function ($q) use ($grupReq) {
                    $q->where('grup', 'LIKE', '%"'.$grupReq.':%')
                        ->orWhere('grup', 'LIKE', '%"'.$grupReq.'"%');
                });
            }
        }

        if ($request->filled('grup_bpjs')) {
            $grupBpjsReq = $request->grup_bpjs;
            if ($request->filled('sub_grup_bpjs')) {
                $subGrupBpjsReq = $request->sub_grup_bpjs;
                $searchStr = $grupBpjsReq.':'.$subGrupBpjsReq;
                $karyawanQuery->where('grup_bpjs', 'LIKE', '%"'.$searchStr.'"%');
            } else {
                $karyawanQuery->where(function ($q) use ($grupBpjsReq) {
                    $q->where('grup_bpjs', 'LIKE', '%"'.$grupBpjsReq.':%')
                        ->orWhere('grup_bpjs', 'LIKE', '%"'.$grupBpjsReq.'"%');
                });
            }
        }

        $karyawans = $karyawanQuery->orderBy('nama_lengkap')->get();
        $karyawanIds = $karyawans->pluck('id')->filter()->unique()->values();

        $rekapData = [];

        if ($karyawanIds->isNotEmpty()) {
            // Ambil nominal uang makan terbaru langsung dari tabel uang_makans per karyawan_id
            $uangMakanMap = \App\Models\UangMakan::whereIn('karyawan_id', $karyawanIds)
                ->where(function ($q) {
                    $q->where('tipe_karyawan', 'NOT LIKE', '%TidakTetap%')
                        ->orWhereNull('tipe_karyawan');
                })
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('karyawan_id')
                ->map(fn ($items) => (float) $items->first()->nominal);

            // Gunakan subquery dua tahap agar kompatibel dengan MySQL only_full_group_by:
            // Inner: label setiap baris dengan tanggal kerja menggunakan AttendanceWorkDate
            // Outer: GROUP BY karyawan_id dan tanggal (kolom sederhana, bukan ekspresi kompleks)
            $driver = \DB::connection()->getDriverName();
            $workDateExprAlias = \App\Helpers\AttendanceWorkDate::sql($driver, 'a');

            $lemburStartsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
            $lemburEndsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";
            $lemburStartsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
            $lemburEndsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";

            $workStart = $startDate->copy()->startOfDay();
            $workEnd = $endDate->copy()->addDays(2)->startOfDay();

            // Inner subquery: satu baris per log absensi, HANYA untuk karyawan yang terfilter
            $inner = \DB::table(\DB::raw('absensis a'))
                ->selectRaw("a.karyawan_id, a.tipe, a.waktu, ($workDateExprAlias) as tanggal")
                ->whereIn('a.karyawan_id', $karyawanIds)
                ->where('a.waktu', '>=', $workStart)
                ->where('a.waktu', '<', $workEnd)
                ->whereRaw("($lemburStartsSub OR $lemburEndsSub)");

            // Outer query: GROUP BY karyawan_id dan tanggal
            $attendance = \DB::table(\DB::raw("({$inner->toSql()}) as sub"))
                ->mergeBindings($inner)
                ->selectRaw("
                    sub.karyawan_id,
                    sub.tanggal,
                    MIN(CASE WHEN $lemburStartsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_masuk,
                    MAX(CASE WHEN $lemburEndsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_pulang
                ")
                ->whereBetween('sub.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('sub.karyawan_id', 'sub.tanggal')
                ->get()
                ->groupBy('karyawan_id');

            // Fetch all UangLembur and Rules (cached)
            $uangLemburs = \Illuminate\Support\Facades\Cache::remember('uang_lemburs_with_rules', 600, function () {
                return UangLembur::with('rules')->get();
            });

            // Fetch registered holidays (hari_liburs) within the selected date range
            $hariLiburDates = HariLibur::whereBetween('tanggal', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
                ->pluck('tanggal')
                ->map(fn ($t) => \Carbon\Carbon::parse($t)->toDateString())
                ->toArray();

            // Pra-kalkulasi metadata tanggal dalam periode (menghilangkan ribuan pemanggilan Carbon::parse & locale formatting)
            $dateMetadata = [];
            $tempCursor = $startDate->copy();
            while ($tempCursor->lte($endDate)) {
                $dStr = $tempCursor->toDateString();
                $tempLocale = $tempCursor->copy()->locale('id');
                $isHoliday = $tempCursor->isSunday() || in_array($dStr, $hariLiburDates);

                $dateMetadata[$dStr] = [
                    'date' => $dStr,
                    'is_holiday' => $isHoliday,
                    'is_saturday' => $tempCursor->isSaturday(),
                    'tipe_hari' => $isHoliday ? 'Hari Libur' : 'Hari Biasa',
                    'hari' => $tempLocale->isoFormat('dddd'),
                    'hari_singkat' => $tempLocale->isoFormat('ddd'),
                    'tanggal_format' => $tempLocale->isoFormat('D MMM Y'),
                    'hari_tanggal' => $tempLocale->isoFormat('dddd, D MMMM Y'),
                    'label' => $tempLocale->isoFormat('ddd, D MMM'),
                    'label_lengkap' => $tempLocale->isoFormat('dddd, D MMMM Y'),
                ];
                $tempCursor->addDay();
            }

            // Ambil data Pranota Lembur yang aktif HANYA untuk karyawan yang terfilter
            $activePranotas = \App\Models\PranotaLemburKaryawanHeader::whereNull('deleted_at')
                ->where(function ($q) use ($startDateStr, $endDateStr) {
                    $q->where(function ($sub) use ($startDateStr, $endDateStr) {
                        $sub->whereNotNull('periode_mulai')
                            ->whereNotNull('periode_selesai')
                            ->where('periode_mulai', '<=', $endDateStr)
                            ->where('periode_selesai', '>=', $startDateStr);
                    })->orWhere(function ($sub) use ($startDateStr, $endDateStr) {
                        $sub->whereNull('periode_mulai')
                            ->whereBetween('tanggal_pranota', [$startDateStr, $endDateStr]);
                    });
                })
                ->whereHas('karyawans', function ($q) use ($karyawanIds) {
                    $q->whereIn('karyawan_id', $karyawanIds);
                })
                ->with(['karyawans' => function ($q) use ($karyawanIds) {
                    $q->whereIn('karyawan_id', $karyawanIds);
                }])
                ->get();

            // Index pranota per karyawan dan tanggal
            $pranotaDateMap = []; // [karyawan_id][Y-m-d] => ['nomor' => nomor_pranota, 'id' => header_id]

            foreach ($activePranotas as $header) {
                foreach ($header->karyawans as $item) {
                    $kId = $item->karyawan_id;
                    $dates = $item->tanggal_lembur;
                    if (is_array($dates) && count($dates) > 0) {
                        foreach ($dates as $tgl) {
                            $pranotaDateMap[$kId][$tgl] = [
                                'nomor' => $header->nomor_pranota,
                                'id' => $header->id,
                            ];
                        }
                    } else {
                        // Legacy: lembur karyawan di seluruh periode pranota dianggap masuk pranota
                        $pMulai = $item->periode_mulai ? $item->periode_mulai->toDateString() : ($header->periode_mulai ? $header->periode_mulai->toDateString() : $header->tanggal_pranota->copy()->startOfMonth()->toDateString());
                        $pSelesai = $item->periode_selesai ? $item->periode_selesai->toDateString() : ($header->periode_selesai ? $header->periode_selesai->toDateString() : $header->tanggal_pranota->copy()->endOfMonth()->toDateString());

                        $curDate = \Carbon\Carbon::parse($pMulai);
                        $endCurDate = \Carbon\Carbon::parse($pSelesai);
                        while ($curDate->lte($endCurDate)) {
                            $pranotaDateMap[$kId][$curDate->toDateString()] = [
                                'nomor' => $header->nomor_pranota,
                                'id' => $header->id,
                            ];
                            $curDate->addDay();
                        }
                    }
                }
            }

            foreach ($karyawans as $karyawan) {
                $logs = $attendance->get($karyawan->id);
                // Lewati langsung karyawan yang tidak memiliki log lembur
                if (! $logs || $logs->isEmpty()) {
                    continue;
                }

                // Find matching rule for this Karyawan
                $matchingUangLemburs = [];
                if (is_array($karyawan->grup)) {
                    foreach ($karyawan->grup as $grupStr) {
                        $parts = explode(':', $grupStr);
                        if (count($parts) >= 2 && trim(strtoupper($parts[0])) === 'LEMBUR') {
                            $group = trim(strtoupper($parts[0]));
                            $sub_group = trim(strtoupper($parts[1]));

                            // Find in DB
                            $ul = $uangLemburs->first(function ($item) use ($group, $sub_group) {
                                return strtoupper($item->group) === $group && strtoupper($item->sub_group) === $sub_group;
                            });

                            if ($ul) {
                                $matchingUangLemburs[] = $ul;
                            }
                        }
                    }
                }

                $totalJamHariBiasa = 0;
                $totalJamHariLibur = 0;
                $totalNominal = 0;
                $totalUangMakanLembur = 0;
                $detailPerhitungan = [];

                // Iterasi langsung pada tanggal yang ada log lembur saja (bukan seluruh 31 hari)
                $sortedLogs = $logs->sortBy('tanggal');

                foreach ($sortedLogs as $dayLog) {
                    $dateStr = $dayLog->tanggal;
                    if (! isset($dateMetadata[$dateStr])) {
                        continue;
                    }

                    if (! $dayLog->waktu_lembur_masuk || ! $dayLog->waktu_lembur_pulang) {
                        continue;
                    }

                    $meta = $dateMetadata[$dateStr];
                    $isHoliday = $meta['is_holiday'];
                    $tipeHari = $meta['tipe_hari'];

                    $lm = Carbon::parse($dayLog->waktu_lembur_masuk);
                    $lp = Carbon::parse($dayLog->waktu_lembur_pulang);

                    if ($lp < $lm) {
                        $lp->addDay();
                    }

                    $durationMinutes = $lm->diffInMinutes($lp);
                    $durasiJam = (int) ceil($durationMinutes / 60);

                    if ($durasiJam > 24) {
                        $durasiJam = 24;
                    }

                    // Aturan Hari Sabtu: jika lembur selesai nanggung 17:00-17:59, bulatkan ke 18:00
                    $lpEvaluation = $lp->copy();
                    if ($meta['is_saturday'] && ! $isHoliday && $lpEvaluation->hour == 17) {
                        $lpEvaluation->setTime(18, 0, 0);
                    }

                    $jamMasukTime = $lm->format('H:i:s');
                    $jamPulangTime = $lp->format('H:i:s');

                    $nominalHariIni = 0;
                    $ruleApplied = null;

                    foreach ($matchingUangLemburs as $ul) {
                        $isPelabuhan1 = strtoupper(trim($ul->sub_group)) === 'PELABUHAN 1';

                        foreach ($ul->rules as $rule) {
                            if ($rule->tipe_hari === $tipeHari) {
                                $matchesTime = false;

                                if ($tipeHari === 'Hari Libur' && ! $isPelabuhan1) {
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
                                        $ruleMulai = \Carbon\Carbon::parse($lm->format('Y-m-d').' '.$rule->jam_mulai);

                                        if ($ruleMulai->copy()->addHours(6) < $lm) {
                                            $ruleMulai->addDay();
                                        }

                                        if ($rule->is_sampai_selesai) {
                                            if ($lpEvaluation >= $ruleMulai) {
                                                $matchesTime = true;
                                            }
                                        } elseif ($rule->jam_selesai) {
                                            $ruleSelesai = \Carbon\Carbon::parse($ruleMulai->format('Y-m-d').' '.$rule->jam_selesai);
                                            if ($ruleSelesai < $ruleMulai) {
                                                $ruleSelesai->addDay();
                                            }

                                            if ($lpEvaluation >= $ruleMulai && $lpEvaluation <= $ruleSelesai) {
                                                $matchesTime = true;
                                            }
                                        }
                                    } else {
                                        $matchesTime = true;
                                    }
                                }

                                if ($matchesTime) {
                                    if (strtolower(trim($rule->satuan)) === 'jam' || strtolower(trim($rule->satuan)) === 'per jam') {
                                        $nominalHariIni = $durasiJam * $rule->nominal;
                                    } else {
                                        $nominalHariIni = $rule->nominal;
                                    }
                                    $ruleApplied = $rule;
                                    break 2;
                                }
                            }
                        }
                    }

                    if ($isHoliday) {
                        $totalJamHariLibur += $durasiJam;
                        $baseUangMakan = $uangMakanMap->get($karyawan->id) ?? (float) ($karyawan->nominal_uang_makan ?? 0);
                        $pengaliUangMakan = 1.0;
                        if (! empty($matchingUangLemburs)) {
                            $pengaliUangMakan = (float) ($matchingUangLemburs[0]->pengali_uang_makan_hari_libur ?? 1);
                        }
                        $nominalUangMakanLembur = $baseUangMakan * $pengaliUangMakan;
                        $totalUangMakanLembur += $nominalUangMakanLembur;
                    } else {
                        $totalJamHariBiasa += $durasiJam;
                        $nominalUangMakanLembur = 0;
                    }

                    $totalNominal += $nominalHariIni;

                    $pranotaInfo = $pranotaDateMap[$karyawan->id][$dateStr] ?? null;
                    $isInPranota = ! empty($pranotaInfo);
                    $pranotaNomor = $pranotaInfo['nomor'] ?? null;

                    $detailPerhitungan[] = [
                        'tanggal' => $dateStr,
                        'hari' => $meta['hari'],
                        'hari_singkat' => $meta['hari_singkat'],
                        'tanggal_format' => $meta['tanggal_format'],
                        'hari_tanggal' => $meta['hari_tanggal'],
                        'tipe_hari' => $tipeHari,
                        'durasi_jam' => $durasiJam,
                        'jam_masuk' => $jamMasukTime,
                        'jam_pulang' => $jamPulangTime,
                        'nominal' => $nominalHariIni,
                        'uang_makan_lembur' => $nominalUangMakanLembur ?? 0,
                        'rule' => $ruleApplied ? $ruleApplied->satuan.' x '.number_format($ruleApplied->nominal, 0, ',', '.') : 'Tidak ada rumus',
                        'is_in_pranota' => $isInPranota,
                        'pranota_nomor' => $pranotaNomor,
                    ];
                }

                if ($totalJamHariBiasa > 0 || $totalJamHariLibur > 0 || $totalNominal > 0) {
                    $nominalUangMakan = $uangMakanMap->get($karyawan->id) ?? (float) ($karyawan->nominal_uang_makan ?? 0);

                    $totalDates = count($detailPerhitungan);
                    $pranotaDatesCount = 0;
                    $pranotaNomors = [];
                    $unpranotaJamBiasa = 0;
                    $unpranotaJamLibur = 0;
                    $unpranotaNominal = 0;
                    $unpranotaUml = 0;

                    foreach ($detailPerhitungan as $dp) {
                        if ($dp['is_in_pranota']) {
                            $pranotaDatesCount++;
                            if (! empty($dp['pranota_nomor']) && ! in_array($dp['pranota_nomor'], $pranotaNomors)) {
                                $pranotaNomors[] = $dp['pranota_nomor'];
                            }
                        } else {
                            if ($dp['tipe_hari'] === 'Hari Biasa') {
                                $unpranotaJamBiasa += $dp['durasi_jam'];
                            } else {
                                $unpranotaJamLibur += $dp['durasi_jam'];
                            }
                            $unpranotaNominal += $dp['nominal'];
                            $unpranotaUml += $dp['uang_makan_lembur'];
                        }
                    }

                    $isAllInPranota = ($totalDates > 0 && $pranotaDatesCount >= $totalDates);
                    $isPartialInPranota = ($pranotaDatesCount > 0 && $pranotaDatesCount < $totalDates);

                    $daftarHariLembur = array_map(function ($dp) {
                        return [
                            'tanggal' => $dp['tanggal'],
                            'hari' => $dp['hari'],
                            'hari_singkat' => $dp['hari_singkat'],
                            'label' => $dp['hari_singkat'].', '.date('j M', strtotime($dp['tanggal'])),
                            'label_lengkap' => $dp['hari_tanggal'],
                            'tipe_hari' => $dp['tipe_hari'],
                            'durasi_jam' => $dp['durasi_jam'],
                            'is_in_pranota' => $dp['is_in_pranota'],
                            'pranota_nomor' => $dp['pranota_nomor'],
                        ];
                    }, $detailPerhitungan);

                    $daftarHariLemburUnpranota = array_values(array_filter($daftarHariLembur, fn ($d) => ! $d['is_in_pranota']));

                    $rekapData[$karyawan->id] = [
                        'karyawan' => $karyawan,
                        'nominal_uang_makan' => $nominalUangMakan,
                        'total_jam_biasa' => $totalJamHariBiasa,
                        'total_jam_libur' => $totalJamHariLibur,
                        'total_nominal' => $totalNominal,
                        'total_uang_makan_lembur' => $totalUangMakanLembur,
                        'detail' => $detailPerhitungan,
                        'daftar_hari_lembur' => $daftarHariLembur,
                        'daftar_tanggal_lembur' => array_column($detailPerhitungan, 'tanggal'),
                        'daftar_hari_lembur_str' => implode(', ', array_column($daftarHariLembur, 'label')),
                        'total_hari_lembur' => count($detailPerhitungan),
                        'daftar_hari_lembur_unpranota' => $daftarHariLemburUnpranota,
                        'total_hari_unpranota' => count($daftarHariLemburUnpranota),
                        'is_all_in_pranota' => $isAllInPranota,
                        'is_partial_in_pranota' => $isPartialInPranota,
                        'pranota_dates_count' => $pranotaDatesCount,
                        'total_dates_count' => $totalDates,
                        'pranota_nomors' => $pranotaNomors,
                        'unpranota_jam' => $unpranotaJamBiasa + $unpranotaJamLibur,
                        'unpranota_jam_biasa' => $unpranotaJamBiasa,
                        'unpranota_jam_libur' => $unpranotaJamLibur,
                        'unpranota_nominal' => $unpranotaNominal,
                        'unpranota_uml' => $unpranotaUml,
                        'unpranota_grand_total' => $unpranotaNominal + $unpranotaUml,
                    ];
                }
            }

            // Filter berdasarkan status_pranota (semua, belum, sudah)
            if ($request->filled('status_pranota')) {
                $statusPranotaFilter = $request->status_pranota;
                if ($statusPranotaFilter === 'belum') {
                    $rekapData = array_filter($rekapData, fn ($item) => ! $item['is_all_in_pranota']);
                } elseif ($statusPranotaFilter === 'sudah') {
                    $rekapData = array_filter($rekapData, fn ($item) => $item['is_all_in_pranota'] || $item['is_partial_in_pranota']);
                }
            }
        }

        // Master Dropdowns — di-cache 5 menit untuk mempercepat reload filter/search
        $dropdownData = \Illuminate\Support\Facades\Cache::remember('perhitungan_lembur_dropdowns_v2', 300, function () {
            $allActiveKaryawans = Karyawan::whereNull('tanggal_berhenti')
                ->select(['pekerjaan', 'divisi', 'cabang', 'penempatan', 'grup', 'grup_bpjs'])
                ->get();

            $pekerjaans = $allActiveKaryawans->pluck('pekerjaan')->filter()->unique()->sort()->values();
            $divisis = $allActiveKaryawans->pluck('divisi')->filter()->unique()->sort()->values();
            $cabangs = $allActiveKaryawans->pluck('cabang')->filter()->unique()->sort()->values();
            $penempatans = $allActiveKaryawans->pluck('penempatan')->filter()->unique()->sort()->values();

            $grupMap = [
                'GAJI' => ['TUNAI', 'TRANSFER', 'ABK', 'MAGANG', 'HARIAN'],
                'UANG MAKAN' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
                'TRANSPORTASI' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
                'LEMBUR' => ['KANTOR JAKARTA', 'PELABUHAN', 'PELABUHAN 1', 'GARASI', 'KANTOR BATAM', 'PELABUHAN BATAM'],
                'CUTI' => [],
            ];
            foreach ($allActiveKaryawans->pluck('grup') as $grupArray) {
                if (is_array($grupArray)) {
                    foreach ($grupArray as $g) {
                        $parts = explode(':', $g, 2);
                        $main = $parts[0];
                        $sub = $parts[1] ?? '';
                        if ($main !== '') {
                            if (! isset($grupMap[$main])) {
                                $grupMap[$main] = [];
                            }
                            if ($sub !== '' && ! in_array($sub, $grupMap[$main])) {
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
                'BPJS-JKN' => ['BPU REIMBURSMENT'],
            ];
            foreach ($allActiveKaryawans->pluck('grup_bpjs') as $grupBpjsArray) {
                if (is_array($grupBpjsArray)) {
                    foreach ($grupBpjsArray as $g) {
                        $parts = explode(':', $g, 2);
                        $main = $parts[0];
                        $sub = $parts[1] ?? '';
                        if ($main !== '') {
                            if (! isset($grupBpjsMap[$main])) {
                                $grupBpjsMap[$main] = [];
                            }
                            if ($sub !== '' && ! in_array($sub, $grupBpjsMap[$main])) {
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

            return [
                'pekerjaans' => $pekerjaans,
                'divisis' => $divisis,
                'cabangs' => $cabangs,
                'penempatans' => $penempatans,
                'grupMap' => $grupMap,
                'grupsList' => $grupsList,
                'grupBpjsMap' => $grupBpjsMap,
                'grupsBpjsList' => $grupsBpjsList,
            ];
        });

        $pekerjaans = $dropdownData['pekerjaans'];
        $divisis = $dropdownData['divisis'];
        $cabangs = $dropdownData['cabangs'];
        $penempatans = $dropdownData['penempatans'];
        $grupMap = $dropdownData['grupMap'];
        $grupsList = $dropdownData['grupsList'];
        $grupBpjsMap = $dropdownData['grupBpjsMap'];
        $grupsBpjsList = $dropdownData['grupsBpjsList'];

        // Riwayat Pranota Lembur Karyawan yang dibuat di sistem
        // Dibatasi 50 terbaru untuk menghindari eager load berlebihan
        $riwayatPranotaUser = \App\Models\PranotaLemburKaryawanHeader::with([
            'creator.karyawan:id,nama_lengkap',
            'karyawans.karyawan:id,nama_lengkap,nama_panggilan,nik',
            'pranotaPuml:id,nomor_pranota,status',
        ])
            ->withCount('karyawans')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

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
            'grupsBpjsList',
            'riwayatPranotaUser'
        ));
    }

    public static function calculateAttendanceForKaryawans($karyawanIds, $startDateStr, $endDateStr)
    {
        $startDate = Carbon::parse($startDateStr);
        $endDate = Carbon::parse($endDateStr);

        $karyawanIds = is_array($karyawanIds) ? array_values(array_filter($karyawanIds)) : $karyawanIds->values()->toArray();
        if (empty($karyawanIds)) {
            return [];
        }

        $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();

        $uangMakanMap = \App\Models\UangMakan::whereIn('karyawan_id', $karyawanIds)
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'NOT LIKE', '%TidakTetap%')
                    ->orWhereNull('tipe_karyawan');
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('karyawan_id')
            ->map(fn ($items) => (float) $items->first()->nominal);

        $driver = \DB::connection()->getDriverName();
        $workDateExprAlias = \App\Helpers\AttendanceWorkDate::sql($driver, 'a');

        $lemburStartsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
        $lemburEndsSub = "LOWER(REPLACE(a.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";
        $lemburStartsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur masuk', 'mulai lembur', 'lembur')";
        $lemburEndsOut = "LOWER(REPLACE(sub.tipe, '_', ' ')) IN ('lembur pulang', 'selesai lembur', 'lembur keluar')";

        $workStart = $startDate->copy()->startOfDay();
        $workEnd = $endDate->copy()->addDays(2)->startOfDay();

        $inner = \DB::table(\DB::raw('absensis a'))
            ->selectRaw("a.karyawan_id, a.tipe, a.waktu, ($workDateExprAlias) as tanggal")
            ->whereIn('a.karyawan_id', $karyawanIds)
            ->where('a.waktu', '>=', $workStart)
            ->where('a.waktu', '<', $workEnd)
            ->whereRaw("($lemburStartsSub OR $lemburEndsSub)");

        $attendance = \DB::table(\DB::raw("({$inner->toSql()}) as sub"))
            ->mergeBindings($inner)
            ->selectRaw("
                sub.karyawan_id,
                sub.tanggal,
                MIN(CASE WHEN $lemburStartsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_masuk,
                MAX(CASE WHEN $lemburEndsOut THEN sub.waktu ELSE NULL END) as waktu_lembur_pulang
            ")
            ->whereBetween('sub.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('sub.karyawan_id', 'sub.tanggal')
            ->get()
            ->groupBy('karyawan_id');

        $uangLemburs = \Illuminate\Support\Facades\Cache::remember('uang_lemburs_with_rules', 600, function () {
            return UangLembur::with('rules')->get();
        });

        $hariLiburDates = HariLibur::whereBetween('tanggal', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ])
            ->pluck('tanggal')
            ->map(fn ($t) => \Carbon\Carbon::parse($t)->toDateString())
            ->toArray();

        $dateMetadata = [];
        $tempCursor = $startDate->copy();
        while ($tempCursor->lte($endDate)) {
            $dStr = $tempCursor->toDateString();
            $isHoliday = $tempCursor->isSunday() || in_array($dStr, $hariLiburDates);

            $dateMetadata[$dStr] = [
                'date' => $dStr,
                'is_holiday' => $isHoliday,
                'is_saturday' => $tempCursor->isSaturday(),
                'tipe_hari' => $isHoliday ? 'Hari Libur' : 'Hari Biasa',
            ];
            $tempCursor->addDay();
        }

        $results = [];

        foreach ($karyawans as $karyawan) {
            $logs = $attendance->get($karyawan->id);
            if (! $logs || $logs->isEmpty()) {
                $results[$karyawan->id] = [
                    'karyawan_id' => $karyawan->id,
                    'total_jam' => 0,
                    'jam_lembur' => '0 Jam',
                    'nominal_lembur' => 0,
                    'uang_makan_lembur' => 0,
                    'nominal_awal' => 0,
                    'total_hari' => 0,
                    'tanggal_lembur' => [],
                ];
                continue;
            }

            $matchingUangLemburs = [];
            if (is_array($karyawan->grup)) {
                foreach ($karyawan->grup as $grupStr) {
                    $parts = explode(':', $grupStr);
                    if (count($parts) >= 2 && trim(strtoupper($parts[0])) === 'LEMBUR') {
                        $group = trim(strtoupper($parts[0]));
                        $sub_group = trim(strtoupper($parts[1]));

                        $ul = $uangLemburs->first(function ($item) use ($group, $sub_group) {
                            return strtoupper($item->group) === $group && strtoupper($item->sub_group) === $sub_group;
                        });

                        if ($ul) {
                            $matchingUangLemburs[] = $ul;
                        }
                    }
                }
            }

            $totalJamHariBiasa = 0;
            $totalJamHariLibur = 0;
            $totalNominal = 0;
            $totalUangMakanLembur = 0;
            $datesList = [];

            $sortedLogs = $logs->sortBy('tanggal');

            foreach ($sortedLogs as $dayLog) {
                $dateStr = $dayLog->tanggal;
                if (! isset($dateMetadata[$dateStr])) {
                    continue;
                }

                if (! $dayLog->waktu_lembur_masuk || ! $dayLog->waktu_lembur_pulang) {
                    continue;
                }

                $meta = $dateMetadata[$dateStr];
                $isHoliday = $meta['is_holiday'];
                $tipeHari = $meta['tipe_hari'];

                $lm = Carbon::parse($dayLog->waktu_lembur_masuk);
                $lp = Carbon::parse($dayLog->waktu_lembur_pulang);

                if ($lp < $lm) {
                    $lp->addDay();
                }

                $durationMinutes = $lm->diffInMinutes($lp);
                $durasiJam = (int) ceil($durationMinutes / 60);

                if ($durasiJam > 24) {
                    $durasiJam = 24;
                }

                $lpEvaluation = $lp->copy();
                if ($meta['is_saturday'] && ! $isHoliday && $lpEvaluation->hour == 17) {
                    $lpEvaluation->setTime(18, 0, 0);
                }

                $nominalHariIni = 0;

                foreach ($matchingUangLemburs as $ul) {
                    $isPelabuhan1 = strtoupper(trim($ul->sub_group)) === 'PELABUHAN 1';

                    foreach ($ul->rules as $rule) {
                        if ($rule->tipe_hari === $tipeHari) {
                            $matchesTime = false;

                            if ($tipeHari === 'Hari Libur' && ! $isPelabuhan1) {
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
                                    $ruleMulai = \Carbon\Carbon::parse($lm->format('Y-m-d').' '.$rule->jam_mulai);

                                    if ($ruleMulai->copy()->addHours(6) < $lm) {
                                        $ruleMulai->addDay();
                                    }

                                    if ($rule->is_sampai_selesai) {
                                        if ($lpEvaluation >= $ruleMulai) {
                                            $matchesTime = true;
                                        }
                                    } elseif ($rule->jam_selesai) {
                                        $ruleSelesai = \Carbon\Carbon::parse($ruleMulai->format('Y-m-d').' '.$rule->jam_selesai);
                                        if ($ruleSelesai < $ruleMulai) {
                                            $ruleSelesai->addDay();
                                        }

                                        if ($lpEvaluation >= $ruleMulai && $lpEvaluation <= $ruleSelesai) {
                                            $matchesTime = true;
                                        }
                                    }
                                } else {
                                    $matchesTime = true;
                                }
                            }

                            if ($matchesTime) {
                                if (strtolower(trim($rule->satuan)) === 'jam' || strtolower(trim($rule->satuan)) === 'per jam') {
                                    $nominalHariIni = $durasiJam * $rule->nominal;
                                } else {
                                    $nominalHariIni = $rule->nominal;
                                }
                                break 2;
                            }
                        }
                    }
                }

                if ($isHoliday) {
                    $totalJamHariLibur += $durasiJam;
                    $baseUangMakan = $uangMakanMap->get($karyawan->id) ?? (float) ($karyawan->nominal_uang_makan ?? 0);
                    $pengaliUangMakan = 1.0;
                    if (! empty($matchingUangLemburs)) {
                        $pengaliUangMakan = (float) ($matchingUangLemburs[0]->pengali_uang_makan_hari_libur ?? 1);
                    }
                    $nominalUangMakanLembur = $baseUangMakan * $pengaliUangMakan;
                    $totalUangMakanLembur += $nominalUangMakanLembur;
                } else {
                    $totalJamHariBiasa += $durasiJam;
                    $nominalUangMakanLembur = 0;
                }

                $totalNominal += $nominalHariIni;
                $datesList[] = $dateStr;
            }

            $totalJam = $totalJamHariBiasa + $totalJamHariLibur;
            $grandTotal = $totalNominal + $totalUangMakanLembur;

            $results[$karyawan->id] = [
                'karyawan_id' => $karyawan->id,
                'total_jam' => $totalJam,
                'jam_lembur' => $totalJam . ' Jam',
                'nominal_lembur' => $totalNominal,
                'uang_makan_lembur' => $totalUangMakanLembur,
                'nominal_awal' => $grandTotal,
                'total_hari' => count($datesList),
                'tanggal_lembur' => array_values(array_unique($datesList)),
            ];
        }

        return $results;
    }
}

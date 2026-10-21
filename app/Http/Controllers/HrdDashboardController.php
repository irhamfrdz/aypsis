<?php

namespace App\Http\Controllers;

use App\Exports\HrdAbsensiExport;
use App\Exports\HrdDashboardDetailExport;
use App\Models\Absensi;
use App\Models\Cabang;
use App\Models\Cuti;
use App\Models\Karyawan;
use App\Services\WorkingHoursService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HrdDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard HRD.
     */
    public function index(Request $request)
    {
        $selectedCabang = $request->input('cabang');
        $selectedGroup = $request->input('grup');
        $selectedDepartemen = $request->input('departemen');

        // Resolve dates: support tanggal_dari & tanggal_sampai, with fallback to tanggal_dashboard or today
        $tanggalDariInput = $request->input('tanggal_dari', $request->input('tanggal_dashboard'));
        $tanggalSampaiInput = $request->input('tanggal_sampai', $request->input('tanggal_dashboard'));

        if (! $tanggalDariInput && ! $tanggalSampaiInput) {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($tanggalDariInput && ! $tanggalSampaiInput) {
            $startDate = Carbon::parse($tanggalDariInput)->startOfDay();
            $endDate = Carbon::parse($tanggalDariInput)->endOfDay();
        } elseif (! $tanggalDariInput && $tanggalSampaiInput) {
            $startDate = Carbon::parse($tanggalSampaiInput)->startOfDay();
            $endDate = Carbon::parse($tanggalSampaiInput)->endOfDay();
        } else {
            $startDate = Carbon::parse($tanggalDariInput)->startOfDay();
            $endDate = Carbon::parse($tanggalSampaiInput)->endOfDay();
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }
        }

        $isSingleDay = $startDate->isSameDay($endDate);
        $filterDate = $startDate->copy(); // For backward compatibility in views
        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        // Daftar cabang unik (untuk filter dropdown)
        $allCabangs = Cabang::orderBy('nama_cabang')->pluck('nama_cabang')
            ->merge(
                Karyawan::where('status', 'active')
                    ->whereNull('tanggal_berhenti')
                    ->whereNotNull('cabang')
                    ->where('cabang', '!=', '')
                    ->distinct()
                    ->pluck('cabang')
            )
            ->map(fn ($c) => trim($c))
            ->unique()
            ->filter(fn ($c) => $c !== '' && strtoupper($c) !== 'BEHENTI')
            ->sort()
            ->values()
            ->toArray();

        // Daftar grup unik dari semua karyawan aktif (untuk filter dropdown)
        // Nilai grup berformat "KATEGORI:SUBKATEGORI" — ambil hanya bagian sebelum ':'
        $allGroups = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti')
            ->whereNotNull('grup')
            ->where('grup', '!=', '[]')
            ->where('grup', '!=', 'null')
            ->pluck('grup')
            ->flatMap(fn ($g) => is_array($g) ? $g : [])
            ->map(fn ($v) => trim(explode(':', $v)[0]))
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $allDepartemens = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti')
            ->whereNotNull('departemen')
            ->where('departemen', '!=', '')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen')
            ->map(fn ($departemen) => trim($departemen))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Query dasar karyawan aktif (dengan filter cabang dan grup jika dipilih)
        $karyawanBaseQuery = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti');

        if (! empty($selectedCabang)) {
            $karyawanBaseQuery->where('cabang', $selectedCabang);
        }

        if (! empty($selectedDepartemen)) {
            $karyawanBaseQuery->where('departemen', $selectedDepartemen);
        }

        if (! empty($selectedGroup)) {
            $karyawanBaseQuery->where(function ($q) use ($selectedGroup) {
                $q->where('grup', 'LIKE', '%"'.$selectedGroup.':%')
                    ->orWhere('grup', 'LIKE', '%"'.$selectedGroup.'"%')
                    ->orWhere('grup', 'LIKE', '%'.$selectedGroup.'%');
            });
        }

        // 1. Total Karyawan Aktif
        $totalKaryawanAktif = (clone $karyawanBaseQuery)->count();
        $activeKaryawanIds = (clone $karyawanBaseQuery)->pluck('id')->toArray();

        // 2. Karyawan Absen Masuk (Hari Ini atau Rentang Periode)
        $absensiMasukQuery = Absensi::with(['karyawan' => function ($q) {
            $q->select('id', 'nik', 'nama_lengkap', 'divisi', 'cabang', 'grup', 'penempatan', 'departemen');
        }])
            ->select('id', 'karyawan_id', 'nik', 'waktu', 'tipe', 'latitude', 'longitude', 'detail_lokasi')
            ->where('tipe', 'Masuk');

        if ($isSingleDay) {
            $absensiMasukQuery->whereDate('waktu', $startDateStr);
        } else {
            $absensiMasukQuery->whereBetween('waktu', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);
        }

        if (! empty($selectedCabang) || ! empty($selectedGroup) || ! empty($selectedDepartemen)) {
            $absensiMasukQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiMasuk = $absensiMasukQuery->get();

        $workingHours = new WorkingHoursService;
        $karyawanIdsAbsen = $absensiMasuk->pluck('karyawan_id')->filter()->unique()->toArray();

        // 3. Karyawan Belum Absen Masuk
        // Pada rentang tanggal, satu scan masuk di hari lain tidak boleh menutupi
        // hari yang memiliki aktivitas absensi tetapi tidak memiliki scan masuk.
        $karyawanBelumAbsen = (clone $karyawanBaseQuery)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        if (! $isSingleDay && $activeKaryawanIds) {
            $logsByEmployee = Absensi::select('karyawan_id', 'tipe', 'waktu')
                ->whereIn('karyawan_id', $activeKaryawanIds)
                ->whereBetween('waktu', [
                    $startDate->copy()->subDay()->setTime(6, 0),
                    $endDate->copy()->addDay()->setTime(5, 59, 59),
                ])
                ->get()
                ->groupBy('karyawan_id');

            $karyawanBelumAbsen = $karyawanBelumAbsen
                ->filter(function ($karyawan) use ($logsByEmployee, $karyawanIdsAbsen, $startDateStr, $endDateStr) {
                    $days = $logsByEmployee->get($karyawan->id, collect())
                        ->groupBy(function ($log) {
                            // Sama dengan tanggal kerja scan biasa di rekap absensi.
                            return Carbon::parse($log->waktu)->subHours(6)->toDateString();
                        });

                    $missingDates = $days->filter(function ($logs, $date) use ($startDateStr, $endDateStr) {
                        if ($date < $startDateStr || $date > $endDateStr) {
                            return false;
                        }

                        return ! $logs->contains(function ($log) {
                            $type = strtolower(trim(str_replace('_', ' ', $log->tipe ?? '')));

                            return in_array($type, ['masuk', 'check in', 'in']);
                        });
                    })->keys()->sort()->values()->all();

                    $karyawan->tanggal_belum_masuk = $missingDates;

                    return $missingDates !== [] || ! in_array($karyawan->id, $karyawanIdsAbsen);
                })
                ->values();
        } else {
            $karyawanBelumAbsen = $karyawanBelumAbsen
                ->whereNotIn('id', $karyawanIdsAbsen)
                ->values();
        }

        // 4. Karyawan Absen Terlambat
        // Evaluasi per absensi berdasarkan jam kerja aktif karyawan.
        $karyawanTerlambat = $absensiMasuk->filter(function ($absen) use ($workingHours) {
            $waktuAbsen = Carbon::parse($absen->waktu);
            $isExempt = $absen->karyawan ? $absen->karyawan->isExemptFromTerlambat() : false;

            return $absen->karyawan
                && $workingHours->lateMinutes($absen->karyawan, $waktuAbsen, $waktuAbsen) > 0
                && ! $isExempt;
        })
            ->sortByDesc('waktu')
            ->unique('karyawan_id')
            ->sortBy(function ($absen) {
                return strtolower($absen->karyawan->nama_lengkap ?? '');
            })
            ->values();

        // Karyawan yang hadir normal: tapping masuk sampai batas toleransi atau bebas keterlambatan.
        // Satu karyawan hanya ditampilkan satu kali pada daftar dashboard.
        $karyawanHadirNormal = $absensiMasuk->filter(function ($absen) use ($workingHours) {
            $waktuAbsen = Carbon::parse($absen->waktu);
            $isExempt = $absen->karyawan ? $absen->karyawan->isExemptFromTerlambat() : false;

            return ($absen->karyawan && $workingHours->lateMinutes($absen->karyawan, $waktuAbsen, $waktuAbsen) === 0) || $isExempt;
        })
            ->sortByDesc('waktu')
            ->unique('karyawan_id')
            ->sortBy(function ($absen) {
                return strtolower($absen->karyawan->nama_lengkap ?? '');
            })
            ->values();

        // Hitung jarak titik GPS absen ke lokasi absensi aktif terdekat.
        $lokasiAbsensi = DB::table('lokasi_absensis')
            ->where('is_active', 1)
            ->get(['nama_lokasi', 'latitude', 'longitude', 'radius']);

        $parsedLokasi = [];
        foreach ($lokasiAbsensi as $lok) {
            if (is_numeric($lok->latitude) && is_numeric($lok->longitude)) {
                $parsedLokasi[] = [
                    'nama_lokasi' => $lok->nama_lokasi,
                    'lat_rad' => deg2rad((float) $lok->latitude),
                    'lon_rad' => deg2rad((float) $lok->longitude),
                    'radius' => (int) $lok->radius,
                ];
            }
        }

        if (! empty($parsedLokasi)) {
            foreach ($karyawanHadirNormal as $absen) {
                $absen->jarak_absen_meter = null;
                $absen->radius_absensi_meter = null;
                $absen->nama_lokasi_absensi = null;

                if (! is_numeric($absen->latitude) || ! is_numeric($absen->longitude)) {
                    continue;
                }

                $lat1 = deg2rad((float) $absen->latitude);
                $lon1 = deg2rad((float) $absen->longitude);
                $minJarak = null;
                $closestLok = null;

                foreach ($parsedLokasi as $lok) {
                    $dLat = $lok['lat_rad'] - $lat1;
                    $dLon = $lok['lon_rad'] - $lon1;
                    $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lok['lat_rad']) * sin($dLon / 2) ** 2;
                    $jarak = 6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a));

                    if ($minJarak === null || $jarak < $minJarak) {
                        $minJarak = $jarak;
                        $closestLok = $lok;
                    }
                }

                if ($closestLok !== null) {
                    $absen->jarak_absen_meter = round($minJarak, 1);
                    $absen->radius_absensi_meter = $closestLok['radius'];
                    $absen->nama_lokasi_absensi = $closestLok['nama_lokasi'];
                }
            }
        }

        // 5. Karyawan Cuti / Izin
        $karyawanCutiQuery = Cuti::with('karyawan')
            ->whereDate('tanggal_mulai', '<=', $endDateStr)
            ->whereDate('tanggal_selesai', '>=', $startDateStr)
            ->where('status', 'approved');

        if (! empty($selectedCabang) || ! empty($selectedGroup) || ! empty($selectedDepartemen)) {
            $karyawanCutiQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $karyawanCuti = $karyawanCutiQuery->get();

        // 6. Karyawan Belum Absen Pulang
        // Yaitu karyawan yang SUDAH absen masuk, tapi BELUM absen pulang
        $absensiPulangQuery = Absensi::where('tipe', 'Pulang');
        if ($isSingleDay) {
            $absensiPulangQuery->whereDate('waktu', $startDateStr);
        } else {
            $absensiPulangQuery->whereBetween('waktu', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);
        }

        if (! empty($selectedCabang) || ! empty($selectedGroup) || ! empty($selectedDepartemen)) {
            $absensiPulangQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiPulang = $absensiPulangQuery->get(['karyawan_id', 'waktu']);
        $masukPerKaryawan = $absensiMasuk->groupBy('karyawan_id');
        $pulangPerKaryawan = $absensiPulang->groupBy('karyawan_id');

        $karyawanBelumAbsenPulang = (clone $karyawanBaseQuery)
            ->whereIn('id', $karyawanIdsAbsen)
            ->orderBy('nama_lengkap', 'asc')
            ->get()
            ->filter(function ($karyawan) use ($masukPerKaryawan, $pulangPerKaryawan) {
                $karyawan->tanggal_belum_pulang = $this->missingClockOutDates(
                    $masukPerKaryawan->get($karyawan->id, collect()),
                    $pulangPerKaryawan->get($karyawan->id, collect())
                );

                return $karyawan->tanggal_belum_pulang !== [];
            })
            ->values();

        // 7. Absensi Luar Radius
        $absensiLuarRadiusQuery = Absensi::with(['karyawan' => function ($q) {
            $q->select('id', 'nik', 'nama_lengkap', 'divisi', 'cabang', 'grup', 'penempatan');
        }])
            ->select('id', 'karyawan_id', 'nik', 'waktu', 'tipe', 'latitude', 'longitude', 'detail_lokasi')
            ->where('detail_lokasi', 'like', '%Di luar radius%')
            ->orderBy('waktu', 'asc');

        if ($isSingleDay) {
            $absensiLuarRadiusQuery->whereDate('waktu', $startDateStr);
        } else {
            $absensiLuarRadiusQuery->whereBetween('waktu', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);
        }

        if (! empty($selectedCabang) || ! empty($selectedGroup) || ! empty($selectedDepartemen)) {
            $absensiLuarRadiusQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiLuarRadius = $absensiLuarRadiusQuery->get();

        // 8. Total presensi (Masuk + Pulang)
        $totalPresensiHariIniQuery = Absensi::query();
        if ($isSingleDay) {
            $totalPresensiHariIniQuery->whereDate('waktu', $startDateStr);
        } else {
            $totalPresensiHariIniQuery->whereBetween('waktu', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);
        }

        if (! empty($selectedCabang) || ! empty($selectedGroup) || ! empty($selectedDepartemen)) {
            $totalPresensiHariIniQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $totalPresensiHariIni = $totalPresensiHariIniQuery->count();

        $jamBatas = $isSingleDay ? ($startDate->isSaturday() ? 8 : 9) : 9;

        return view('hrd-dashboard.index', compact(
            'filterDate',
            'startDate',
            'endDate',
            'isSingleDay',
            'jamBatas',
            'totalKaryawanAktif',
            'activeKaryawanIds',
            'karyawanBelumAbsen',
            'karyawanTerlambat',
            'karyawanHadirNormal',
            'karyawanCuti',
            'karyawanBelumAbsenPulang',
            'absensiMasuk',
            'absensiLuarRadius',
            'totalPresensiHariIni',
            'allGroups',
            'selectedGroup',
            'allDepartemens',
            'selectedDepartemen',
            'allCabangs',
            'selectedCabang'
        ));
    }

    protected function missingClockOutDates(\Illuminate\Support\Collection $masuk, \Illuminate\Support\Collection $pulang): array
    {
        // Cocokkan per tanggal kalender, sesuai filter tanggal dashboard.
        $dates = fn ($logs) => $logs->map(fn ($log) => Carbon::parse($log->waktu)->toDateString())->unique();

        return $dates($masuk)->diff($dates($pulang))->sort()->values()->all();
    }

    /**
     * Export rekap absensi HRD (4 sheets).
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', $request->input('tanggal_dari', Carbon::now()->startOfMonth()->toDateString()));
        $endDate = $request->input('end_date', $request->input('tanggal_sampai', Carbon::now()->endOfMonth()->toDateString()));

        $fileName = 'Rekap_Absensi_HRD_'.str_replace('-', '', $startDate).'_'.str_replace('-', '', $endDate).'.xlsx';

        return Excel::download(new HrdAbsensiExport($startDate, $endDate), $fileName);
    }

    /** Export the same rows and filters shown in one dashboard detail table. */
    public function exportDetail(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:belum-masuk,hadir-normal,belum-pulang,terlambat,luar-radius',
            'q' => 'nullable|string|max:255',
        ]);

        $data = $this->index($request)->getData();
        $search = mb_strtolower(trim($validated['q'] ?? ''));
        $employee = static fn ($karyawan) => [
            $karyawan->nik ?? '-',
            $karyawan->nama_lengkap ?? '-',
            $karyawan->divisi ?? '-',
            $karyawan->cabang ?? '-',
        ];
        $employeeSearch = static fn ($karyawan) => implode(' ', $employee($karyawan));
        $entry = static fn (array $row, string $searchText) => [
            'row' => $row,
            'search' => mb_strtolower($searchText),
        ];

        [$label, $headings, $items] = match ($validated['kategori']) {
            'belum-masuk' => [
                'Belum_Absen_Masuk',
                ['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Tanggal Tanpa Scan Masuk'],
                $data['karyawanBelumAbsen']->map(function ($karyawan) use ($employee, $employeeSearch, $entry) {
                    $dates = collect($karyawan->tanggal_belum_masuk ?? [])
                        ->map(fn ($date) => Carbon::parse($date)->format('d-m-Y'))
                        ->implode(', ');

                    return $entry(array_merge($employee($karyawan), [
                        $dates ?: 'Tidak ada scan masuk dalam periode',
                    ]), $employeeSearch($karyawan).' '.implode(' ', $karyawan->tanggal_belum_masuk ?? []));
                }),
            ],
            'hadir-normal' => [
                'Hadir_Normal',
                ['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Waktu Tapping Masuk', 'Jarak (meter)', 'Radius (meter)', 'Lokasi'],
                $data['karyawanHadirNormal']->map(fn ($absen) => $entry(array_merge($employee($absen->karyawan), [
                    Carbon::parse($absen->waktu)->format('d-m-Y H:i:s'),
                    $absen->jarak_absen_meter,
                    $absen->radius_absensi_meter,
                    $absen->nama_lokasi_absensi ?? 'GPS tidak tersedia',
                ]), $employeeSearch($absen->karyawan))),
            ],
            'belum-pulang' => [
                'Belum_Absen_Pulang',
                ['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Tanggal Belum Absen Pulang'],
                $data['karyawanBelumAbsenPulang']->map(function ($karyawan) use ($entry, $employee, $employeeSearch) {
                    $dates = collect($karyawan->tanggal_belum_pulang ?? [])
                        ->map(fn ($date) => Carbon::parse($date)->format('d-m-Y'))->implode(', ');

                    return $entry(array_merge($employee($karyawan), [$dates]), $employeeSearch($karyawan).' '.$dates);
                }),
            ],
            'terlambat' => [
                'Absen_Terlambat',
                ['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Waktu Tapping Masuk'],
                $data['karyawanTerlambat']->map(fn ($absen) => $entry(array_merge($employee($absen->karyawan), [
                    Carbon::parse($absen->waktu)->format('d-m-Y H:i:s'),
                ]), $employeeSearch($absen->karyawan))),
            ],
            'luar-radius' => [
                'Absen_Luar_Radius',
                ['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Waktu', 'Tipe', 'Detail Lokasi'],
                $data['absensiLuarRadius']->map(fn ($absen) => $entry(array_merge($employee($absen->karyawan), [
                    Carbon::parse($absen->waktu)->format('d-m-Y H:i:s'),
                    $absen->tipe,
                    $absen->detail_lokasi ?: 'Koordinat di luar radius',
                ]), implode(' ', [
                    $absen->karyawan->nik ?? '',
                    $absen->karyawan->nama_lengkap ?? '',
                    $absen->tipe ?? '',
                    $absen->detail_lokasi ?? '',
                    $absen->karyawan->cabang ?? '',
                ]))),
            ],
        };

        $rows = $items
            ->filter(fn ($item) => $search === '' || str_contains($item['search'], $search))
            ->values()
            ->map(fn ($item, $index) => array_merge([$index + 1], $item['row']))
            ->all();

        $fileName = $label.'_'.$data['startDate']->format('Ymd').'_'.$data['endDate']->format('Ymd').'.xlsx';

        return Excel::download(new HrdDashboardDetailExport($headings, $rows), $fileName);
    }
}

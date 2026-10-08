<?php

namespace App\Http\Controllers;

use App\Exports\HrdAbsensiExport;
use App\Models\Absensi;
use App\Models\Cabang;
use App\Models\Cuti;
use App\Models\Karyawan;
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

        // Query dasar karyawan aktif (dengan filter cabang dan grup jika dipilih)
        $karyawanBaseQuery = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti');

        if (! empty($selectedCabang)) {
            $karyawanBaseQuery->where('cabang', $selectedCabang);
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
        $absensiMasukQuery = Absensi::with('karyawan')
            ->where('tipe', 'Masuk');

        if ($isSingleDay) {
            $absensiMasukQuery->whereDate('waktu', $startDateStr);
        } else {
            $absensiMasukQuery->whereBetween('waktu', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);
        }

        if (! empty($selectedCabang) || ! empty($selectedGroup)) {
            $absensiMasukQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiMasuk = $absensiMasukQuery->get();

        $karyawanIdsAbsen = $absensiMasuk->pluck('karyawan_id')->filter()->unique()->toArray();

        // 3. Karyawan Belum Absen
        // Yaitu karyawan aktif yang id-nya belum ada di daftar absen masuk dalam periode
        $karyawanBelumAbsen = (clone $karyawanBaseQuery)
            ->whereNotIn('id', $karyawanIdsAbsen)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 4. Karyawan Absen Terlambat
        // Evaluasi per absensi: cek apakah waktu > jam batas toleransi 5 menit (Sabtu: 08:05, Hari lain: 09:05)
        $karyawanTerlambat = $absensiMasuk->filter(function ($absen) {
            $waktuAbsen = Carbon::parse($absen->waktu);
            $jamBatasHari = $waktuAbsen->isSaturday() ? 8 : 9;
            $batasTerlambat = $waktuAbsen->copy()->setHour($jamBatasHari)->setMinute(5)->setSecond(0);
            $isExempt = $absen->karyawan ? $absen->karyawan->isExemptFromTerlambat() : false;

            return $waktuAbsen->greaterThan($batasTerlambat) && ! $isExempt;
        })->values();

        // Karyawan yang hadir normal: tapping masuk sampai batas toleransi atau bebas keterlambatan.
        $karyawanHadirNormal = $absensiMasuk->filter(function ($absen) {
            $waktuAbsen = Carbon::parse($absen->waktu);
            $jamBatasHari = $waktuAbsen->isSaturday() ? 8 : 9;
            $batasTerlambat = $waktuAbsen->copy()->setHour($jamBatasHari)->setMinute(5)->setSecond(0);
            $isExempt = $absen->karyawan ? $absen->karyawan->isExemptFromTerlambat() : false;

            return $waktuAbsen->lessThanOrEqualTo($batasTerlambat) || $isExempt;
        });

        if ($isSingleDay) {
            $karyawanHadirNormal = $karyawanHadirNormal->unique('karyawan_id');
        }
        $karyawanHadirNormal = $karyawanHadirNormal->sortBy(function ($absen) {
            return strtolower($absen->karyawan->nama_lengkap ?? '');
        })->values();

        // Hitung jarak titik GPS absen ke lokasi absensi aktif terdekat.
        $lokasiAbsensi = DB::table('lokasi_absensis')
            ->where('is_active', 1)
            ->get(['nama_lokasi', 'latitude', 'longitude', 'radius']);
        foreach ($karyawanHadirNormal as $absen) {
            $absen->jarak_absen_meter = null;
            $absen->radius_absensi_meter = null;
            $absen->nama_lokasi_absensi = null;

            if (! is_numeric($absen->latitude) || ! is_numeric($absen->longitude) || $lokasiAbsensi->isEmpty()) {
                continue;
            }

            $lat1 = deg2rad((float) $absen->latitude);
            $lon1 = deg2rad((float) $absen->longitude);
            $terdekat = $lokasiAbsensi->map(function ($lokasi) use ($lat1, $lon1) {
                if (! is_numeric($lokasi->latitude) || ! is_numeric($lokasi->longitude)) {
                    return null;
                }

                $lat2 = deg2rad((float) $lokasi->latitude);
                $lon2 = deg2rad((float) $lokasi->longitude);
                $dLat = $lat2 - $lat1;
                $dLon = $lon2 - $lon1;
                $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
                $jarak = 6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a));

                $lokasi->jarak_meter = $jarak;

                return $lokasi;
            })->filter()->sortBy('jarak_meter')->first();

            if ($terdekat) {
                $absen->jarak_absen_meter = round($terdekat->jarak_meter, 1);
                $absen->radius_absensi_meter = (int) $terdekat->radius;
                $absen->nama_lokasi_absensi = $terdekat->nama_lokasi;
            }
        }

        // 5. Karyawan Cuti / Izin
        $karyawanCutiQuery = Cuti::with('karyawan')
            ->whereDate('tanggal_mulai', '<=', $endDateStr)
            ->whereDate('tanggal_selesai', '>=', $startDateStr)
            ->where('status', 'approved');

        if (! empty($selectedCabang) || ! empty($selectedGroup)) {
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

        if (! empty($selectedCabang) || ! empty($selectedGroup)) {
            $absensiPulangQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiPulang = $absensiPulangQuery->pluck('karyawan_id')
            ->filter()
            ->unique()
            ->toArray();

        $karyawanBelumAbsenPulang = (clone $karyawanBaseQuery)
            ->whereIn('id', $karyawanIdsAbsen)
            ->whereNotIn('id', $absensiPulang)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 7. Absensi Luar Radius
        $absensiLuarRadiusQuery = Absensi::with('karyawan')
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

        if (! empty($selectedCabang) || ! empty($selectedGroup)) {
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

        if (! empty($selectedCabang) || ! empty($selectedGroup)) {
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
            'allCabangs',
            'selectedCabang'
        ));
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
}

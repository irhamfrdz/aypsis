<?php

namespace App\Http\Controllers;

use App\Exports\HrdAbsensiExport;
use App\Models\Absensi;
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
        $date = $request->input('tanggal_dashboard', Carbon::today()->format('Y-m-d'));
        $filterDate = Carbon::parse($date)->startOfDay();
        $selectedGroup = $request->input('grup');

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

        // Query dasar karyawan aktif (dengan filter grup jika dipilih)
        $karyawanBaseQuery = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti');

        if (!empty($selectedGroup)) {
            $karyawanBaseQuery->where(function ($q) use ($selectedGroup) {
                $q->where('grup', 'LIKE', '%"' . $selectedGroup . ':%')
                  ->orWhere('grup', 'LIKE', '%"' . $selectedGroup . '"%')
                  ->orWhere('grup', 'LIKE', '%' . $selectedGroup . '%');
            });
        }

        // 1. Total Karyawan Aktif
        $totalKaryawanAktif = (clone $karyawanBaseQuery)->count();
        $activeKaryawanIds = (clone $karyawanBaseQuery)->pluck('id')->toArray();

        // 2. Karyawan Absen Masuk Hari Ini
        $absensiMasukQuery = Absensi::with('karyawan')
            ->whereDate('waktu', $filterDate)
            ->where('tipe', 'Masuk');

        if (!empty($selectedGroup)) {
            $absensiMasukQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiMasuk = $absensiMasukQuery->get();

        $karyawanIdsAbsen = $absensiMasuk->pluck('karyawan_id')->filter()->unique()->toArray();

        // 3. Karyawan Belum Absen
        // Yaitu karyawan aktif yang id-nya belum ada di daftar absen masuk hari ini.
        $karyawanBelumAbsen = (clone $karyawanBaseQuery)
            ->whereNotIn('id', $karyawanIdsAbsen)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 4. Karyawan Absen Terlambat
        // Definisi terlambat: Jam waktu absen > 09:05:00 (Senin-Jumat), atau > 08:05:00 (Sabtu) - toleransi 5 menit
        $jamBatas = $filterDate->isSaturday() ? 8 : 9;
        $batasTerlambat = $filterDate->copy()->setHour($jamBatas)->setMinute(5)->setSecond(0);
        $karyawanTerlambat = $absensiMasuk->filter(function ($absen) use ($batasTerlambat) {
            $waktuAbsen = Carbon::parse($absen->waktu);

            return $waktuAbsen->greaterThan($batasTerlambat);
        })->values();

        // Karyawan yang hadir normal: tapping masuk sampai batas toleransi.
        // Satu karyawan hanya ditampilkan satu kali pada daftar dashboard.
        $karyawanHadirNormal = $absensiMasuk->filter(function ($absen) use ($batasTerlambat) {
            return Carbon::parse($absen->waktu)->lessThanOrEqualTo($batasTerlambat);
        })->unique('karyawan_id')->sortBy(function ($absen) {
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
            ->whereDate('tanggal_mulai', '<=', $filterDate)
            ->whereDate('tanggal_selesai', '>=', $filterDate)
            ->where('status', 'approved');

        if (!empty($selectedGroup)) {
            $karyawanCutiQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $karyawanCuti = $karyawanCutiQuery->get();

        // 6. Karyawan Belum Absen Pulang
        // Yaitu karyawan yang SUDAH absen masuk hari ini, tapi BELUM absen pulang hari ini
        $absensiPulangQuery = Absensi::whereDate('waktu', $filterDate)
            ->where('tipe', 'Pulang');

        if (!empty($selectedGroup)) {
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
            ->whereDate('waktu', $filterDate)
            ->where('detail_lokasi', 'like', '%Di luar radius%')
            ->orderBy('waktu', 'asc');

        if (!empty($selectedGroup)) {
            $absensiLuarRadiusQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $absensiLuarRadius = $absensiLuarRadiusQuery->get();

        // 8. Total presensi (Masuk + Pulang) hari ini
        $totalPresensiHariIniQuery = Absensi::whereDate('waktu', $filterDate);
        if (!empty($selectedGroup)) {
            $totalPresensiHariIniQuery->whereIn('karyawan_id', $activeKaryawanIds);
        }
        $totalPresensiHariIni = $totalPresensiHariIniQuery->count();

        return view('hrd-dashboard.index', compact(
            'filterDate',
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
            'selectedGroup'
        ));
    }

    /**
     * Export rekap absensi HRD (4 sheets).
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $fileName = 'Rekap_Absensi_HRD_'.str_replace('-', '', $startDate).'_'.str_replace('-', '', $endDate).'.xlsx';

        return Excel::download(new HrdAbsensiExport($startDate, $endDate), $fileName);
    }
}

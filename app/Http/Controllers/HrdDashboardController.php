<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HrdDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard HRD.
     */
    public function index(Request $request)
    {
        $date = $request->input('tanggal_dashboard', Carbon::today()->format('Y-m-d'));
        $filterDate = Carbon::parse($date)->startOfDay();
        
        // 1. Total Karyawan Aktif
        $totalKaryawanAktif = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti')
            ->count();

        // 2. Karyawan Absen Masuk Hari Ini
        $absensiMasuk = Absensi::with('karyawan')
            ->whereDate('waktu', $filterDate)
            ->where('tipe', 'IN')
            ->get();

        $karyawanIdsAbsen = $absensiMasuk->pluck('karyawan_id')->filter()->unique()->toArray();

        // 3. Karyawan Belum Absen
        // Yaitu karyawan aktif yang id-nya belum ada di daftar absen masuk hari ini.
        $karyawanBelumAbsen = Karyawan::where('status', 'active')
            ->whereNull('tanggal_berhenti')
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


        // 5. Karyawan Cuti / Izin
        $karyawanCuti = Cuti::with('karyawan')
            ->whereDate('tanggal_mulai', '<=', $filterDate)
            ->whereDate('tanggal_selesai', '>=', $filterDate)
            ->where('status', 'approved')
            ->get();

        return view('hrd-dashboard.index', compact(
            'filterDate',
            'jamBatas',
            'totalKaryawanAktif',
            'karyawanBelumAbsen',
            'karyawanTerlambat',
            'karyawanCuti'
        ));
    }
}

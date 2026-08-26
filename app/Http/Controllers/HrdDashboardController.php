<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HrdAbsensiExport;

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
            ->where('tipe', 'Masuk')
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

        // 6. Karyawan Belum Absen Pulang
        // Yaitu karyawan yang SUDAH absen masuk hari ini, tapi BELUM absen pulang hari ini
        $absensiPulang = Absensi::whereDate('waktu', $filterDate)
            ->where('tipe', 'Pulang')
            ->pluck('karyawan_id')
            ->filter()
            ->unique()
            ->toArray();

        $karyawanBelumAbsenPulang = Karyawan::whereIn('id', $karyawanIdsAbsen)
            ->whereNotIn('id', $absensiPulang)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('hrd-dashboard.index', compact(
            'filterDate',
            'jamBatas',
            'totalKaryawanAktif',
            'karyawanBelumAbsen',
            'karyawanTerlambat',
            'karyawanCuti',
            'karyawanBelumAbsenPulang',
            'absensiMasuk'
        ));
    }

    /**
     * Export rekap absensi HRD (4 sheets).
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $fileName = 'Rekap_Absensi_HRD_' . str_replace('-', '', $startDate) . '_' . str_replace('-', '', $endDate) . '.xlsx';

        return Excel::download(new HrdAbsensiExport($startDate, $endDate), $fileName);
    }
}

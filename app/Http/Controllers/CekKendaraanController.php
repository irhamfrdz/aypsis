<?php

namespace App\Http\Controllers;

use App\Models\CekKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CekKendaraanDailyExport;
use App\Exports\CekKendaraanWeeklyExport;
use Carbon\Carbon;

class CekKendaraanController extends Controller
{
    public function index()
    {
        $cekKendaraans = CekKendaraan::with(['mobil', 'karyawan'])
            ->latest()
            ->paginate(15);
            
        return view('cek-kendaraan.index', compact('cekKendaraans'));
    }

    public function show(CekKendaraan $cekKendaraan)
    {
        $cekKendaraan->load(['mobil', 'karyawan']);
        return view('cek-kendaraan.show', compact('cekKendaraan'));
    }

    public function dailyDashboard(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        // Use the same definition of Supir as User::isSupir()
        // Divisi is 'SUPIR' or 'DRIVER'
        $drivers = \App\Models\Karyawan::where(function($query) {
                $query->whereIn(DB::raw('UPPER(TRIM(divisi))'), ['SUPIR', 'DRIVER'])
                      ->orWhereIn(DB::raw('UPPER(TRIM(pekerjaan))'), ['SUPIR', 'DRIVER']);
            })
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get();
            
        $checksForDate = CekKendaraan::whereDate('tanggal', $date)
            ->get()
            ->groupBy('karyawan_id');
            
        return view('cek-kendaraan.daily', compact('drivers', 'checksForDate', 'date'));
    }

    public function exportDaily(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        $drivers = \App\Models\Karyawan::where(function($query) {
                $query->whereIn(DB::raw('UPPER(TRIM(divisi))'), ['SUPIR', 'DRIVER'])
                      ->orWhereIn(DB::raw('UPPER(TRIM(pekerjaan))'), ['SUPIR', 'DRIVER']);
            })
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get();
            
        $checksForDate = CekKendaraan::whereDate('tanggal', $date)
            ->get()
            ->groupBy('karyawan_id');

        $fileName = 'Laporan_Cek_Kendaraan_' . $date . '.xlsx';
        
        return Excel::download(new CekKendaraanDailyExport($drivers, $checksForDate, $date), $fileName);
    }

    public function weeklyDashboard(Request $request)
    {
        $weekStart = Carbon::parse($request->get('week_start', now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')))->startOfDay();
        // Snap to Monday if not already
        if ($weekStart->dayOfWeek !== Carbon::MONDAY) {
            $weekStart->startOfWeek(Carbon::MONDAY);
        }
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $weekDays->push($weekStart->copy()->addDays($i));
        }

        $drivers = \App\Models\Karyawan::where(function($query) {
                $query->whereIn(DB::raw('UPPER(TRIM(divisi))'), ['SUPIR', 'DRIVER'])
                      ->orWhereIn(DB::raw('UPPER(TRIM(pekerjaan))'), ['SUPIR', 'DRIVER']);
            })
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get();

        $checksForWeek = CekKendaraan::whereBetween('tanggal', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->with('mobil')
            ->get()
            ->groupBy(['karyawan_id', function ($item) {
                return $item->tanggal->format('Y-m-d');
            }]);

        return view('cek-kendaraan.weekly', compact('drivers', 'checksForWeek', 'weekStart', 'weekEnd', 'weekDays'));
    }

    public function exportWeekly(Request $request)
    {
        $weekStart = Carbon::parse($request->get('week_start', now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')))->startOfDay();
        if ($weekStart->dayOfWeek !== Carbon::MONDAY) {
            $weekStart->startOfWeek(Carbon::MONDAY);
        }
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $drivers = \App\Models\Karyawan::where(function($query) {
                $query->whereIn(DB::raw('UPPER(TRIM(divisi))'), ['SUPIR', 'DRIVER'])
                      ->orWhereIn(DB::raw('UPPER(TRIM(pekerjaan))'), ['SUPIR', 'DRIVER']);
            })
            ->whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get();

        $checksForWeek = CekKendaraan::whereBetween('tanggal', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get()
            ->groupBy(['karyawan_id', function ($item) {
                return $item->tanggal->format('Y-m-d');
            }]);

        $fileName = 'Laporan_Cek_Kendaraan_Mingguan_' . $weekStart->format('Y-m-d') . '_ke_' . $weekEnd->format('Y-m-d') . '.xlsx';

        return Excel::download(new CekKendaraanWeeklyExport($drivers, $checksForWeek, $weekStart), $fileName);
    }
}

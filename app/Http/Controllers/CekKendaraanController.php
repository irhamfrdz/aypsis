<?php

namespace App\Http\Controllers;

use App\Models\CekKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CekKendaraanDailyExport;

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
}

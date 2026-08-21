<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Cuti;
use Illuminate\Http\Request;

class HistoryCutiController extends Controller
{
    public function selectKaryawan()
    {
        $karyawanList = Karyawan::orderBy('nama', 'asc')->get();
        return view('report.history-cuti.select-karyawan', compact('karyawanList'));
    }

    public function index(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id'
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        
        $query = Cuti::where('karyawan_id', $karyawan->id)
            ->orderBy('tanggal_mulai', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mulai', [$request->start_date, $request->end_date]);
        }

        $cutis = $query->get();

        return view('report.history-cuti.index', compact('karyawan', 'cutis'));
    }
}

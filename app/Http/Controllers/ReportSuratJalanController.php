<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\ReportSuratJalanExport; // Nanti dibuat

class ReportSuratJalanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        return view('report-surat-jalan.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query Surat Jalan (Muatan)
        $querySj = SuratJalan::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        // Query Surat Jalan Bongkaran
        $querySjb = SuratJalanBongkaran::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
              ->orWhereHas('tandaTerima', function($tt) use ($startDate, $endDate) {
                  $tt->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
              })
              ->orWhereBetween('tanggal_checkpoint', [$startDate, $endDate]);
        });

        $suratJalans = $querySj->with(['tandaTerima', 'order', 'tujuanPengambilanRelation', 'supirKaryawan', 'kenekKaryawan'])->get();
        $suratJalanBongkarans = $querySjb->with(['tandaTerima', 'tujuanPengambilanRelation', 'supirKaryawan', 'kenekKaryawan'])->get();

        $data = collect();

        foreach ($suratJalans as $sj) {
            $data->push([
                'tanggal' => $sj->tanggal_surat_jalan,
                'no_surat_jalan' => $sj->no_surat_jalan,
                'no_plat' => $sj->no_plat,
                'supir' => $sj->supirKaryawan ? $sj->supirKaryawan->nama_lengkap : ($sj->supir ?: ($sj->supir2 ?: '-')),
                'kenek' => $sj->kenekKaryawan ? $sj->kenekKaryawan->nama_lengkap : ($sj->kenek ?: '-'),
                'customer' => $sj->order ? $sj->order->nama_customer : '-',
                'rute' => ($sj->pengirim ?? '-') . ' -> ' . ($sj->tujuan_pengiriman ?? '-'),
                'jenis' => 'Muat',
                'status' => $sj->status ?? 'Open', // Asumsi ada kolom status, kalau tidak ada default Open/text lain
                'original_data' => $sj
            ]);
        }

        foreach ($suratJalanBongkarans as $sjb) {
            $data->push([
                'tanggal' => $sjb->tanggal_surat_jalan,
                'no_surat_jalan' => $sjb->nomor_surat_jalan,
                'no_plat' => $sjb->no_plat,
                'supir' => $sjb->supirKaryawan ? $sjb->supirKaryawan->nama_lengkap : ($sjb->supir ?: ($sjb->supir2 ?: '-')),
                'kenek' => $sjb->kenekKaryawan ? $sjb->kenekKaryawan->nama_lengkap : ($sjb->kenek ?: '-'),
                'customer' => '-', // Bongkaran mungkin ambil dari field lain atau dikosongkan
                'rute' => ($sjb->pengirim ?? '-') . ' -> ' . ($sjb->tujuan_pengiriman ?? '-'),
                'jenis' => 'Bongkar',
                'status' => $sjb->status ?? 'Open',
                'original_data' => $sjb
            ]);
        }

        $data = $data->sortBy('tanggal');

        return view('report-surat-jalan.view', compact('data', 'startDate', 'endDate'));
    }
}

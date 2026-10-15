<?php

namespace App\Http\Controllers;

use App\Exports\ReportGajiSupirBatamExport;
use App\Models\GajiSupirBatam;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportGajiSupirBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $karyawanId = $request->get('karyawan_id', '');
        $statusPembayaran = $request->get('status_pembayaran', '');

        // Get list of Batam supir for filter dropdown
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        $query = GajiSupirBatam::with('karyawan');

        $this->applyFilters($query, $karyawanId, $statusPembayaran, $startDate, $endDate);

        $query->orderBy('tanggal_mulai', 'desc');

        $gajiList = $query->paginate(20)->appends($request->all());

        return view('report-gaji-supir-batam.index', compact(
            'gajiList',
            'supirList',
            'karyawanId',
            'statusPembayaran',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export to Excel.
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $karyawanId = $request->get('karyawan_id', '');
        $statusPembayaran = $request->get('status_pembayaran', '');

        if (! $startDate || ! $endDate) {
            return back()->with('error', 'Silakan pilih rentang tanggal terlebih dahulu untuk export.');
        }

        $query = GajiSupirBatam::with('karyawan');

        $this->applyFilters($query, $karyawanId, $statusPembayaran, $startDate, $endDate);

        $query->orderBy('tanggal_mulai', 'asc');
        $gajiList = $query->get();

        return Excel::download(
            new ReportGajiSupirBatamExport($gajiList, $startDate, $endDate),
            'Report_Gaji_Supir_Batam_'.$startDate.'_sd_'.$endDate.'.xlsx'
        );
    }

    private function applyFilters(Builder $query, $karyawanId, $statusPembayaran, $startDate, $endDate): void
    {
        if ($karyawanId !== '') {
            $query->where('karyawan_id', $karyawanId);
        }

        if ($statusPembayaran !== '') {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        // Include every salary period that intersects the selected date range.
        if ($startDate !== '') {
            $query->whereDate('tanggal_selesai', '>=', $startDate);
        }

        if ($endDate !== '') {
            $query->whereDate('tanggal_mulai', '<=', $endDate);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaUangJalan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportPranotaUangJalanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('pranota-uang-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        return view('report-pranota-uang-jalan.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('pranota-uang-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('report.pranota-uang-jalan.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $search = $request->input('search');

        $query = PranotaUangJalan::query()
            ->with(['uangJalans', 'creator', 'pembayaranPranotaUangJalans'])
            ->whereBetween('tanggal_pranota', [$startDate, $endDate]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('periode_tagihan', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $pranotas = $query->orderBy('tanggal_pranota', 'desc')->get();

        return view('report-pranota-uang-jalan.view', [
            'pranotas' => $pranotas,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search
        ]);
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $user = Auth::user();

        if (!$user->can('pranota-uang-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();
        $search = $request->input('search');

        $query = PranotaUangJalan::query()
            ->with(['uangJalans', 'creator', 'pembayaranPranotaUangJalans'])
            ->whereBetween('tanggal_pranota', [$startDate, $endDate]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('periode_tagihan', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $pranotas = $query->orderBy('tanggal_pranota', 'asc')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportPranotaUangJalanExport($pranotas, $startDate, $endDate), 
            'Report_Pranota_Uang_Jalan_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx'
        );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportLemburController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Tampilkan halaman select date
        return view('report-lembur.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Validasi required tanggal
        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('report.lembur.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query untuk Surat Jalan biasa - filter yang lembur atau nginap
        $querySuratJalan = SuratJalan::where(function($q) {
                $q->where('lembur', true)
                  ->orWhere('nginap', true);
            })
            ->whereDate('tanggal_surat_jalan', '>=', $startDate)
            ->whereDate('tanggal_surat_jalan', '<=', $endDate);

        // Query untuk Surat Jalan Bongkaran? (Need to check if bongkaran has lembur/nginap)
        // Check Model SuratJalanBongkaran first.
        
        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $querySuratJalan->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        $suratJalans = $querySuratJalan->orderBy('tanggal_surat_jalan', 'desc')->get();

        // Separate or combine logic as needed. For now just passing suratJalans.
        // I might need to check if SuratJalanBongkaran has these fields too.

        return view('report-lembur.view', compact('suratJalans', 'startDate', 'endDate'));
    }
}

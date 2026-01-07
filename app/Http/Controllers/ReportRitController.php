<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportRitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Tampilkan halaman select date
        return view('report-rit.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        // Validasi required tanggal
        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('report.rit.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = SuratJalan::whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $query->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $query->where('kegiatan', $request->kegiatan);
        }

        // Order by tanggal descending
        $query->orderBy('tanggal_surat_jalan', 'desc')->orderBy('created_at', 'desc');

        $suratJalans = $query->with(['order', 'pengirimRelation', 'jenisBarangRelation', 'tujuanPengirimanRelation'])
            ->paginate($request->get('per_page', 50))
            ->appends($request->except('page'));

        return view('report-rit.view', compact('suratJalans', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = SuratJalan::whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $query->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $query->where('kegiatan', $request->kegiatan);
        }

        $suratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')->orderBy('created_at', 'desc')->get();

        return view('report-rit.print', compact('suratJalans', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = SuratJalan::whereBetween('tanggal_surat_jalan', [$startDate, $endDate]);

        // Filter tambahan jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('supir2', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supir')) {
            $query->where(function($q) use ($request) {
                $q->where('supir', 'like', "%{$request->supir}%")
                  ->orWhere('supir2', 'like', "%{$request->supir}%");
            });
        }

        if ($request->filled('kegiatan')) {
            $query->where('kegiatan', $request->kegiatan);
        }

        $suratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')->orderBy('created_at', 'desc')->get();

        $filename = 'Report_Rit_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.xlsx';

        return \Excel::download(new \App\Exports\ReportRitExport($suratJalans, $startDate, $endDate), $filename);
    }
}

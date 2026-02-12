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
        $search = $request->input('search');

        // Query Surat Jalan (Muat)
        $suratJalanQuery = SuratJalan::query()
            ->with('tandaTerima')
            ->where(function($q) {
                $q->where('lembur', true)
                  ->orWhere('nginap', true);
            })
            ->whereHas('tandaTerima', function($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal', '>=', $startDate)
                  ->whereDate('tanggal', '<=', $endDate);
            });

        if ($search) {
            $suratJalanQuery->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        $suratJalans = $suratJalanQuery->get();

        // Query Surat Jalan Bongkaran
        $bongkaranQuery = SuratJalanBongkaran::query()
            ->with('tandaTerima')
            ->where(function($q) {
                $q->where('lembur', true)
                  ->orWhere('nginap', true);
            })
            ->whereHas('tandaTerima', function($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal_tanda_terima', '>=', $startDate)
                  ->whereDate('tanggal_tanda_terima', '<=', $endDate);
            });

        if ($search) {
            $bongkaranQuery->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        $bongkarans = $bongkaranQuery->get();

        // Standardize properties
        $suratJalans->each(function($item) {
            $item->type_surat = 'Muat';
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal : null;
        });

        $bongkarans->each(function($item) {
            $item->type_surat = 'Bongkaran';
            // Alias for view consistency
            $item->no_surat_jalan = $item->nomor_surat_jalan;
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal_tanda_terima : null;
        });

        // Merge collections
        $allSuratJalans = $suratJalans->concat($bongkarans)->sortByDesc('report_date')->values();

        return view('report-lembur.view', [
            'suratJalans' => $allSuratJalans,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}

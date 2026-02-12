<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\PranotaLembur;
use App\Models\MasterPricelistLembur;
use App\Models\NomorTerakhir;
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
        $statusPranota = $request->input('status_pranota');

        // Query Surat Jalan (Muat)
        $suratJalanQuery = SuratJalan::query()
            ->with(['tandaTerima', 'pranotaLemburs'])
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

        if ($statusPranota === 'sudah') {
            $suratJalanQuery->whereHas('pranotaLemburs');
        } elseif ($statusPranota === 'belum') {
            $suratJalanQuery->whereDoesntHave('pranotaLemburs');
        }

        $suratJalans = $suratJalanQuery->get();

        // Query Surat Jalan Bongkaran
        $bongkaranQuery = SuratJalanBongkaran::query()
            ->with(['tandaTerima', 'pranotaLemburs'])
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

        if ($statusPranota === 'sudah') {
            $bongkaranQuery->whereHas('pranotaLemburs');
        } elseif ($statusPranota === 'belum') {
            $bongkaranQuery->whereDoesntHave('pranotaLemburs');
        }

        $bongkarans = $bongkaranQuery->get();

        // Standardize properties
        $suratJalans->each(function($item) {
            $item->type_surat = 'Muat';
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal : null;
            // Check if has pranota lembur
            $item->sudah_pranota = $item->hasPranotaLembur();
        });

        $bongkarans->each(function($item) {
            $item->type_surat = 'Bongkaran';
            // Alias for view consistency
            $item->no_surat_jalan = $item->nomor_surat_jalan;
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal_tanda_terima : null;
            // Check if has pranota lembur
            $item->sudah_pranota = $item->hasPranotaLembur();
        });

        // Merge collections
        $allSuratJalans = $suratJalans->concat($bongkarans)->sortByDesc('report_date')->values();

        // Prepare data for Modal Pranota
        $pricelistLemburs = MasterPricelistLembur::where('status', 'aktif')->get();
        
        $nomorTerakhir = NomorTerakhir::where('modul', 'PML')->first();
        $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;
        $tahun = now()->format('y');
        $bulan = now()->format('m');
        $nomorCetakan = 1; // Default
        $nomorPranotaDisplay = "PML{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return view('report-lembur.view', [
            'suratJalans' => $allSuratJalans,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pricelistLemburs' => $pricelistLemburs,
            'nomorPranotaDisplay' => $nomorPranotaDisplay,
            'nextNumber' => $nextNumber,
            'statusPranota' => $statusPranota
        ]);
    }
}

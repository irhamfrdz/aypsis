<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use App\Models\Mobil;
use App\Models\TireInstallationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RiwayatPasangBanController extends Controller
{
    /**
     * Tampilkan riwayat pemasangan dan pelepasan ban.
     */
    public function index(Request $request)
    {
        $query = TireInstallationLog::with([
            'mobil',
            'alatBerat',
            'stockBan.namaStockBan',
            'stockBan.merkBan',
        ]);

        // Filter: Kategori (mobil / alat_berat)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter: Mobil ID
        if ($request->filled('mobil_id')) {
            $query->where('mobil_id', $request->mobil_id);
        }

        // Filter: Alat Berat ID
        if ($request->filled('alat_berat_id')) {
            $query->where('alat_berat_id', $request->alat_berat_id);
        }

        // Filter: Aksi (pasang, copot, tukar, dll)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter: Status Pinjaman
        if ($request->filled('is_borrowed')) {
            $query->where('is_borrowed', $request->is_borrowed == '1');
        }

        // Filter: Rentang Tanggal
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Filter: Pencarian Keyword (Nomor Seri, Posisi Roda, Catatan, Nama Unit Donor)
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_seri', 'like', "%{$keyword}%")
                    ->orWhere('wheel_code', 'like', "%{$keyword}%")
                    ->orWhere('wheel_id', 'like', "%{$keyword}%")
                    ->orWhere('donor_unit_name', 'like', "%{$keyword}%")
                    ->orWhere('notes', 'like', "%{$keyword}%")
                    ->orWhereHas('mobil', function ($qMobil) use ($keyword) {
                        $qMobil->where('nomor_polisi', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('alatBerat', function ($qAlat) use ($keyword) {
                        $qAlat->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('kode_alat', 'like', "%{$keyword}%");
                    });
            });
        }

        // Hitung statistik untuk badge ringkasan
        $statsQuery = clone $query;
        $totalLogs = (clone $statsQuery)->count();
        $totalPasang = (clone $statsQuery)->where('action', 'pasang')->count();
        $totalCopot = (clone $statsQuery)->where('action', 'copot')->count();
        $totalPinjaman = (clone $statsQuery)->where('is_borrowed', true)->count();

        // Urutkan dan paginasi
        $logs = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        $mobils = Mobil::orderBy('nomor_polisi')->get(['id', 'nomor_polisi']);
        $alatBerats = AlatBerat::orderBy('nama')->get(['id', 'nama', 'kode_alat']);

        return view('riwayat-pasang-ban.index', compact(
            'logs',
            'mobils',
            'alatBerats',
            'totalLogs',
            'totalPasang',
            'totalCopot',
            'totalPinjaman'
        ));
    }
}

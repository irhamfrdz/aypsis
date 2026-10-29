<?php

namespace App\Http\Controllers;

use App\Models\PembayaranAktivitasLain;
use Illuminate\Http\Request;

class ReportBiayaPerijinanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = PembayaranAktivitasLain::query()
            ->where('jenis_aktivitas', 'Pembayaran Kendaraan')
            ->where('sub_jenis_kendaraan', 'KIR');

        if (! empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('nomor', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%")
                    ->orWhere('nomor_polisi', 'like', "%{$search}%")
                    ->orWhere('kendaraan_details', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $summary = (clone $query)->selectRaw('COUNT(*) AS total_transaksi, COALESCE(SUM(jumlah), 0) AS total_biaya, COALESCE(SUM(pph_nominal), 0) AS total_pph, COALESCE(SUM(COALESCE(grand_total, jumlah - COALESCE(pph_nominal, 0))), 0) AS total_dibayar')->first();
        $pembayarans = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('report.biaya-perijinan.index', compact('pembayarans', 'summary'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BiayaBensin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportBiayaBensinController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $filters = [
            'tanggal_dari' => $validated['tanggal_dari'] ?? now()->startOfMonth()->toDateString(),
            'tanggal_sampai' => $validated['tanggal_sampai'] ?? now()->endOfMonth()->toDateString(),
            'status' => $validated['status'] ?? '',
            'search' => trim($validated['search'] ?? ''),
        ];

        $query = BiayaBensin::query()
            ->whereDate('tanggal', '>=', $filters['tanggal_dari'])
            ->whereDate('tanggal', '<=', $filters['tanggal_sampai']);

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('nomor_kartu', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%')
                    ->orWhereHas('mobil', fn ($mobil) => $mobil->where('nomor_polisi', 'like', '%'.$search.'%'))
                    ->orWhereHas('alatBerat', fn ($alatBerat) => $alatBerat->where('nama', 'like', '%'.$search.'%'))
                    ->orWhereHas('supir', fn ($supir) => $supir->where('nama_panggilan', 'like', '%'.$search.'%')
                        ->orWhere('nama_lengkap', 'like', '%'.$search.'%'));
            });
        }

        $summary = (clone $query)->selectRaw('COUNT(*) AS jumlah_transaksi, COALESCE(SUM(liter), 0) AS total_liter, COALESCE(SUM(biaya), 0) AS total_biaya')->first();
        $items = $query->with(['mobil', 'alatBerat', 'supir', 'pranotaBiayaBensin'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('report.biaya-bensin.index', compact('filters', 'summary', 'items'));
    }
}

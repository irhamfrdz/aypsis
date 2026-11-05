<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use Illuminate\Http\Request;

class RekapPemakaianBanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = StockBan::query()
            ->with(['namaStockBan', 'penerima', 'mobil', 'alatBerat', 'kapal'])
            ->where(function ($query) {
                $query->whereNotNull('tanggal_digunakan')->orWhere('status', 'Terpakai');
            });

        foreach (['start_date' => '>=', 'end_date' => '<='] as $field => $operator) {
            if (! empty($filters[$field])) {
                $query->whereRaw(
                    "COALESCE(tanggal_digunakan, tanggal_keluar, DATE(updated_at)) {$operator} ?",
                    [$filters[$field]]
                );
            }
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('nomor_seri', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('ukuran', 'like', "%{$search}%")
                    ->orWhere('nomor_bukti_pakai', 'like', "%{$search}%")
                    ->orWhereHas('namaStockBan', fn ($query) => $query->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('mobil', fn ($query) => $query->where('nomor_polisi', 'like', "%{$search}%")
                        ->orWhere('no_kir', 'like', "%{$search}%"))
                    ->orWhereHas('alatBerat', fn ($query) => $query->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('kapal', fn ($query) => $query->where('nama_kapal', 'like', "%{$search}%"));
            });
        }

        $total = (clone $query)->count();
        $items = $query->orderByRaw('COALESCE(tanggal_digunakan, tanggal_keluar, DATE(updated_at)) DESC')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('rekap.pemakaian-ban', compact('items', 'total'));
    }
}

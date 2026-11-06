<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use Illuminate\Http\Request;

class RekapPemakaianBanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'required_with:end_date,unit', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'required_with:start_date,unit', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'unit' => ['nullable', 'integer', 'min:1'],
            'print' => ['nullable', 'in:1'],
        ]);
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $unit = $filters['unit'] ?? null;
        $units = collect();
        $items = null;

        if ($startDate && $endDate) {
            $query = StockBan::query()->whereHas('mobil')
                ->where(fn ($q) => $q->whereNotNull('tanggal_digunakan')->orWhere('status', 'Terpakai'))
                ->whereRaw('COALESCE(tanggal_digunakan, tanggal_keluar, DATE(updated_at)) >= ?', [$startDate])
                ->whereRaw('COALESCE(tanggal_digunakan, tanggal_keluar, DATE(updated_at)) <= ?', [$endDate]);
            $units = (clone $query)->with('mobil')->selectRaw('mobil_id, COUNT(*) as total')
                ->groupBy('mobil_id')->get()->sortBy(fn ($row) => $row->mobil?->nomor_polisi ?: $row->mobil?->no_kir)->values();
            if ($unit) {
                $detailQuery = (clone $query)->with(['namaStockBan', 'penerima', 'mobil'])->where('mobil_id', $unit)
                    ->orderByRaw('COALESCE(tanggal_digunakan, tanggal_keluar, DATE(updated_at)) DESC')
                    ->orderByDesc('id');
                if (($filters['print'] ?? null) === '1') {
                    $items = $detailQuery->get();
                    $selectedUnit = $units->firstWhere('mobil_id', $unit)?->mobil;

                    return view('rekap.pemakaian-ban-print', compact('items', 'startDate', 'endDate', 'selectedUnit'));
                }
                $items = $detailQuery->paginate(25)->withQueryString();
            }
        }

        return view('rekap.pemakaian-ban', compact('items', 'units', 'startDate', 'endDate', 'unit'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\StockBanUsageHistory;
use App\Models\StockBanDalamUsage;
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
        $ringItems = null;

        if ($startDate && $endDate) {
            $query = StockBanUsageHistory::query()->whereNotNull('mobil_id')
                ->whereBetween('tanggal_digunakan', [$startDate, $endDate]);
            $units = (clone $query)->with('mobil')->selectRaw('mobil_id, MAX(unit_name) as unit_name, COUNT(*) as total')
                ->groupBy('mobil_id')->get()->keyBy('mobil_id');
            $ringQuery = StockBanDalamUsage::query()->whereHas('mobil')
                ->whereNull('stock_ban_dalam_id')
                ->where('keterangan', 'like', '[Ring Velg ID: %')
                ->whereDate('tanggal_keluar', '>=', $startDate)
                ->whereDate('tanggal_keluar', '<=', $endDate);
            $ringUsages = (clone $ringQuery)->with('mobil')->selectRaw('mobil_id, COUNT(*) as ring_total')
                ->groupBy('mobil_id')->get();
            foreach ($ringUsages as $ringUsage) {
                if ($units->has($ringUsage->mobil_id)) {
                    $units->get($ringUsage->mobil_id)->ring_total = $ringUsage->ring_total;
                } else {
                    $ringUsage->total = 0;
                    $units->put($ringUsage->mobil_id, $ringUsage);
                }
            }
            $units = $units->sortBy(fn ($row) => $row->mobil?->nomor_polisi ?: ($row->unit_name ?: $row->mobil?->no_kir))->values();
            if ($unit) {
                $detailQuery = (clone $query)->with('mobil')->where('mobil_id', $unit)
                    ->orderByDesc('tanggal_digunakan')
                    ->orderByDesc('id');
                if (($filters['print'] ?? null) === '1') {
                    $items = $detailQuery->get();
                    $selectedUnit = $units->firstWhere('mobil_id', $unit)?->mobil;
                    $selectedUnitName = $units->firstWhere('mobil_id', $unit)?->unit_name;

                    return view('rekap.pemakaian-ban-print', compact('items', 'startDate', 'endDate', 'selectedUnit', 'selectedUnitName'));
                }
                $items = $detailQuery->paginate(25)->withQueryString();
                $ringItems = (clone $ringQuery)->with('penerima')->where('mobil_id', $unit)
                    ->orderByDesc('tanggal_keluar')->orderByDesc('id')
                    ->paginate(25, ['*'], 'ring_page')->withQueryString();
            }
        }

        return view('rekap.pemakaian-ban', compact('items', 'ringItems', 'units', 'startDate', 'endDate', 'unit'));
    }
}

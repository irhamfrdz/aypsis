<?php

namespace App\Http\Controllers;

use App\Models\StockBanUsageHistory;
use App\Models\StockBanDalamUsage;
use App\Models\StockVelg;
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
        $banDalamItems = null;

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
            $banDalamQuery = StockBanDalamUsage::query()->whereHas('mobil')
                ->join('stock_ban_dalams', 'stock_ban_dalams.id', '=', 'stock_ban_dalam_usages.stock_ban_dalam_id')
                ->join('nama_stock_bans', 'nama_stock_bans.id', '=', 'stock_ban_dalams.nama_stock_ban_id')
                ->whereRaw('LOWER(nama_stock_bans.nama) like ?', ['%ban dalam%'])
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '>=', $startDate)
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '<=', $endDate);
            $banDalamUsages = (clone $banDalamQuery)->with('mobil')->selectRaw('stock_ban_dalam_usages.mobil_id, COUNT(*) as ban_dalam_total')
                ->groupBy('stock_ban_dalam_usages.mobil_id')->get();
            foreach ($banDalamUsages as $banDalamUsage) {
                if ($units->has($banDalamUsage->mobil_id)) {
                    $units->get($banDalamUsage->mobil_id)->ban_dalam_total = $banDalamUsage->ban_dalam_total;
                } else {
                    $banDalamUsage->total = 0;
                    $units->put($banDalamUsage->mobil_id, $banDalamUsage);
                }
            }
            $banPerutUsages = StockBanDalamUsage::query()->whereHas('mobil')
                ->join('stock_ban_dalams', 'stock_ban_dalams.id', '=', 'stock_ban_dalam_usages.stock_ban_dalam_id')
                ->join('nama_stock_bans', 'nama_stock_bans.id', '=', 'stock_ban_dalams.nama_stock_ban_id')
                ->whereRaw('LOWER(nama_stock_bans.nama) like ?', ['%ban perut%'])
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '>=', $startDate)
                ->whereDate('stock_ban_dalam_usages.tanggal_keluar', '<=', $endDate)
                ->with('mobil')->selectRaw('stock_ban_dalam_usages.mobil_id, COUNT(*) as ban_perut_total')
                ->groupBy('stock_ban_dalam_usages.mobil_id')->get();
            foreach ($banPerutUsages as $banPerutUsage) {
                if ($units->has($banPerutUsage->mobil_id)) {
                    $units->get($banPerutUsage->mobil_id)->ban_perut_total = $banPerutUsage->ban_perut_total;
                } else {
                    $banPerutUsage->total = 0;
                    $units->put($banPerutUsage->mobil_id, $banPerutUsage);
                }
            }
            // Velg usage stores its stock ID in the history description.
            $velgs = StockVelg::withTrashed()->whereIn('ukuran', ['8', '10'])->pluck('ukuran', 'id');
            $velgUsages = StockBanDalamUsage::query()->whereHas('mobil')
                ->whereNull('stock_ban_dalam_id')
                ->where('keterangan', 'like', '[Velg ID: %')
                ->whereDate('tanggal_keluar', '>=', $startDate)
                ->whereDate('tanggal_keluar', '<=', $endDate)
                ->with('mobil')->get(['id', 'mobil_id', 'keterangan']);
            foreach ($velgUsages as $usage) {
                if (! preg_match('/^\[Velg ID: (\d+)\]/', $usage->keterangan, $matches)) {
                    continue;
                }
                $ukuran = $velgs->get((int) $matches[1]);
                if (! in_array((string) $ukuran, ['8', '10'], true)) {
                    continue;
                }
                if (! $units->has($usage->mobil_id)) {
                    $usage->total = 0;
                    $units->put($usage->mobil_id, $usage);
                }
                $vehicle = $units->get($usage->mobil_id);
                $field = 'velg_'.$ukuran.'_total';
                $vehicle->$field = ($vehicle->$field ?? 0) + 1;
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
                $banDalamItems = (clone $banDalamQuery)->with('penerima')
                    ->where('stock_ban_dalam_usages.mobil_id', $unit)
                    ->select('stock_ban_dalam_usages.*', 'nama_stock_bans.nama as nama_barang')
                    ->orderByDesc('stock_ban_dalam_usages.tanggal_keluar')->orderByDesc('stock_ban_dalam_usages.id')
                    ->paginate(25, ['*'], 'ban_dalam_page')->withQueryString();
            }
        }

        return view('rekap.pemakaian-ban', compact('items', 'ringItems', 'banDalamItems', 'units', 'startDate', 'endDate', 'unit'));
    }
}

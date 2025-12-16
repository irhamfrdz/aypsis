<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PranotaOb;
use App\Models\NaikKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PranotaObController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('pranota-ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota OB.');
        }

        $query = PranotaOb::with('creator', 'itemsPivot');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_pranota', 'like', "%{$s}%")
                  ->orWhere('nama_kapal', 'like', "%{$s}%")
                  ->orWhere('no_voyage', 'like', "%{$s}%")
                  ->orWhereJsonContains('items', ['nomor_kontainer' => $s]);
            });
        }

        $pranotas = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        $stats = [
            'total' => PranotaOb::count(),
            'this_month' => PranotaOb::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('pranota-ob.index', compact('pranotas', 'stats'));
    }

    public function show($id)
    {
        $pranota = PranotaOb::findOrFail($id);
        $user = Auth::user();
        if (!$user || !$user->can('pranota-ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota OB.');
        }

        // Use pivot items where available
        $pranota->loadMissing('itemsPivot', 'creator');
        $enrichedItems = $pranota->getEnrichedItems();

        // If enrichment yielded nothing, keep original items as fallback
        $displayItems = $enrichedItems;
        if (empty($displayItems) && is_array($pranota->items) && count($pranota->items)) {
            $displayItems = $pranota->items;
        }

        // Log for debugging: what contents are present
        \Log::info('PranotaOb show - id:' . $pranota->id . ' nomor:' . $pranota->nomor_pranota . ' nama_kapal:' . $pranota->nama_kapal . ' no_voyage:' . $pranota->no_voyage . ' items:' . json_encode($pranota->items) . ' enriched:' . json_encode($enrichedItems));

        return view('pranota-ob.show', compact('pranota', 'displayItems'));
    }

    public function print($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('pranota-ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak pranota OB.');
        }

        $pranota = PranotaOb::with('creator', 'itemsPivot')->findOrFail($id);
        $enrichedItems = $pranota->getEnrichedItems();

        $displayItems = $enrichedItems;
        if (empty($displayItems) && is_array($pranota->items) && count($pranota->items)) {
            $displayItems = $pranota->items;
        }

        // Summaries: total biaya and group by driver (supir) + counts for full/empty per size
        $totalBiaya = 0;
        $perSupir = []; // total biaya per supir
        $perSupirCounts = []; // per supir counts for status and size
        foreach ($displayItems as $item) {
            $amount = (float)($item['biaya'] ?? 0);
            $totalBiaya += $amount;
            $key = trim($item['supir'] ?? 'Perusahaan');
            if (!isset($perSupir[$key])) $perSupir[$key] = 0;
            $perSupir[$key] += $amount;

            if (!isset($perSupirCounts[$key])) {
                $perSupirCounts[$key] = [
                    'full' => 0,
                    'empty' => 0,
                    'sizes' => [],
                ];
            }

            // Detect status: prefer explicit field, otherwise check type + DB if available
            $status = 'full';
            if (isset($item['status']) && in_array($item['status'], ['full','empty'])) {
                $status = $item['status'];
            } elseif (isset($item['type']) && isset($item['id'])) {
                try {
                    if ($item['type'] === 'bl') {
                        $bl = \DB::table('bls')->find($item['id']);
                        if ($bl) {
                            $name = $bl->nama_barang ?? '';
                            $lowerName = strtolower($name);
                            if (empty($name) || str_contains($lowerName, 'empty') || str_contains($lowerName, 'kosong')) $status = 'empty';
                            else $status = 'full';
                        }
                    } elseif ($item['type'] === 'naik_kapal') {
                        $nk = \DB::table('naik_kapal')->find($item['id']);
                        if ($nk) {
                            $name = $nk->jenis_barang ?? ($nk->nama_barang ?? '');
                            $lowerName = strtolower($name);
                            if (empty($name) || str_contains($lowerName, 'empty') || str_contains($lowerName, 'kosong')) $status = 'empty';
                            else $status = 'full';
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore and rely on fallback logic
                }
            } else {
                $name = $item['nama_barang'] ?? '';
                $lowerName = strtolower($name);
                if (empty($name) || str_contains($lowerName, 'empty') || str_contains($lowerName, 'kosong')) $status = 'empty';
                else $status = 'full';
            }

            $size = (string)($item['size'] ?? $item['size_kontainer'] ?? 'unknown');
            if ($size === '') $size = 'unknown';
            // Normalize size to 20, 40 or other
            $sizeKey = 'other';
            $lowerSize = strtolower($size);
            if (str_contains($lowerSize, '40')) $sizeKey = '40';
            elseif (str_contains($lowerSize, '20')) $sizeKey = '20';
            elseif (is_numeric($size) && intval($size) === 40) $sizeKey = '40';
            elseif (is_numeric($size) && intval($size) === 20) $sizeKey = '20';

            $perSupirCounts[$key][$status]++;
            if (!isset($perSupirCounts[$key]['sizes'][$sizeKey])) {
                $perSupirCounts[$key]['sizes'][$sizeKey] = ['full' => 0, 'empty' => 0];
            }
            $perSupirCounts[$key]['sizes'][$sizeKey][$status]++;
        }

        return view('pranota-ob.print', compact('pranota', 'displayItems', 'totalBiaya', 'perSupir', 'perSupirCounts'));
    }

    public function inputDp($id)
    {
        $pranota = PranotaOb::with(['creator', 'itemsPivot'])->findOrFail($id);
        $user = Auth::user();
        if (!$user || !$user->can('pranota-ob-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota OB.');
        }

        // Get pembayaran DP data from pembayaran_obs table
        $pembayaranDps = \DB::table('pembayaran_obs')
            ->select('id', 'nomor_pembayaran', 'tanggal_pembayaran', 'supir_ids', 'dp_amount', 'jumlah_per_supir', 'keterangan', 'kas_bank_akun_id')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        // Get unique supir list from pranota items
        $displayItems = $pranota->itemsPivot && $pranota->itemsPivot->count() > 0 
            ? $pranota->itemsPivot->map(function($pivot) {
                return json_decode($pivot->item_data, true);
            })->toArray()
            : (is_array($pranota->items) ? $pranota->items : []);

        $supirList = [];
        foreach ($displayItems as $item) {
            $supir = $item['supir'] ?? null;
            if ($supir && !in_array($supir, $supirList)) {
                $supirList[] = $supir;
            }
        }

        return view('pranota-ob.input-dp', compact('pranota', 'pembayaranDps', 'supirList'));
    }
}

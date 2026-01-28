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

        // Build reverse map from pricelist: biaya|size => status
        // Use integer biaya as key to match with item biaya
        $pricelists = \App\Models\MasterPricelistOb::all();
        $reverseMap = [];
        foreach ($pricelists as $pl) {
            // Normalize size - remove 'ft' suffix if present, then add it back
            $sizeRaw = preg_replace('/ft$/i', '', $pl->size_kontainer);
            $sizeStr = $sizeRaw . 'ft';
            $biaya = (int) $pl->biaya; // Convert to integer for consistent comparison
            $key = $biaya . '|' . $sizeStr;
            $reverseMap[$key] = strtolower($pl->status_kontainer);
            \Log::info("Print pricelist map: $key => " . $pl->status_kontainer);
        }

        // Summaries: total biaya and group by driver (supir) + counts for full/empty per size
        $totalBiaya = 0;
        $perSupir = []; // total biaya per supir
        $perSupirCounts = []; // per supir counts for status and size
        $totalTlContainers = 0; // count TL containers
        foreach ($displayItems as $item) {
            // Check if container is TL - check both is_tl flag and biaya === null
            $isTl = ($item['is_tl'] ?? false) === 1 || 
                    ($item['is_tl'] ?? false) === true || 
                    ($item['is_tl'] ?? false) === '1' ||
                    (($item['biaya'] ?? 0) === null || ($item['biaya'] ?? 0) === 0);
            if ($isTl) {
                $totalTlContainers++;
            }
            
            $amount = (float)($item['biaya'] ?? 0);
            $totalBiaya += $amount;
            
            $supirName = trim($item['supir'] ?? '');
            if (($supirName === '' || strtolower($supirName) === 'perusahaan') && $isTl) {
                $supirName = 'TL';
            }
            if ($supirName === '') {
                $supirName = 'Perusahaan';
            }
            $key = $supirName;

            if (!isset($perSupir[$key])) $perSupir[$key] = 0;
            $perSupir[$key] += $amount;

            if (!isset($perSupirCounts[$key])) {
                $perSupirCounts[$key] = [
                    'full' => 0,
                    'empty' => 0,
                    'sizes' => [],
                ];
            }

            // PERBAIKAN: Tentukan status dari biaya yang tersimpan, bukan dari field status yang mungkin salah
            $status = 'full'; // default
            
            // Normalize size first
            $size = (string)($item['size'] ?? $item['size_kontainer'] ?? 'unknown');
            if ($size === '') $size = 'unknown';
            $sizeKey = 'other';
            $lowerSize = strtolower($size);
            if (str_contains($lowerSize, '40')) $sizeKey = '40';
            elseif (str_contains($lowerSize, '20')) $sizeKey = '20';
            elseif (is_numeric($size) && intval($size) === 40) $sizeKey = '40';
            elseif (is_numeric($size) && intval($size) === 20) $sizeKey = '20';
            
            // Convert size to pricelist format
            $sizeStr = null;
            if ($sizeKey === '20') $sizeStr = '20ft';
            elseif ($sizeKey === '40') $sizeStr = '40ft';
            
            // Detect status from biaya using reverse map
            $biayaInt = (int) $amount; // Convert to integer for consistent comparison
            if ($biayaInt > 0 && $sizeStr) {
                $reverseKey = $biayaInt . '|' . $sizeStr;
                \Log::info("Print lookup: biaya=$biayaInt, size=$sizeStr, key=$reverseKey, found=" . ($reverseMap[$reverseKey] ?? 'NOT FOUND'));
                if (isset($reverseMap[$reverseKey])) {
                    // Status ditentukan dari pricelist yang cocok dengan biaya
                    $status = $reverseMap[$reverseKey];
                } else {
                    // Biaya tidak cocok dengan pricelist, fallback ke status tersimpan atau nama_barang
                    if (isset($item['status']) && in_array(strtolower($item['status']), ['full','empty'])) {
                        $status = strtolower($item['status']);
                    } else {
                        $name = $item['nama_barang'] ?? '';
                        $lowerName = strtolower($name);
                        if (empty($name) || str_contains($lowerName, 'empty') || str_contains($lowerName, 'kosong')) {
                            $status = 'empty';
                        }
                    }
                }
            } else {
                // Tidak ada biaya atau size, fallback ke status tersimpan atau nama_barang
                if (isset($item['status']) && in_array(strtolower($item['status']), ['full','empty'])) {
                    $status = strtolower($item['status']);
                } else {
                    $name = $item['nama_barang'] ?? '';
                    $lowerName = strtolower($name);
                    if (empty($name) || str_contains($lowerName, 'empty') || str_contains($lowerName, 'kosong')) {
                        $status = 'empty';
                    }
                }
            }

            $perSupirCounts[$key][$status]++;
            if (!isset($perSupirCounts[$key]['sizes'][$sizeKey])) {
                $perSupirCounts[$key]['sizes'][$sizeKey] = ['full' => 0, 'empty' => 0, 'biaya' => 0];
            }
            $perSupirCounts[$key]['sizes'][$sizeKey][$status]++;
            $perSupirCounts[$key]['sizes'][$sizeKey]['biaya'] += $amount;
        }

        // Calculate total biaya per size (across all supirs)
        $biayaPerSize = [
            '20' => 0,
            '40' => 0,
            'other' => 0
        ];
        foreach ($perSupirCounts as $counts) {
            foreach ($counts['sizes'] as $size => $data) {
                if (isset($biayaPerSize[$size])) {
                    $biayaPerSize[$size] += $data['biaya'] ?? 0;
                }
            }
        }

        // Pass pranotaItems for print view
        $pranotaItems = $displayItems;

        return view('pranota-ob.print', compact('pranota', 'displayItems', 'totalBiaya', 'perSupir', 'perSupirCounts', 'pranotaItems', 'biayaPerSize', 'totalTlContainers'));
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
            ->select('id', 'nomor_pembayaran', 'tanggal_pembayaran', 'supir_ids', 'total_pembayaran', 'jumlah_per_supir', 'keterangan', 'kas_bank_id')
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

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('pranota-ob-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pranota OB.');
        }

        $pranota = PranotaOb::findOrFail($id);
        
        try {
            // Delete related pivot items first
            if ($pranota->itemsPivot) {
                $pranota->itemsPivot()->delete();
            }
            
            // Delete the pranota
            $pranota->delete();
            
            return redirect()->route('pranota-ob.index')
                ->with('success', 'Pranota OB berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pranota-ob.index')
                ->with('error', 'Gagal menghapus pranota OB: ' . $e->getMessage());
        }
    }
}

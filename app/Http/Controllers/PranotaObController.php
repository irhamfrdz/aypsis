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
}

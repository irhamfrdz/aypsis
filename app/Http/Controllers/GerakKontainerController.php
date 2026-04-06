<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\Gudang;
use App\Models\HistoryKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GerakKontainerController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('gerak-kontainer.index', compact('gudangs'));
    }

    public function search(Request $request)
    {
        $term = $request->term;
        if (empty($term)) return response()->json([]);
        
        $results = [];

        // Search in StockKontainer
        $stockResults = StockKontainer::with('gudang:id,nama_gudang,lokasi')
            ->select('id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'gudangs_id')
            ->where('nomor_seri_gabungan', 'like', "%{$term}%")
            ->where('status', '!=', 'inactive')
            ->limit(10)
            ->get();
            
        foreach ($stockResults as $item) {
            $asalStr = $item->gudang ? $item->gudang->nama_gudang . " (" . $item->gudang->lokasi . ")" : "Belum ada lokasi";
            $results[] = [
                'id' => 'stock_' . $item->id,
                'text' => $item->nomor_seri_gabungan . " ({$item->ukuran}' {$item->tipe_kontainer}) - [Asal: {$asalStr}]",
                'nomor' => $item->nomor_seri_gabungan,
                'tipe' => $item->tipe_kontainer,
                'asal_name' => $asalStr,
                'asal_id' => $item->gudangs_id
            ];
        }

        // Search in Kontainer
        $kontainerResults = Kontainer::with('gudang:id,nama_gudang,lokasi')
            ->select('id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'gudangs_id')
            ->where('nomor_seri_gabungan', 'like', "%{$term}%")
            ->where('status', '!=', 'inactive')
            ->limit(10)
            ->get();
            
        foreach ($kontainerResults as $item) {
            $asalStr = $item->gudang ? $item->gudang->nama_gudang . " (" . $item->gudang->lokasi . ")" : "Belum ada lokasi";
            $results[] = [
                'id' => 'kontainer_' . $item->id,
                'text' => $item->nomor_seri_gabungan . " ({$item->ukuran}' {$item->tipe_kontainer}) - [Asal: {$asalStr}]",
                'nomor' => $item->nomor_seri_gabungan,
                'tipe' => $item->tipe_kontainer,
                'asal_name' => $asalStr,
                'asal_id' => $item->gudangs_id
            ];
        }
        
        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kontainer_id' => 'required',
            'gudang_id' => 'required|exists:gudangs,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500'
        ]);

        $parts = explode('_', $request->kontainer_id);
        if (count($parts) < 2) return back()->with('error', 'Format kontainer tidak valid.');

        $source = $parts[0];
        $id = $parts[1];
        
        $nomor = '';
        $tipe = '';
        $asal_id = null;

        if ($source == 'stock') {
            $item = StockKontainer::findOrFail($id);
            $asal_id = $item->gudangs_id;
            $item->gudangs_id = $request->gudang_id;
            $item->save();
            $nomor = $item->nomor_seri_gabungan;
            $tipe = $item->tipe_kontainer;
        } else {
            $item = Kontainer::findOrFail($id);
            $asal_id = $item->gudangs_id;
            $item->gudangs_id = $request->gudang_id;
            $item->save();
            $nomor = $item->nomor_seri_gabungan;
            $tipe = $item->tipe_kontainer;
        }

        // Log to HistoryKontainer
        HistoryKontainer::create([
            'nomor_kontainer' => $nomor,
            'tipe_kontainer' => $tipe,
            'jenis_kegiatan' => 'Pindahan Gudang',
            'tanggal_kegiatan' => $request->tanggal,
            'asal_gudang_id' => $asal_id,
            'gudang_id' => $request->gudang_id,
            'keterangan' => $request->keterangan ?? 'Pemindahan dari ' . ($request->asal_name ?? 'gudang lama') . ' ke tujuan baru',
            'created_by' => Auth::id()
        ]);

        return redirect()->route('gerak-kontainer.index')->with('success', "Kontainer {$nomor} berhasil dipindahkan.");
    }
}

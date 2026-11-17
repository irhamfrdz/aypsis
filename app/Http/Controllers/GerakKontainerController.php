<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        if (empty($term)) {
            return response()->json([]);
        }

        $results = [];

        // Search in StockKontainer
        $stockResults = StockKontainer::with('gudang:id,nama_gudang,lokasi')
            ->select('id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'gudangs_id')
            ->where('nomor_seri_gabungan', 'like', "%{$term}%")
            ->where('status', '!=', 'inactive')
            ->limit(10)
            ->get();

        foreach ($stockResults as $item) {
            $asalStr = $item->gudang ? $item->gudang->nama_gudang.' ('.$item->gudang->lokasi.')' : 'Belum ada lokasi';
            $results[] = [
                'id' => 'stock_'.$item->id,
                'text' => $item->nomor_seri_gabungan." ({$item->ukuran}' {$item->tipe_kontainer}) - [Asal: {$asalStr}]",
                'nomor' => $item->nomor_seri_gabungan,
                'tipe' => $item->tipe_kontainer,
                'asal_name' => $asalStr,
                'asal_id' => $item->gudangs_id,
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
            $asalStr = $item->gudang ? $item->gudang->nama_gudang.' ('.$item->gudang->lokasi.')' : 'Belum ada lokasi';
            $results[] = [
                'id' => 'kontainer_'.$item->id,
                'text' => $item->nomor_seri_gabungan." ({$item->ukuran}' {$item->tipe_kontainer}) - [Asal: {$asalStr}]",
                'nomor' => $item->nomor_seri_gabungan,
                'tipe' => $item->tipe_kontainer,
                'asal_name' => $asalStr,
                'asal_id' => $item->gudangs_id,
            ];
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kontainer_ids' => ['required', 'array', 'min:1', 'max:100'],
            'kontainer_ids.*' => ['required', 'string', 'distinct', 'regex:/^(stock|kontainer)_[0-9]+$/'],
            'gudang_id' => 'required|exists:gudangs,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $count = DB::transaction(function () use ($data) {
            $movedNumbers = [];
            foreach ($data['kontainer_ids'] as $selectedId) {
                [$source, $id] = explode('_', $selectedId, 2);
                $model = $source === 'stock' ? StockKontainer::class : Kontainer::class;
                $item = $model::query()->lockForUpdate()->find($id);
                if (! $item) {
                    throw ValidationException::withMessages(['kontainer_ids' => "Kontainer {$selectedId} tidak ditemukan."]);
                }

                $nomor = $item->nomor_seri_gabungan;
                if (isset($movedNumbers[$nomor])) {
                    continue;
                }
                $movedNumbers[$nomor] = true;
                $asalId = $item->gudangs_id;
                $item->gudangs_id = $data['gudang_id'];
                $item->save();

                HistoryKontainer::create([
                    'nomor_kontainer' => $nomor,
                    'tipe_kontainer' => $item->tipe_kontainer,
                    'jenis_kegiatan' => 'Pindahan Gudang',
                    'tanggal_kegiatan' => $data['tanggal'],
                    'asal_gudang_id' => $asalId,
                    'gudang_id' => $data['gudang_id'],
                    'keterangan' => ($data['keterangan'] ?? null) ?: 'Pemindahan dari '.(Gudang::find($asalId)?->nama_gudang ?? 'gudang lama').' ke tujuan baru',
                    'created_by' => Auth::id(),
                ]);
            }

            return count($movedNumbers);
        });

        return redirect()->route('gerak-kontainer.index')->with('success', "{$count} kontainer berhasil dipindahkan.");
    }
}

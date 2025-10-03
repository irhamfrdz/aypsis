<?php

namespace App\Http\Controllers;

use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockKontainer::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        // Search berdasarkan nomor kontainer
        if ($request->filled('search')) {
            $query->where('nomor_kontainer', 'like', '%' . $request->search . '%');
        }

        $stockKontainers = $query->latest()->paginate(15);

        return view('master-stock-kontainer.index', compact('stockKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-stock-kontainer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kontainer' => 'required|string|unique:stock_kontainers,nomor_kontainer',
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged',
            'lokasi' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'nomor_seri' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        StockKontainer::create($request->all());

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.show', compact('stockKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.edit', compact('stockKontainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockKontainer $stockKontainer)
    {
        $request->validate([
            'nomor_kontainer' => ['required', 'string', Rule::unique('stock_kontainers')->ignore($stockKontainer->id)],
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged',
            'lokasi' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'nomor_seri' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $stockKontainer->update($request->all());

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockKontainer $stockKontainer)
    {
        $stockKontainer->delete();

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil dihapus.');
    }
}

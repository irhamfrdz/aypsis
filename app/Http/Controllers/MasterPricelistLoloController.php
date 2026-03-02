<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistLolo;
use Illuminate\Http\Request;

class MasterPricelistLoloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistLolo::query();

        if ($request->filled('terminal')) {
            $query->where('terminal', 'like', '%' . $request->terminal . '%');
        }

        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('master.pricelist-lolo.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-lolo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal' => 'required|string|max:255',
            'size' => 'required|string|max:50',
            'kategori' => 'required|string|max:50', // Full, Empty
            'tipe_aktivitas' => 'required|string|max:50', // Lift On, Lift Off
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        MasterPricelistLolo::create($validated);

        return redirect()->route('master.pricelist-lolo.index')
            ->with('success', 'Pricelist LOLO berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistLolo $pricelistLolo)
    {
        return view('master.pricelist-lolo.edit', compact('pricelistLolo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistLolo $pricelistLolo)
    {
        $validated = $request->validate([
            'terminal' => 'required|string|max:255',
            'size' => 'required|string|max:50',
            'kategori' => 'required|string|max:50',
            'tipe_aktivitas' => 'required|string|max:50',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $pricelistLolo->update($validated);

        return redirect()->route('master.pricelist-lolo.index')
            ->with('success', 'Pricelist LOLO berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistLolo $pricelistLolo)
    {
        $pricelistLolo->delete();

        return redirect()->route('master.pricelist-lolo.index')
            ->with('success', 'Pricelist LOLO berhasil dihapus.');
    }
}

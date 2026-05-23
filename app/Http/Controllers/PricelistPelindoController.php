<?php

namespace App\Http\Controllers;

use App\Models\PricelistPelindo;
use Illuminate\Http\Request;

class PricelistPelindoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistPelindo::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                    ->orWhere('ukuran', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('master.pricelist-pelindo.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-pelindo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('tarif')) {
            $tarifClean = str_replace(['.', ','], ['', '.'], $request->input('tarif'));
            $request->merge(['tarif' => $tarifClean]);
        }

        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'status_kontainer' => 'nullable|in:empty,full',
        ]);

        PricelistPelindo::create($validated);

        return redirect()->route('master.pricelist-pelindo.index')
            ->with('success', 'Pricelist Pelindo berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistPelindo $pricelistPelindo)
    {
        return view('master.pricelist-pelindo.show', compact('pricelistPelindo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistPelindo $pricelistPelindo)
    {
        return view('master.pricelist-pelindo.edit', compact('pricelistPelindo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistPelindo $pricelistPelindo)
    {
        if ($request->has('tarif')) {
            $tarifClean = str_replace(['.', ','], ['', '.'], $request->input('tarif'));
            $request->merge(['tarif' => $tarifClean]);
        }

        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'status_kontainer' => 'nullable|in:empty,full',
        ]);

        $pricelistPelindo->update($validated);

        return redirect()->route('master.pricelist-pelindo.index')
            ->with('success', 'Pricelist Pelindo berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistPelindo $pricelistPelindo)
    {
        $pricelistPelindo->delete();

        return redirect()->route('master.pricelist-pelindo.index')
            ->with('success', 'Pricelist Pelindo berhasil dihapus.');
    }
}

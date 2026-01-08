<?php

namespace App\Http\Controllers;

use App\Models\PricelistBiayaDokumen;
use Illuminate\Http\Request;

class PricelistBiayaDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pricelists = PricelistBiayaDokumen::orderBy('created_at', 'desc')->get();
        return view('master.pricelist-biaya-dokumen.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-biaya-dokumen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        PricelistBiayaDokumen::create($validated);

        return redirect()->route('master.pricelist-biaya-dokumen.index')
            ->with('success', 'Pricelist biaya dokumen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistBiayaDokumen $pricelistBiayaDokumen)
    {
        return view('master.pricelist-biaya-dokumen.show', compact('pricelistBiayaDokumen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistBiayaDokumen $pricelistBiayaDokumen)
    {
        return view('master.pricelist-biaya-dokumen.edit', compact('pricelistBiayaDokumen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistBiayaDokumen $pricelistBiayaDokumen)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $pricelistBiayaDokumen->update($validated);

        return redirect()->route('master.pricelist-biaya-dokumen.index')
            ->with('success', 'Pricelist biaya dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistBiayaDokumen $pricelistBiayaDokumen)
    {
        $pricelistBiayaDokumen->delete();

        return redirect()->route('master.pricelist-biaya-dokumen.index')
            ->with('success', 'Pricelist biaya dokumen berhasil dihapus.');
    }
}

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

        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }



        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
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
            'vendor' => 'nullable|string|max:255',
            'lokasi' => 'required|in:Jakarta,Batam,Pinang',
            'size' => 'required|string|max:50',
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
            'vendor' => 'nullable|string|max:255',
            'lokasi' => 'required|in:Jakarta,Batam,Pinang',
            'size' => 'required|string|max:50',
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

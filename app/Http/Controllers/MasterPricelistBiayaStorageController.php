<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistBiayaStorage;
use Illuminate\Http\Request;

class MasterPricelistBiayaStorageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistBiayaStorage::query();

        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('master-pricelist-biaya-storage.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pricelist-biaya-storage.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size_kontainer' => 'required|string|max:50',
            'biaya_per_hari' => 'required|numeric|min:0',
            'free_time' => 'required|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'keterangan' => 'nullable|string',
        ]);

        MasterPricelistBiayaStorage::create($validated);

        return redirect()->route('master-pricelist-biaya-storage.index')
            ->with('success', 'Pricelist Biaya Storage berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistBiayaStorage $masterPricelistBiayaStorage)
    {
        $pricelist = $masterPricelistBiayaStorage;
        return view('master-pricelist-biaya-storage.edit', compact('pricelist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistBiayaStorage $masterPricelistBiayaStorage)
    {
        $validated = $request->validate([
            'vendor' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size_kontainer' => 'required|string|max:50',
            'biaya_per_hari' => 'required|numeric|min:0',
            'free_time' => 'required|integer|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'keterangan' => 'nullable|string',
        ]);

        $masterPricelistBiayaStorage->update($validated);

        return redirect()->route('master-pricelist-biaya-storage.index')
            ->with('success', 'Pricelist Biaya Storage berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistBiayaStorage $masterPricelistBiayaStorage)
    {
        $masterPricelistBiayaStorage->delete();

        return redirect()->route('master-pricelist-biaya-storage.index')
            ->with('success', 'Pricelist Biaya Storage berhasil dihapus.');
    }
}

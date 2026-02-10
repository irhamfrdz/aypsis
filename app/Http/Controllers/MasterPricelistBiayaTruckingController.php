<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistBiayaTrucking;
use Illuminate\Http\Request;

class MasterPricelistBiayaTruckingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistBiayaTrucking::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan nama_vendor
        if ($request->filled('nama_vendor')) {
            $query->where('nama_vendor', 'like', '%' . $request->nama_vendor . '%');
        }

        // Filter berdasarkan size
        if ($request->filled('size')) {
            $query->where('size', 'like', '%' . $request->size . '%');
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('master.pricelist-biaya-trucking.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-biaya-trucking.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'size' => 'required|string|max:50',
            'biaya' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        MasterPricelistBiayaTrucking::create($validated);

        return redirect()->route('master.pricelist-biaya-trucking.index')
            ->with('success', 'Pricelist biaya trucking berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistBiayaTrucking $pricelistBiayaTrucking)
    {
        return view('master.pricelist-biaya-trucking.show', compact('pricelistBiayaTrucking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistBiayaTrucking $pricelistBiayaTrucking)
    {
        return view('master.pricelist-biaya-trucking.edit', compact('pricelistBiayaTrucking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistBiayaTrucking $pricelistBiayaTrucking)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'size' => 'required|string|max:50',
            'biaya' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $pricelistBiayaTrucking->update($validated);

        return redirect()->route('master.pricelist-biaya-trucking.index')
            ->with('success', 'Pricelist biaya trucking berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistBiayaTrucking $pricelistBiayaTrucking)
    {
        $pricelistBiayaTrucking->delete();

        return redirect()->route('master.pricelist-biaya-trucking.index')
            ->with('success', 'Pricelist biaya trucking berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PricelistThc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PricelistThcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistThc::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%");
            });
        }

        $pricelistThcs = $query->latest()->paginate(10)->withQueryString();

        return view('master.pricelist-thc.index', compact('pricelistThcs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-thc.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|in:Jakarta,Batam,Pinang',
            'vendor' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        PricelistThc::create($request->all());

        return redirect()->route('master.pricelist-thc.index')
            ->with('success', 'Pricelist THC berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistThc $pricelistThc)
    {
        return view('master.pricelist-thc.show', compact('pricelistThc'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pricelistThc = PricelistThc::findOrFail($id);
        return view('master.pricelist-thc.edit', compact('pricelistThc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pricelistThc = PricelistThc::findOrFail($id);
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|in:Jakarta,Batam,Pinang',
            'vendor' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $pricelistThc->update($request->all());

        return redirect()->route('master.pricelist-thc.index')
            ->with('success', 'Pricelist THC berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pricelistThc = PricelistThc::findOrFail($id);
        $pricelistThc->delete();

        return redirect()->route('master.pricelist-thc.index')
            ->with('success', 'Pricelist THC berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PricelistTkbm;
use Illuminate\Http\Request;

class PricelistTkbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistTkbm::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        $pricelistTkbms = $query->latest()->paginate(10);

        return view('master.pricelist-tkbm.index', compact('pricelistTkbms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-tkbm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        PricelistTkbm::create($request->all());

        return redirect()->route('master.pricelist-tkbm.index')
            ->with('success', 'Pricelist TKBM berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistTkbm $pricelistTkbm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pricelistTkbm = PricelistTkbm::findOrFail($id);
        return view('master.pricelist-tkbm.edit', compact('pricelistTkbm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pricelistTkbm = PricelistTkbm::findOrFail($id);
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $pricelistTkbm->update($request->all());

        return redirect()->route('master.pricelist-tkbm.index')
            ->with('success', 'Pricelist TKBM berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pricelistTkbm = PricelistTkbm::findOrFail($id);
        $pricelistTkbm->delete();

        return redirect()->route('master.pricelist-tkbm.index')
            ->with('success', 'Pricelist TKBM berhasil dihapus.');
    }
}

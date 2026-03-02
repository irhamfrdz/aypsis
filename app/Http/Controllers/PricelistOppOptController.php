<?php

namespace App\Http\Controllers;

use App\Models\PricelistOppOpt;
use Illuminate\Http\Request;

class PricelistOppOptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistOppOpt::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $pricelistOppOpts = $query->latest()->paginate(10)->withQueryString();

        return view('master.pricelist-opp-opt.index', compact('pricelistOppOpts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-opp-opt.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|in:Jakarta,Batam,Pinang',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        PricelistOppOpt::create($request->all());

        return redirect()->route('master.pricelist-opp-opt.index')
            ->with('success', 'Pricelist OPP/OPT berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pricelistOppOpt = PricelistOppOpt::findOrFail($id);
        return view('master.pricelist-opp-opt.edit', compact('pricelistOppOpt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pricelistOppOpt = PricelistOppOpt::findOrFail($id);
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|in:Jakarta,Batam,Pinang',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $pricelistOppOpt->update($request->all());

        return redirect()->route('master.pricelist-opp-opt.index')
            ->with('success', 'Pricelist OPP/OPT berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pricelistOppOpt = PricelistOppOpt::findOrFail($id);
        $pricelistOppOpt->delete();

        return redirect()->route('master.pricelist-opp-opt.index')
            ->with('success', 'Pricelist OPP/OPT berhasil dihapus.');
    }
}

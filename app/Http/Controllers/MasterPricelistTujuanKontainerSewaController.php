<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistTujuanKontainerSewa;
use Illuminate\Http\Request;

class MasterPricelistTujuanKontainerSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistTujuanKontainerSewa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tujuan', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
        }

        $pricelists = $query->orderBy('tujuan')->paginate(20);

        return view('master-pricelist-tujuan-kontainer-sewa.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pricelist-tujuan-kontainer-sewa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tujuan' => 'required|string|max:255',
            'ongkos_truk_20ft' => 'required|numeric|min:0',
            'ongkos_truk_40ft' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        MasterPricelistTujuanKontainerSewa::create($request->all());

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Tujuan Kontainer Sewa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pricelist = MasterPricelistTujuanKontainerSewa::findOrFail($id);

        return view('master-pricelist-tujuan-kontainer-sewa.show', compact('pricelist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pricelist = MasterPricelistTujuanKontainerSewa::findOrFail($id);

        return view('master-pricelist-tujuan-kontainer-sewa.edit', compact('pricelist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required|string|max:255',
            'ongkos_truk_20ft' => 'required|numeric|min:0',
            'ongkos_truk_40ft' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $pricelist = MasterPricelistTujuanKontainerSewa::findOrFail($id);
        $pricelist->update($request->all());

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Tujuan Kontainer Sewa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pricelist = MasterPricelistTujuanKontainerSewa::findOrFail($id);
        $pricelist->delete();

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Tujuan Kontainer Sewa berhasil dihapus.');
    }
}

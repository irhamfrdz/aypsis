<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistLembur;
use Illuminate\Http\Request;

class MasterPricelistLemburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistLembur::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('master.pricelist-lembur.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-lembur.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        MasterPricelistLembur::create($validated);

        return redirect()->route('master.pricelist-lembur.index')
            ->with('success', 'Pricelist lembur berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistLembur $pricelistLembur)
    {
        return view('master.pricelist-lembur.edit', compact('pricelistLembur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistLembur $pricelistLembur)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $pricelistLembur->update($validated);

        return redirect()->route('master.pricelist-lembur.index')
            ->with('success', 'Pricelist lembur berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistLembur $pricelistLembur)
    {
        $pricelistLembur->delete();

        return redirect()->route('master.pricelist-lembur.index')
            ->with('success', 'Pricelist lembur berhasil dihapus.');
    }
}

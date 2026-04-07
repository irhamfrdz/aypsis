<?php

namespace App\Http\Controllers;

use App\Models\PricelistMeratus;
use Illuminate\Http\Request;

class PricelistMeratusController extends Controller
{
    public function index(Request $request)
    {
        $query = PricelistMeratus::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_biaya', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $pricelistMeratus = $query->latest()->paginate(10)->withQueryString();

        return view('master.pricelist-meratus.index', compact('pricelistMeratus'));
    }

    public function create()
    {
        return view('master.pricelist-meratus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_biaya' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        PricelistMeratus::create($request->all());

        return redirect()->route('master.pricelist-meratus.index')
            ->with('success', 'Pricelist Meratus berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pricelistMeratus = PricelistMeratus::findOrFail($id);
        return view('master.pricelist-meratus.edit', compact('pricelistMeratus'));
    }

    public function update(Request $request, $id)
    {
        $pricelistMeratus = PricelistMeratus::findOrFail($id);
        
        $request->validate([
            'jenis_biaya' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $pricelistMeratus->update($request->all());

        return redirect()->route('master.pricelist-meratus.index')
            ->with('success', 'Pricelist Meratus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pricelistMeratus = PricelistMeratus::findOrFail($id);
        $pricelistMeratus->delete();

        return redirect()->route('master.pricelist-meratus.index')
            ->with('success', 'Pricelist Meratus berhasil dihapus.');
    }
}

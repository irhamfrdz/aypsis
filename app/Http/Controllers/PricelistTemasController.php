<?php

namespace App\Http\Controllers;

use App\Models\PricelistTemas;
use Illuminate\Http\Request;

class PricelistTemasController extends Controller
{
    public function index(Request $request)
    {
        $query = PricelistTemas::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_biaya', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $pricelistTemas = $query->latest()->paginate(10)->withQueryString();

        return view('master.pricelist-temas.index', compact('pricelistTemas'));
    }

    public function create()
    {
        return view('master.pricelist-temas.create');
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

        PricelistTemas::create($request->all());

        return redirect()->route('master.pricelist-temas.index')
            ->with('success', 'Pricelist Temas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pricelistTemas = PricelistTemas::findOrFail($id);
        return view('master.pricelist-temas.edit', compact('pricelistTemas'));
    }

    public function update(Request $request, $id)
    {
        $pricelistTemas = PricelistTemas::findOrFail($id);
        
        $request->validate([
            'jenis_biaya' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $pricelistTemas->update($request->all());

        return redirect()->route('master.pricelist-temas.index')
            ->with('success', 'Pricelist Temas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pricelistTemas = PricelistTemas::findOrFail($id);
        $pricelistTemas->delete();

        return redirect()->route('master.pricelist-temas.index')
            ->with('success', 'Pricelist Temas berhasil dihapus.');
    }
}

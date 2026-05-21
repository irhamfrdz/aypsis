<?php

namespace App\Http\Controllers;

use App\Models\PricelistTanto;
use Illuminate\Http\Request;

class PricelistTantoController extends Controller
{
    public function index(Request $request)
    {
        $query = PricelistTanto::query();

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_biaya', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $pricelistTantos = $query->latest()->paginate(10)->withQueryString();

        return view('master.pricelist-tanto.index', compact('pricelistTantos'));
    }

    public function create()
    {
        return view('master.pricelist-tanto.create');
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

        PricelistTanto::create($request->all());

        return redirect()->route('master.pricelist-tanto.index')
            ->with('success', 'Pricelist Tanto berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pricelistTanto = PricelistTanto::findOrFail($id);

        return view('master.pricelist-tanto.edit', compact('pricelistTanto'));
    }

    public function update(Request $request, $id)
    {
        $pricelistTanto = PricelistTanto::findOrFail($id);

        $request->validate([
            'jenis_biaya' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $pricelistTanto->update($request->all());

        return redirect()->route('master.pricelist-tanto.index')
            ->with('success', 'Pricelist Tanto berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pricelistTanto = PricelistTanto::findOrFail($id);
        $pricelistTanto->delete();

        return redirect()->route('master.pricelist-tanto.index')
            ->with('success', 'Pricelist Tanto berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PricelistCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricelistCatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistCat::with(['creator', 'updater']);

                // Filter by vendor
        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }

        // Filter by jenis_cat
        if ($request->filled('jenis_cat')) {
            $query->where('jenis_cat', $request->jenis_cat);
        }

        // Filter by ukuran_kontainer
        if ($request->filled('ukuran_kontainer')) {
            $query->where('ukuran_kontainer', $request->ukuran_kontainer);
        }

        // Filter by search (general search)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('vendor', 'like', '%' . $request->search . '%')
                  ->orWhere('ukuran_kontainer', 'like', '%' . $request->search . '%');
            });
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('master-pricelist-cat.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pricelist = new PricelistCat();
        return view('master-pricelist-cat.create', compact('pricelist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required|string|max:255',
            'jenis_cat' => 'required|in:cat_sebagian,cat_full',
            'tarif_raw' => 'nullable|numeric|min:0',
            'ukuran_kontainer' => 'required|string|max:255',
        ]);

        PricelistCat::create([
            'vendor' => $request->vendor,
            'jenis_cat' => $request->jenis_cat,
            'tarif' => $request->tarif_raw,
            'ukuran_kontainer' => $request->ukuran_kontainer,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('master.pricelist-cat.index')
                        ->with('success', 'Pricelist CAT berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistCat $pricelistCat)
    {
        return view('master-pricelist-cat.show', compact('pricelistCat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistCat $pricelistCat)
    {
        return view('master-pricelist-cat.edit', compact('pricelistCat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistCat $pricelistCat)
    {
        $request->validate([
            'vendor' => 'required|string|max:255',
            'jenis_cat' => 'required|in:cat_sebagian,cat_full',
            'tarif_raw' => 'nullable|numeric|min:0',
            'ukuran_kontainer' => 'required|string|max:255',
        ]);

        $pricelistCat->update([
            'vendor' => $request->vendor,
            'jenis_cat' => $request->jenis_cat,
            'tarif' => $request->tarif_raw,
            'ukuran_kontainer' => $request->ukuran_kontainer,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('master.pricelist-cat.index')
                        ->with('success', 'Pricelist CAT berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistCat $pricelistCat)
    {
        $pricelistCat->delete();

        return redirect()->route('master.pricelist-cat.index')
                        ->with('success', 'Pricelist CAT berhasil dihapus.');
    }
}

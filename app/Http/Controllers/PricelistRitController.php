<?php

namespace App\Http\Controllers;

use App\Models\PricelistRit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricelistRitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistRit::with(['creator', 'updater']);

        // Filter by tujuan
        if ($request->filled('tujuan')) {
            $query->where('tujuan', 'like', '%' . $request->tujuan . '%');
        }

        // Filter by keterangan
        if ($request->filled('keterangan')) {
            $query->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }

        // Filter by search (general search)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('tujuan', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('master-pricelist-rit.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pricelist = new PricelistRit();
        return view('master-pricelist-rit.create', compact('pricelist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tujuan' => 'required|in:Supir,Kenek',
            'tarif_raw' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'keterangan' => 'nullable|string',
        ]);

        PricelistRit::create([
            'tujuan' => $request->tujuan,
            'tarif' => $request->tarif_raw,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('master.pricelist-rit.index')
                        ->with('success', 'Pricelist Rit berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistRit $pricelistRit)
    {
        return view('master-pricelist-rit.show', compact('pricelistRit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistRit $pricelistRit)
    {
        return view('master-pricelist-rit.edit', compact('pricelistRit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistRit $pricelistRit)
    {
        $request->validate([
            'tujuan' => 'required|in:Supir,Kenek',
            'tarif_raw' => 'required|numeric|min:0',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'keterangan' => 'nullable|string',
        ]);

        $pricelistRit->update([
            'tujuan' => $request->tujuan,
            'tarif' => $request->tarif_raw,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('master.pricelist-rit.index')
                        ->with('success', 'Pricelist Rit berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistRit $pricelistRit)
    {
        $pricelistRit->delete();

        return redirect()->route('master.pricelist-rit.index')
                        ->with('success', 'Pricelist Rit berhasil dihapus.');
    }
}

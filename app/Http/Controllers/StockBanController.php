<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockBans = StockBan::with('mobil')->latest()->get();
        return view('stock-ban.index', compact('stockBans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        return view('stock-ban.create', compact('mobils'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_seri' => 'required|unique:stock_bans,nomor_seri',
            'merk' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'kondisi' => 'required|in:Baru,Vulkanisir,Bekas,Afkir',
            'status' => 'required|in:Stok,Terpakai,Rusak,Hilang',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
        ]);

        // Clean up numeric input if needed (though numeric validation usually handles it, sometimes format uses dots)
        // Assuming simple input for now or standard format.
        
        StockBan::create($request->all());

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stockBan = StockBan::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        return view('stock-ban.edit', compact('stockBan', 'mobils'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        $request->validate([
            'nomor_seri' => 'required|unique:stock_bans,nomor_seri,' . $stockBan->id,
            'merk' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'kondisi' => 'required|in:Baru,Vulkanisir,Bekas,Afkir',
            'status' => 'required|in:Stok,Terpakai,Rusak,Hilang',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
        ]);

        $stockBan->update($request->all());

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stockBan = StockBan::findOrFail($id);
        $stockBan->delete();

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil dihapus');
    }
}

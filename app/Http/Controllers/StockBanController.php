<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use App\Models\Mobil;
use App\Models\NamaStockBan;
use App\Models\MerkBan;
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
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        return view('stock-ban.create', compact('mobils', 'namaStockBans', 'merkBans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'required|unique:stock_bans,nomor_seri',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'required|string|max:255',
            'kondisi' => 'required|in:Baru,Vulkanisir,Bekas,Afkir',
            'status' => 'required|in:Stok,Terpakai,Rusak,Hilang',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }
        
        StockBan::create($data);

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stockBan = StockBan::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        return view('stock-ban.edit', compact('stockBan', 'mobils', 'namaStockBans', 'merkBans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
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
            'nomor_bukti' => 'nullable|string|max:255',
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

<?php

namespace App\Http\Controllers;

use App\Models\StockAmprahan;
use App\Models\MasterNamaBarangAmprahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockAmprahanController extends Controller
{
    public function index()
    {
        $items = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy'])
            ->latest()
            ->paginate(20);
            
        return view('stock-amprahan.index', compact('items'));
    }

    public function create()
    {
        $masterItems = MasterNamaBarangAmprahan::where('status', 'aktif')->orderBy('nama_barang')->get();
        return view('stock-amprahan.create', compact('masterItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        
        StockAmprahan::create($data);

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil ditambahkan');
    }

    public function show($id)
    {
        $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('stock-amprahan.show', compact('item'));
    }

    public function edit($id)
    {
        $item = StockAmprahan::findOrFail($id);
        $masterItems = MasterNamaBarangAmprahan::where('status', 'aktif')->orderBy('nama_barang')->get();
        return view('stock-amprahan.edit', compact('item', 'masterItems'));
    }

    public function update(Request $request, $id)
    {
        $item = StockAmprahan::findOrFail($id);
        
        $data = $request->validate([
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();

        $item->update($data);

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = StockAmprahan::findOrFail($id);
        $item->delete();
        
        return redirect()->route('stock-amprahan.index')->with('success', 'Data stock amprahan berhasil dihapus');
    }
}

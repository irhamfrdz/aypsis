<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MasterItemKwitansi;
use Illuminate\Http\Request;

class MasterItemKwitansiController extends Controller
{
    public function index()
    {
        $items = MasterItemKwitansi::latest()->get();
        return view('master.item-kwitansi.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        MasterItemKwitansi::create($request->all());

        return redirect()->back()->with('success', 'Item Kwitansi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $item = MasterItemKwitansi::findOrFail($id);
        $item->update($request->all());

        return redirect()->back()->with('success', 'Item Kwitansi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = MasterItemKwitansi::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item Kwitansi berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MasterItemKwitansi;

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
            'kode' => 'required|string|max:50|unique:master_item_kwitansis,kode',
            'nama_item' => 'required|string|max:255',
            'group' => 'required|string|max:100',
        ]);

        MasterItemKwitansi::create($request->all());

        return redirect()->back()->with('success', 'Item Kwitansi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_item_kwitansis,kode,' . $id,
            'nama_item' => 'required|string|max:255',
            'group' => 'required|string|max:100',
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

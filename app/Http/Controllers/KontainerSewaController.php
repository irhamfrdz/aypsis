<?php

namespace App\Http\Controllers;

use App\Models\KontainerSewa;
use Illuminate\Http\Request;

class KontainerSewaController extends Controller
{
    public function index()
    {
        $kontainerSewa = KontainerSewa::orderBy('created_at', 'desc')->paginate(10);
        return view('kontainer-sewa.index', compact('kontainerSewa'));
    }

    public function create()
    {
    return view('kontainer-sewa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
        ]);
        KontainerSewa::create($request->all());
    return redirect()->route('kontainer-sewa.index')->with('success', 'Kontainer sewa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kontainerSewa = KontainerSewa::findOrFail($id);
        return view('kontainer-sewa.edit', compact('kontainerSewa'));
    }

    public function update(Request $request, $id)
    {
        $kontainerSewa = KontainerSewa::findOrFail($id);
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
        ]);
        $kontainerSewa->update($request->all());
    return redirect()->route('kontainer-sewa.index')->with('success', 'Kontainer sewa berhasil diupdate');
    }

    public function destroy($id)
    {
        $kontainerSewa = KontainerSewa::findOrFail($id);
        $kontainerSewa->delete();
    return redirect()->route('kontainer-sewa.index')->with('success', 'Kontainer sewa berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;

class MobilController extends Controller
{
    /**
     * Menampilkan daftar semua mobil.
     */
    public function index()
    {
        $mobils = Mobil::latest()->paginate(10);
        return view('master-mobil.index', compact('mobils'));
    }

    /**
     * Menampilkan form untuk membuat mobil baru.
     */
    public function create()
    {
        return view('master-mobil.create');
    }

    /**
     * Menyimpan mobil baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aktiva' => 'required|string|max:50|unique:mobils,aktiva',
            'plat' => 'required|string|max:20|unique:mobils,plat',
            'nomor_rangka' => 'required|string|max:50|unique:mobils,nomor_rangka',
            'ukuran' => 'required|string|max:50',
        ]);

        Mobil::create($validated);

        return redirect()->route('master.mobil.index')
                         ->with('success', 'Mobil berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit mobil.
     */
    public function edit(Mobil $mobil)
    {
        return view('master-mobil.edit', compact('mobil'));
    }

    /**
     * Memperbarui data mobil di database.
     */
    public function update(Request $request, Mobil $mobil)
    {
        $validated = $request->validate([
            'aktiva' => 'required|string|max:50|unique:mobils,aktiva,' . $mobil->id,
            'plat' => 'required|string|max:20|unique:mobils,plat,' . $mobil->id,
            'nomor_rangka' => 'required|string|max:50|unique:mobils,nomor_rangka,' . $mobil->id,
            'ukuran' => 'required|string|max:50',
        ]);

        $mobil->update($validated);

        return redirect()->route('master.mobil.index')
                         ->with('success', 'Mobil berhasil diperbarui.');
    }

    /**
     * Menghapus mobil dari database.
     */
    public function destroy(Mobil $mobil)
    {
        $mobil->delete();

        return redirect()->route('master.mobil.index')
                         ->with('success', 'Mobil berhasil dihapus.');
    }
}

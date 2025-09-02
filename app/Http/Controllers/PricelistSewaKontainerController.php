<?php

namespace App\Http\Controllers;

use App\Models\PricelistSewaKontainer;
use Illuminate\Http\Request;

class PricelistSewaKontainerController extends Controller
{
    /**
     * Menampilkan daftar semua pricelist sewa kontainer.
     */
    public function index()
    {
        $pricelists = PricelistSewaKontainer::latest()->paginate(10);
        return view('master-pricelist-sewa-kontainer.index', compact('pricelists'));
    }

    /**
     * Menampilkan form untuk membuat pricelist sewa kontainer baru.
     */
    public function create()
    {
        return view('master-pricelist-sewa-kontainer.create');
    }

    /**
     * Menyimpan pricelist sewa kontainer baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'required|string|in:ZONA,DPE',
            'tarif' => 'required|string|in:Bulanan,Harian',
            'ukuran_kontainer' => 'required|string|in:20,40',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'required|date|after:tanggal_harga_awal',
            'keterangan' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        PricelistSewaKontainer::create($validated);

        return redirect()->route('master.pricelist-sewa-kontainer.index')
                         ->with('success', 'Pricelist sewa kontainer berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit pricelist sewa kontainer.
     */
    public function edit(PricelistSewaKontainer $pricelistSewaKontainer)
    {
        return view('master-pricelist-sewa-kontainer.edit', compact('pricelistSewaKontainer'));
    }

    /**
     * Memperbarui data pricelist sewa kontainer di database.
     */
    public function update(Request $request, PricelistSewaKontainer $pricelistSewaKontainer)
    {
        $validated = $request->validate([
            'vendor' => 'required|string|in:ZONA,DPE',
            'tarif' => 'required|string|in:Bulanan,Harian',
            'ukuran_kontainer' => 'required|string|in:20,40',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'required|date|after:tanggal_harga_awal',
            'keterangan' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $pricelistSewaKontainer->update($validated);

        return redirect()->route('master.pricelist-sewa-kontainer.index')
                         ->with('success', 'Pricelist sewa kontainer berhasil diperbarui.');
    }

    /**
     * Menghapus pricelist sewa kontainer dari database.
     */
    public function destroy(PricelistSewaKontainer $pricelistSewaKontainer)
    {
        $pricelistSewaKontainer->delete();

        return redirect()->route('master.pricelist-sewa-kontainer.index')
                         ->with('success', 'Pricelist sewa kontainer berhasil dihapus.');
    }
}

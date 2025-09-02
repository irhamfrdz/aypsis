<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Http\Request;

class MasterPricelistSewaKontainerController extends Controller
{
    public function index()
    {
        $pricelists = MasterPricelistSewaKontainer::orderBy('created_at', 'desc')->paginate(10);
        return view('master-pricelist-sewa-kontainer.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master-pricelist-sewa-kontainer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'nullable|date',
        ]);
        $data = $request->all();
        // Normalize date inputs to date-only strings to avoid sqlite storing timestamps
        try {
            if (!empty($data['tanggal_harga_awal'])) {
                $data['tanggal_harga_awal'] = \Carbon\Carbon::parse($data['tanggal_harga_awal'])->toDateString();
            }
            if (!empty($data['tanggal_harga_akhir'])) {
                $data['tanggal_harga_akhir'] = \Carbon\Carbon::parse($data['tanggal_harga_akhir'])->toDateString();
            }
        } catch (\Exception $e) {
            // leave input as-is if parsing fails
        }
        // Some test DBs (sqlite) may still have the legacy nomor_tagihan column. If present, ensure it exists in data.
        if (\Illuminate\Support\Facades\Schema::hasColumn('master_pricelist_sewa_kontainers', 'nomor_tagihan') && !array_key_exists('nomor_tagihan', $data)) {
            // If the legacy column exists (tests/sqlite), assign a generated unique placeholder to satisfy NOT NULL/unique.
            $data['nomor_tagihan'] = 'PR-' . time() . '-' . rand(1000, 9999);
        }
        MasterPricelistSewaKontainer::create($data);
    return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pricelist = MasterPricelistSewaKontainer::findOrFail($id);
        return view('master-pricelist-sewa-kontainer.edit', compact('pricelist'));
    }

    public function update(Request $request, $id)
    {
        $pricelist = MasterPricelistSewaKontainer::findOrFail($id);
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'nullable|date',
        ]);
        $pricelist->update($request->all());
    return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $pricelist = MasterPricelistSewaKontainer::findOrFail($id);
        $pricelist->delete();
    return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', 'Data berhasil dihapus');
    }
}

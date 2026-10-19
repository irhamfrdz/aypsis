<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistBiayaStoragePelindo;
use Illuminate\Http\Request;

class MasterPricelistBiayaStoragePelindoController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPricelistBiayaStoragePelindo::query();

        foreach (['vendor', 'lokasi'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, 'like', '%'.$request->input($field).'%');
            }
        }

        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->input('size_kontainer'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pricelists = $query->orderBy('vendor')->orderBy('lokasi')->orderBy('size_kontainer')->paginate(20)->withQueryString();

        return view('master-pricelist-biaya-storage-pelindo.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master-pricelist-biaya-storage-pelindo.create');
    }

    public function store(Request $request)
    {
        MasterPricelistBiayaStoragePelindo::create($this->validated($request));

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil ditambahkan.');
    }

    public function edit(MasterPricelistBiayaStoragePelindo $masterPricelistBiayaStoragePelindo)
    {
        $pricelist = $masterPricelistBiayaStoragePelindo;

        return view('master-pricelist-biaya-storage-pelindo.edit', compact('pricelist'));
    }

    public function update(Request $request, MasterPricelistBiayaStoragePelindo $masterPricelistBiayaStoragePelindo)
    {
        $masterPricelistBiayaStoragePelindo->update($this->validated($request));

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil diperbarui.');
    }

    public function destroy(MasterPricelistBiayaStoragePelindo $masterPricelistBiayaStoragePelindo)
    {
        $masterPricelistBiayaStoragePelindo->delete();

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'vendor' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'size_kontainer' => ['required', 'string', 'max:50'],
            'tarif_massa_1' => ['required', 'numeric', 'min:0'],
            'tarif_massa_2' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:aktif,non-aktif'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}

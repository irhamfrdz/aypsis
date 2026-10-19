<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistBiayaStoragePelindo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterPricelistBiayaStoragePelindoController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPricelistBiayaStoragePelindo::with('tarifMassa');

        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->input('size_kontainer'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('status_kontainer')) {
            $query->where('status_kontainer', $request->input('status_kontainer'));
        }

        $pricelists = $query->orderBy('size_kontainer')->orderBy('status_kontainer')->paginate(20)->withQueryString();

        return view('master-pricelist-biaya-storage-pelindo.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master-pricelist-biaya-storage-pelindo.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($data) {
            $pricelist = MasterPricelistBiayaStoragePelindo::create($data);
            $pricelist->tarifMassa()->createMany($data['tarif_massa']);
        });

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil ditambahkan.');
    }

    public function edit(MasterPricelistBiayaStoragePelindo $pricelist)
    {
        $pricelist->load('tarifMassa');

        return view('master-pricelist-biaya-storage-pelindo.edit', compact('pricelist'));
    }

    public function update(Request $request, MasterPricelistBiayaStoragePelindo $pricelist)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($pricelist, $data) {
            $pricelist->update(collect($data)->except('tarif_massa')->all());
            $pricelist->tarifMassa()->delete();
            $pricelist->tarifMassa()->createMany($data['tarif_massa']);
        });

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil diperbarui.');
    }

    public function destroy(MasterPricelistBiayaStoragePelindo $pricelist)
    {
        $pricelist->delete();

        return redirect()->route('master-pricelist-biaya-storage-pelindo.index')
            ->with('success', 'Pricelist Biaya Storage Pelindo berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'size_kontainer' => ['required', 'string', 'max:50'],
            'status_kontainer' => ['required', 'in:full,empty'],
            'tarif_massa' => ['required', 'array', 'min:1'],
            'tarif_massa.*.massa' => ['required', 'numeric', 'gt:0', 'distinct'],
            'tarif_massa.*.tarif' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:aktif,non-aktif'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}

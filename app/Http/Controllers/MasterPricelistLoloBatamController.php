<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistLoloBatam;
use Illuminate\Http\Request;

class MasterPricelistLoloBatamController extends Controller
{
    public function index(Request $request)
    {
        $pricelists = MasterPricelistLoloBatam::query()
            ->with(['creator', 'updater'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->trim()->toString();
                $query->where(function ($query) use ($search) {
                    $query->where('keterangan', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('size'), fn ($query) => $query->where('size', $request->input('size')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('master.pricelist-lolo-batam.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master.pricelist-lolo-batam.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        MasterPricelistLoloBatam::create($data);

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Pricelist LOLO Batam berhasil ditambahkan.');
    }

    public function edit(MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        return view('master.pricelist-lolo-batam.edit', compact('pricelistLoloBatam'));
    }

    public function update(Request $request, MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        $data = $this->validateData($request);
        $data['updated_by'] = auth()->id();
        $pricelistLoloBatam->update($data);

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Pricelist LOLO Batam berhasil diperbarui.');
    }

    public function destroy(MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        $pricelistLoloBatam->delete();

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Pricelist LOLO Batam berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'size' => ['required', 'in:20,40,45'],
            'tarif' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:aktif,non-aktif'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ], [
            'size.required' => 'Ukuran kontainer wajib dipilih.',
            'size.in' => 'Ukuran kontainer harus 20, 40, atau 45 kaki.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.numeric' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh kurang dari nol.',
        ]);
    }
}

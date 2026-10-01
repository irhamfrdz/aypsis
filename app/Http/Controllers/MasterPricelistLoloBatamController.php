<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistLoloBatam;
use Illuminate\Http\Request;

class MasterPricelistLoloBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPricelistLoloBatam::query();

        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%'.$request->vendor.'%');
        }

        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $pricelists = $query->orderBy('vendor')->orderBy('size')->orderBy('tipe')->paginate(20)->withQueryString();

        $vendors = MasterPricelistLoloBatam::select('vendor')->whereNotNull('vendor')->distinct()->pluck('vendor');

        return view('master.pricelist-lolo-batam.index', compact('pricelists', 'vendors'));
    }

    public function create()
    {
        $existingVendors = MasterPricelistLoloBatam::select('vendor')->whereNotNull('vendor')->distinct()->pluck('vendor');

        return view('master.pricelist-lolo-batam.create', compact('existingVendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'nullable|string|max:255',
            'nama_biaya' => 'required|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'size' => 'required|in:20,40',
            'tipe' => 'required|in:FULL,EMPTY,ALL',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($validated['kegiatan'])) {
            $validated['kegiatan'] = 'LOLO Batam';
        }

        MasterPricelistLoloBatam::create($validated);

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Master Pricelist LOLO Batam berhasil ditambahkan.');
    }

    public function edit(MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        $existingVendors = MasterPricelistLoloBatam::select('vendor')->whereNotNull('vendor')->distinct()->pluck('vendor');

        return view('master.pricelist-lolo-batam.edit', compact('pricelistLoloBatam', 'existingVendors'));
    }

    public function update(Request $request, MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        $validated = $request->validate([
            'vendor' => 'nullable|string|max:255',
            'nama_biaya' => 'required|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'size' => 'required|in:20,40',
            'tipe' => 'required|in:FULL,EMPTY,ALL',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,non-aktif',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($validated['kegiatan'])) {
            $validated['kegiatan'] = 'LOLO Batam';
        }

        $pricelistLoloBatam->update($validated);

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Master Pricelist LOLO Batam berhasil diperbarui.');
    }

    public function destroy(MasterPricelistLoloBatam $pricelistLoloBatam)
    {
        $pricelistLoloBatam->delete();

        return redirect()->route('master.pricelist-lolo-batam.index')
            ->with('success', 'Master Pricelist LOLO Batam berhasil dihapus.');
    }
}

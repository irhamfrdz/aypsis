<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistVendorSupir;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPricelistVendorSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistVendorSupir::with(['vendor', 'creator', 'updater']);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('dari', 'like', "%{$search}%")
                  ->orWhere('ke', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($vq) use ($search) {
                      $vq->where('nama_vendor', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jenis_kontainer')) {
            $query->where('jenis_kontainer', $request->jenis_kontainer);
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);
        $vendors = \App\Models\VendorSupir::orderBy('nama_vendor')->get();
        
        return view('master-tarif.pricelist-vendor-supir.index', compact('pricelists', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = \App\Models\VendorSupir::orderBy('nama_vendor')->get();
        return view('master-tarif.pricelist-vendor-supir.create', compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendor_supirs,id',
            'dari' => 'required|string|max:255',
            'ke' => 'required|string|max:255',
            'jenis_kontainer' => 'required|in:20,40,45',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ], [
            'dari.required' => 'Asal (Dari) harus diisi.',
            'ke.required' => 'Tujuan (Ke) harus diisi.',
            'jenis_kontainer.required' => 'Jenis kontainer harus dipilih.',
            'nominal.required' => 'Nominal harus diisi.',
            'status.required' => 'Status harus dipilih.',
        ]);

        $validated['created_by'] = Auth::id();

        MasterPricelistVendorSupir::create($validated);

        return redirect()->route('master.pricelist-vendor-supir.index')
            ->with('success', 'Pricelist vendor supir berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        // Not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        $vendors = \App\Models\VendorSupir::orderBy('nama_vendor')->get();
        return view('master-tarif.pricelist-vendor-supir.edit', compact('pricelistVendorSupir', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendor_supirs,id',
            'dari' => 'required|string|max:255',
            'ke' => 'required|string|max:255',
            'jenis_kontainer' => 'required|in:20,40,45',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ], [
            'dari.required' => 'Asal (Dari) harus diisi.',
            'ke.required' => 'Tujuan (Ke) harus diisi.',
            'jenis_kontainer.required' => 'Jenis kontainer harus dipilih.',
            'nominal.required' => 'Nominal harus diisi.',
            'status.required' => 'Status harus dipilih.',
        ]);

        $validated['updated_by'] = Auth::id();

        $pricelistVendorSupir->update($validated);

        return redirect()->route('master.pricelist-vendor-supir.index')
            ->with('success', 'Pricelist vendor supir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        $pricelistVendorSupir->delete();

        return redirect()->route('master.pricelist-vendor-supir.index')
            ->with('success', 'Pricelist vendor supir berhasil dihapus.');
    }
}

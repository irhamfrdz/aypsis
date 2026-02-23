<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistVendorSupir;
use App\Models\Tujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPricelistVendorSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistVendorSupir::with(['tujuan', 'creator', 'updater']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tujuan_id')) {
            $query->where('tujuan_id', $request->tujuan_id);
        }

        if ($request->filled('jenis_kontainer')) {
            $query->where('jenis_kontainer', $request->jenis_kontainer);
        }

        $pricelists = $query->orderBy('created_at', 'desc')->paginate(20);
        $tujuans = Tujuan::where('status', 'aktif')->orderBy('nama_tujuan')->get();
        
        return view('master-tarif.pricelist-vendor-supir.index', compact('pricelists', 'tujuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tujuans = Tujuan::where('status', 'aktif')->orderBy('nama_tujuan')->get();
        return view('master-tarif.pricelist-vendor-supir.create', compact('tujuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan_id' => 'required|exists:tujuans,id',
            'jenis_kontainer' => 'required|in:20,40,45',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ], [
            'tujuan_id.required' => 'Tujuan harus dipilih.',
            'jenis_kontainer.required' => 'Jenis kontainer harus dipilih.',
            'nominal.required' => 'Nominal harus diisi.',
            'status.required' => 'Status harus dipilih.',
        ]);

        $validated['created_by'] = Auth::id();

        MasterPricelistVendorSupir::create($validated);

        return redirect()->route('pricelist-vendor-supir.index')
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
        $tujuans = Tujuan::where('status', 'aktif')->orderBy('nama_tujuan')->get();
        return view('master-tarif.pricelist-vendor-supir.edit', compact('pricelistVendorSupir', 'tujuans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        $validated = $request->validate([
            'tujuan_id' => 'required|exists:tujuans,id',
            'jenis_kontainer' => 'required|in:20,40,45',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ], [
            'tujuan_id.required' => 'Tujuan harus dipilih.',
            'jenis_kontainer.required' => 'Jenis kontainer harus dipilih.',
            'nominal.required' => 'Nominal harus diisi.',
            'status.required' => 'Status harus dipilih.',
        ]);

        $validated['updated_by'] = Auth::id();

        $pricelistVendorSupir->update($validated);

        return redirect()->route('pricelist-vendor-supir.index')
            ->with('success', 'Pricelist vendor supir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistVendorSupir $pricelistVendorSupir)
    {
        $pricelistVendorSupir->delete();

        return redirect()->route('pricelist-vendor-supir.index')
            ->with('success', 'Pricelist vendor supir berhasil dihapus.');
    }
}

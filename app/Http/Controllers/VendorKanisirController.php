<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VendorKanisir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorKanisirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = VendorKanisir::latest()->get();
        $nextKode = VendorKanisir::generateNextCode();
        return view('vendor-kanisir.index', compact('vendors', 'nextKode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'kode' => 'nullable|string|unique:vendor_kanisirs,kode',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['kode'] = $request->kode ?: VendorKanisir::generateNextCode();
        $data['created_by'] = Auth::id();

        VendorKanisir::create($data);

        return redirect()->back()->with('success', 'Vendor Kanisir berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vendor = VendorKanisir::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'kode' => 'nullable|string|unique:vendor_kanisirs,kode,' . $id,
            'keterangan' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $vendor->update($data);

        return redirect()->back()->with('success', 'Vendor Kanisir berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vendor = VendorKanisir::findOrFail($id);
        $vendor->delete();

        return redirect()->back()->with('success', 'Vendor Kanisir berhasil dihapus');
    }
}

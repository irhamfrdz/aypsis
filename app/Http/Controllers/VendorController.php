<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($search = $request->get('search')) {
            $query->where('nama_vendor', 'like', '%' . $search . '%');
        }

        $vendors = $query->latest()->paginate(15);
        
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'tipe_hitung' => 'required|in:bulanan,harian',
        ]);

        Vendor::create($request->all());

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'tipe_hitung' => 'required|in:bulanan,harian',
        ]);

        $vendor->update($request->all());

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor berhasil dihapus!');
    }
}

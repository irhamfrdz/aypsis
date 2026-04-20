<?php

namespace App\Http\Controllers;

use App\Models\VendorAmprahan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorAmprahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VendorAmprahan::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                  ->orWhere('alamat_toko', 'like', "%{$search}%");
            });
        }

        $vendorAmprahans = $query->orderBy('nama_toko')->paginate(10);

        return view('master.vendor-amprahan.index', compact('vendorAmprahans'));
    }

    public function create()
    {
        return view('master.vendor-amprahan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:255|unique:vendor_amprahans,nama_toko',
            'alamat_toko' => 'nullable|string'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        VendorAmprahan::create($validated);

        return redirect()->route('master.vendor-amprahan.index')
            ->with('success', 'Vendor Amprahan berhasil ditambahkan.');
    }

    public function show(VendorAmprahan $vendorAmprahan)
    {
        return view('master.vendor-amprahan.show', compact('vendorAmprahan'));
    }

    public function edit(VendorAmprahan $vendorAmprahan)
    {
        return view('master.vendor-amprahan.edit', compact('vendorAmprahan'));
    }

    public function update(Request $request, VendorAmprahan $vendorAmprahan)
    {
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:255|unique:vendor_amprahans,nama_toko,' . $vendorAmprahan->id,
            'alamat_toko' => 'nullable|string'
        ]);

        $validated['updated_by'] = auth()->id();

        $vendorAmprahan->update($validated);

        return redirect()->route('master.vendor-amprahan.index')
            ->with('success', 'Vendor Amprahan berhasil diperbarui.');
    }

    public function destroy(VendorAmprahan $vendorAmprahan)
    {
        $vendorAmprahan->delete();

        return redirect()->route('master.vendor-amprahan.index')
            ->with('success', 'Vendor Amprahan berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VendorKontainerSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorKontainerSewaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VendorKontainerSewa::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%");
            });
        }

        $vendors = $query->latest()->paginate(10);

        return view('vendor-kontainer-sewa.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendor-kontainer-sewa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'tax_ppn_percent' => 'required|numeric|min:0|max:100',
            'tax_pph_percent' => 'required|numeric|min:0|max:100'
        ]);

        try {
            VendorKontainerSewa::create($request->all());

            return redirect()->route('vendor-kontainer-sewa.index')
                           ->with('success', 'Vendor kontainer sewa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorKontainerSewa $vendorKontainerSewa)
    {
        return view('vendor-kontainer-sewa.show', compact('vendorKontainerSewa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorKontainerSewa $vendorKontainerSewa)
    {
        return view('vendor-kontainer-sewa.edit', compact('vendorKontainerSewa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorKontainerSewa $vendorKontainerSewa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'tax_ppn_percent' => 'required|numeric|min:0|max:100',
            'tax_pph_percent' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $vendorKontainerSewa->update($request->all());

            return redirect()->route('vendor-kontainer-sewa.index')
                           ->with('success', 'Vendor kontainer sewa berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorKontainerSewa $vendorKontainerSewa)
    {
        try {
            $vendorKontainerSewa->delete();

            return redirect()->route('vendor-kontainer-sewa.index')
                           ->with('success', 'Vendor kontainer sewa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\VendorBengkel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class VendorBengkelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = VendorBengkel::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_bengkel', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $vendorBengkel = $query->orderBy('nama_bengkel')->paginate(10);

        return view('master.vendor-bengkel.index', compact('vendorBengkel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.vendor-bengkel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255|unique:vendor_bengkel,nama_bengkel',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        VendorBengkel::create($validated);

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorBengkel $vendorBengkel): View
    {
        return view('master.vendor-bengkel.show', compact('vendorBengkel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorBengkel $vendorBengkel): View
    {
        return view('master.vendor-bengkel.edit', compact('vendorBengkel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorBengkel $vendorBengkel): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255|unique:vendor_bengkel,nama_bengkel,' . $vendorBengkel->id,
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $validated['updated_by'] = Auth::id();

        $vendorBengkel->update($validated);

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorBengkel $vendorBengkel): RedirectResponse
    {
        // Check if vendor is being used in other tables
        // You might want to add relationship checks here

        $vendorBengkel->delete();

        return redirect()->route('master.vendor-bengkel.index')
            ->with('success', 'Vendor/Bengkel berhasil dihapus.');
    }
}

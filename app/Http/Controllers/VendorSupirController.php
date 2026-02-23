<?php

namespace App\Http\Controllers;

use App\Models\VendorSupir;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class VendorSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = VendorSupir::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_vendor', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $vendorSupirs = $query->orderBy('nama_vendor')->paginate(10);

        return view('master.vendor-supir.index', compact('vendorSupirs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.vendor-supir.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        VendorSupir::create($validated);

        return redirect()->route('master.vendor-supir.index')
            ->with('success', 'Vendor Supir berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorSupir $vendorSupir): View
    {
        return view('master.vendor-supir.show', compact('vendorSupir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorSupir $vendorSupir): View
    {
        return view('master.vendor-supir.edit', compact('vendorSupir'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorSupir $vendorSupir): RedirectResponse
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = Auth::id();

        $vendorSupir->update($validated);

        return redirect()->route('master.vendor-supir.index')
            ->with('success', 'Vendor Supir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorSupir $vendorSupir): RedirectResponse
    {
        $vendorSupir->delete();

        return redirect()->route('master.vendor-supir.index')
            ->with('success', 'Vendor Supir berhasil dihapus.');
    }
}

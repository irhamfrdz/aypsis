<?php

namespace App\Http\Controllers;

use App\Models\TagihanCat;
use App\Models\PerbaikanKontainer;
use App\Models\VendorBengkel;
use App\Models\PricelistCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanCatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TagihanCat::with(['creator', 'updater']);

        // Filter by tanggal_cat
        if ($request->filled('tanggal_cat')) {
            $query->whereDate('tanggal_cat', $request->tanggal_cat);
        }

        // Search across all relevant fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('nomor_tagihan_cat', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $tagihanCats = $query->orderBy('tanggal_cat', 'desc')
                            ->paginate(15);

        return view('tagihan-cat.index', compact('tagihanCats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tagihanCat = new TagihanCat();
        $vendors = PricelistCat::select('vendor')->distinct()->orderBy('vendor')->get();

        return view('tagihan-cat.create', compact('tagihanCat', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tagihan_cat' => 'nullable|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'tanggal_cat' => 'required|date',
            'estimasi_biaya_raw' => 'nullable|numeric|min:0',
            'realisasi_biaya_raw' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,paid,cancelled',
            'keterangan' => 'nullable|string',
        ]);

        // Use raw values for amount fields
        $validated['estimasi_biaya'] = $validated['estimasi_biaya_raw'] ?? null;
        $validated['realisasi_biaya'] = $validated['realisasi_biaya_raw'] ?? null;

        // Remove raw fields from validated data
        unset($validated['estimasi_biaya_raw'], $validated['realisasi_biaya_raw']);

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'pending';

        // Set created_by
        $validated['created_by'] = Auth::id();

        TagihanCat::create($validated);

        return redirect()->route('tagihan-cat.index')
                        ->with('success', 'Tagihan CAT berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TagihanCat $tagihanCat)
    {
        $tagihanCat->load(['creator', 'updater']);

        return view('tagihan-cat.show', compact('tagihanCat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TagihanCat $tagihanCat)
    {
        $vendors = PricelistCat::select('vendor')->distinct()->orderBy('vendor')->get();

        return view('tagihan-cat.edit', compact('tagihanCat', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TagihanCat $tagihanCat)
    {
        $validated = $request->validate([
            'nomor_tagihan_cat' => 'nullable|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'tanggal_cat' => 'required|date',
            'estimasi_biaya_raw' => 'nullable|numeric|min:0',
            'realisasi_biaya_raw' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'keterangan' => 'nullable|string',
        ]);

        // Use raw values for amount fields
        $validated['estimasi_biaya'] = $validated['estimasi_biaya_raw'] ?? null;
        $validated['realisasi_biaya'] = $validated['realisasi_biaya_raw'] ?? null;

        // Remove raw fields from validated data
        unset($validated['estimasi_biaya_raw'], $validated['realisasi_biaya_raw']);

        $validated['updated_by'] = Auth::id();

        $tagihanCat->update($validated);

        return redirect()->route('tagihan-cat.index')
                        ->with('success', 'Tagihan CAT berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TagihanCat $tagihanCat)
    {
        $tagihanCat->delete();

        return redirect()->route('tagihan-cat.index')
                        ->with('success', 'Tagihan CAT berhasil dihapus.');
    }

    /**
     * Bulk delete tagihan-cat records.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tagihan_cats,id'
        ]);

        $count = TagihanCat::whereIn('id', $request->ids)->delete();

        return redirect()->route('tagihan-cat.index')
                        ->with('success', "{$count} tagihan CAT berhasil dihapus.");
    }

    /**
     * Bulk update status for tagihan-cat records.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tagihan_cats,id',
            'status' => 'required|in:pending,masuk pranota,paid,cancelled'
        ]);

        $count = TagihanCat::whereIn('id', $request->ids)
                          ->update([
                              'status' => $request->status,
                              'updated_by' => Auth::id()
                          ]);

        $statusLabels = [
            'pending' => 'Pending',
            'masuk pranota' => 'Masuk Pranota',
            'paid' => 'Sudah Dibayar',
            'cancelled' => 'Dibatalkan'
        ];

        return redirect()->route('tagihan-cat.index')
                        ->with('success', "{$count} tagihan CAT berhasil diubah status menjadi {$statusLabels[$request->status]}.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PerbaikanKontainer;
use App\Models\Kontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PerbaikanKontainer::with(['creator', 'vendorBengkel']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_perbaikan', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_perbaikan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_perbaikan', '<=', $request->tanggal_sampai);
        }

        // Search across all relevant fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('deskripsi_perbaikan', 'like', "%{$search}%")
                  ->orWhere('nomor_tagihan', 'like', "%{$search}%")
                  ->orWhere('estimasi_kerusakan_kontainer', 'like', "%{$search}%")
                  ->orWhere('realisasi_kerusakan', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('status_perbaikan', 'like', "%{$search}%")
                  ->orWhereHas('vendorBengkel', function($vendorQuery) use ($search) {
                      $vendorQuery->where('nama_bengkel', 'like', "%{$search}%");
                  });
            });
        }

        $perbaikanKontainers = $query->orderBy('tanggal_perbaikan', 'desc')
                                   ->paginate(15);

        $stats = [
            'total' => PerbaikanKontainer::count(),
            'belum_masuk_pranota' => PerbaikanKontainer::where('status_perbaikan', 'belum_masuk_pranota')->count(),
            'sudah_masuk_pranota' => PerbaikanKontainer::where('status_perbaikan', 'sudah_masuk_pranota')->count(),
            'sudah_dibayar' => PerbaikanKontainer::where('status_perbaikan', 'sudah_dibayar')->count(),
        ];

        return view('perbaikan-kontainer.index', compact('perbaikanKontainers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $perbaikan = new PerbaikanKontainer();
        $vendorBengkels = \App\Models\VendorBengkel::select('id', 'nama_bengkel')->orderBy('nama_bengkel')->get();

        return view('perbaikan-kontainer.create', compact('perbaikan', 'vendorBengkels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tagihan' => 'nullable|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'tanggal_perbaikan' => 'required|date',
            'estimasi_kerusakan_kontainer' => 'required|string',
            'deskripsi_perbaikan' => 'required|string',
            'realisasi_kerusakan' => 'nullable|string',
            'estimasi_biaya_perbaikan_raw' => 'nullable|numeric|min:0',
            'realisasi_biaya_perbaikan_raw' => 'nullable|numeric|min:0',
            'vendor_bengkel_id' => 'nullable|exists:vendor_bengkel,id',
            'status_perbaikan' => 'nullable|string|in:belum_masuk_pranota,sudah_masuk_pranota,sudah_dibayar',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        // Use raw values for biaya fields
        $validated['estimasi_biaya_perbaikan'] = $validated['estimasi_biaya_perbaikan_raw'] ?? null;
        $validated['realisasi_biaya_perbaikan'] = $validated['realisasi_biaya_perbaikan_raw'] ?? null;

        // Remove raw fields from validated data
        unset($validated['estimasi_biaya_perbaikan_raw']);
        unset($validated['realisasi_biaya_perbaikan_raw']);

        // Set default status if not provided
        $validated['status_perbaikan'] = $validated['status_perbaikan'] ?? 'belum_masuk_pranota';

        // Set created_by
        $validated['created_by'] = Auth::id();

        PerbaikanKontainer::create($validated);

        return redirect()->route('perbaikan-kontainer.index')
                        ->with('success', 'Perbaikan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PerbaikanKontainer $perbaikanKontainer)
    {
        $perbaikanKontainer->load(['creator', 'updater', 'vendorBengkel']);

        return view('perbaikan-kontainer.show', compact('perbaikanKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PerbaikanKontainer $perbaikanKontainer)
    {
        $vendorBengkels = \App\Models\VendorBengkel::select('id', 'nama_bengkel')->orderBy('nama_bengkel')->get();

        return view('perbaikan-kontainer.edit', compact('perbaikanKontainer', 'vendorBengkels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PerbaikanKontainer $perbaikanKontainer)
    {
        $validated = $request->validate([
            'nomor_kontainer' => 'required|string|max:255',
            'nomor_tagihan' => 'nullable|string|max:255',
            'vendor_bengkel_id' => 'nullable|exists:vendor_bengkel,id',
            'tanggal_perbaikan' => 'required|date',
            'estimasi_kerusakan_kontainer' => 'required|string',
            'deskripsi_perbaikan' => 'required|string',
            'realisasi_kerusakan' => 'nullable|string',
            'estimasi_biaya_perbaikan_raw' => 'nullable|numeric|min:0',
            'realisasi_biaya_perbaikan_raw' => 'nullable|numeric|min:0',
            'tanggal_selesai' => 'nullable|date',
            'status_perbaikan' => 'required|in:belum_masuk_pranota,sudah_masuk_pranota,sudah_dibayar',
            'catatan' => 'nullable|string',
        ]);

        // Use raw values for biaya fields
        $validated['estimasi_biaya_perbaikan'] = $validated['estimasi_biaya_perbaikan_raw'] ?? null;
        $validated['realisasi_biaya_perbaikan'] = $validated['realisasi_biaya_perbaikan_raw'] ?? null;

        // Remove raw fields from validated data
        unset($validated['estimasi_biaya_perbaikan_raw']);
        unset($validated['realisasi_biaya_perbaikan_raw']);

        $validated['updated_by'] = Auth::id();

        // Set tanggal selesai jika status sudah_dibayar
        if ($validated['status_perbaikan'] === 'sudah_dibayar' && !$perbaikanKontainer->tanggal_selesai) {
            $validated['tanggal_selesai'] = now();
        }

        $perbaikanKontainer->update($validated);

        return redirect()->route('perbaikan-kontainer.index')
                        ->with('success', 'Perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerbaikanKontainer $perbaikanKontainer)
    {
        $perbaikanKontainer->delete();

        return redirect()->route('perbaikan-kontainer.index')
                        ->with('success', 'Perbaikan kontainer berhasil dihapus.');
    }

    /**
     * Update status perbaikan
     */
    public function updateStatus(Request $request, PerbaikanKontainer $perbaikanKontainer)
    {
        $validated = $request->validate([
            'status_perbaikan' => 'required|in:belum_masuk_pranota,sudah_masuk_pranota,sudah_dibayar',
            'catatan' => 'nullable|string',
        ]);

        $updateData = [
            'status_perbaikan' => $validated['status_perbaikan'],
            'updated_by' => Auth::id(),
        ];

        if ($validated['status_perbaikan'] === 'sudah_dibayar') {
            $updateData['tanggal_selesai'] = now();
        }

        if (isset($validated['catatan'])) {
            $updateData['catatan'] = $validated['catatan'];
        }

        $perbaikanKontainer->update($updateData);

        return redirect()->back()
                        ->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    /**
     * Bulk delete selected items
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:perbaikan_kontainers,id'
        ]);

        $count = PerbaikanKontainer::whereIn('id', $request->ids)->delete();

        return redirect()->back()
                        ->with('success', "{$count} data perbaikan berhasil dihapus.");
    }

    /**
     * Bulk update status for selected items
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:perbaikan_kontainers,id',
            'status' => 'required|in:belum_masuk_pranota,sudah_masuk_pranota,sudah_dibayar'
        ]);

        $count = PerbaikanKontainer::whereIn('id', $request->ids)
                                  ->update(['status_perbaikan' => $request->status]);

        return redirect()->back()
                        ->with('success', "Status {$count} data perbaikan berhasil diperbarui.");
    }

    /**
     * Bulk update status to "sudah_masuk_pranota" for selected items
     */
    public function bulkPranota(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:perbaikan_kontainers,id'
        ]);

        $count = PerbaikanKontainer::whereIn('id', $request->ids)
                                  ->where('status_perbaikan', 'belum_masuk_pranota')
                                  ->update(['status_perbaikan' => 'sudah_masuk_pranota']);

        return redirect()->back()
                        ->with('success', "{$count} data perbaikan berhasil dimasukkan ke pranota.");
    }
}

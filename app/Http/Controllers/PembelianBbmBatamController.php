<?php

namespace App\Http\Controllers;

use App\Models\PembelianBbmBatam;
use Illuminate\Http\Request;

class PembelianBbmBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembelianBbmBatam::with(['createdBy', 'updatedBy'])->latest('tanggal');

        // Search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_bukti', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('nomor_nota', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->has('start_date') && ! empty($request->start_date)) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && ! empty($request->end_date)) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Get total statistics for summary (using the filtered query but without pagination)
        $statsQuery = clone $query;
        $totalLiters = $statsQuery->sum('jumlah_liter');
        $totalCost = $statsQuery->sum('total_harga');
        $averagePrice = $totalLiters > 0 ? $totalCost / $totalLiters : 0;

        $items = $query->paginate(20);

        return view('pembelian-bbm-batam.index', compact('items', 'totalLiters', 'totalCost', 'averagePrice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextInvoice = PembelianBbmBatam::generateNextInvoice();

        return view('pembelian-bbm-batam.create', compact('nextInvoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jumlah_liter' => 'required|numeric|min:0.01',
            'harga_per_liter' => 'required|numeric|min:0.01',
            'supplier' => 'nullable|string|max:255',
            'nomor_nota' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['nomor_bukti'] = PembelianBbmBatam::generateNextInvoice();
        $validated['total_harga'] = $validated['jumlah_liter'] * $validated['harga_per_liter'];
        $validated['created_by'] = auth()->id();

        PembelianBbmBatam::create($validated);

        return redirect()->route('pembelian-bbm-batam.index')
            ->with('success', 'Data pembelian BBM Batam berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = PembelianBbmBatam::findOrFail($id);

        return view('pembelian-bbm-batam.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = PembelianBbmBatam::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jumlah_liter' => 'required|numeric|min:0.01',
            'harga_per_liter' => 'required|numeric|min:0.01',
            'supplier' => 'nullable|string|max:255',
            'nomor_nota' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['total_harga'] = $validated['jumlah_liter'] * $validated['harga_per_liter'];
        $validated['updated_by'] = auth()->id();

        $item->update($validated);

        return redirect()->route('pembelian-bbm-batam.index')
            ->with('success', 'Data pembelian BBM Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = PembelianBbmBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('pembelian-bbm-batam.index')
            ->with('success', 'Data pembelian BBM Batam berhasil dihapus.');
    }
}

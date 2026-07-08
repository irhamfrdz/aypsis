<?php

namespace App\Http\Controllers;

use App\Models\MasterChasisBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterChasisBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterChasisBatam::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('kondisi', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Filter by kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Filter by lokasi
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $chasisList = $query->latest()->paginate(15)->appends($request->query());

        // Get distinct types for filter dropdown
        $types = MasterChasisBatam::whereNotNull('tipe')
            ->where('tipe', '!=', '')
            ->distinct()
            ->orderBy('tipe')
            ->pluck('tipe');

        return view('master-chasis-batam.index', compact('chasisList', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate next automatic code if wanted, e.g. CH-001
        $nextKode = $this->generateNextKode();

        return view('master-chasis-batam.create', compact('nextKode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_chasis_batams,kode',
            'tipe' => 'nullable|string|max:50',
            'kondisi' => 'required|string|in:baik,rusak',
            'lokasi' => 'required|string|in:sm,relasi',
            'tanggal_terakhir_pakai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        MasterChasisBatam::create($validated);

        return redirect()->route('master.chasis-batam.index')
            ->with('success', 'Chasis Batam berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterChasisBatam $chasisBatam)
    {
        return view('master-chasis-batam.show', compact('chasisBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterChasisBatam $chasisBatam)
    {
        return view('master-chasis-batam.edit', compact('chasisBatam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterChasisBatam $chasisBatam)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_chasis_batams,kode,'.$chasisBatam->id,
            'tipe' => 'nullable|string|max:50',
            'kondisi' => 'required|string|in:baik,rusak',
            'lokasi' => 'required|string|in:sm,relasi',
            'tanggal_terakhir_pakai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $chasisBatam->update($validated);

        return redirect()->route('master.chasis-batam.index')
            ->with('success', 'Chasis Batam berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterChasisBatam $chasisBatam)
    {
        $chasisBatam->delete();

        return redirect()->route('master.chasis-batam.index')
            ->with('success', 'Chasis Batam berhasil dihapus!');
    }

    /**
     * Generate automatic sequential code for new chassis (e.g. CH-0001)
     */
    private function generateNextKode()
    {
        $latest = MasterChasisBatam::withTrashed()->orderBy('id', 'desc')->first();
        if (! $latest) {
            return 'CH-0001';
        }

        $latestKode = $latest->kode;
        if (preg_match('/CH-(\d+)/', $latestKode, $matches)) {
            $number = intval($matches[1]) + 1;

            return 'CH-'.str_pad($number, 4, '0', STR_PAD_LEFT);
        }

        return 'CH-'.str_pad($latest->id + 1, 4, '0', STR_PAD_LEFT);
    }
}

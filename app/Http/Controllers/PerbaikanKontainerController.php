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
        $query = PerbaikanKontainer::with(['kontainer', 'creator']);

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

        // Search by kontainer number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('kontainer', function($kontainer) use ($search) {
                    $kontainer->where('nomor_kontainer', 'like', "%{$search}%");
                })
                ->orWhere('deskripsi_perbaikan', 'like', "%{$search}%");
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

        return view('perbaikan-kontainer.create', compact('perbaikan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kontainer' => 'required|string|max:255',
            'tanggal_perbaikan' => 'required|date',
            'jenis_perbaikan' => 'required|string',
            'deskripsi_perbaikan' => 'required|string',
            'biaya_perbaikan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Find or create container based on nomor_kontainer
        $kontainer = Kontainer::firstOrCreate(
            ['nomor_kontainer' => $validated['nomor_kontainer']],
            ['ukuran' => '20ft', 'status_kontainer' => 'baik'] // Default values for new containers
        );

        $validated['kontainer_id'] = $kontainer->id;
        unset($validated['nomor_kontainer']); // Remove nomor_kontainer from validated data

        $validated['created_by'] = Auth::id();
        $validated['status_perbaikan'] = 'belum_masuk_pranota';
        $validated['nomor_memo_perbaikan'] = PerbaikanKontainer::generateNomorMemoPerbaikan();

        PerbaikanKontainer::create($validated);

        return redirect()->route('perbaikan-kontainer.index')
                        ->with('success', 'Perbaikan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PerbaikanKontainer $perbaikanKontainer)
    {
        $perbaikanKontainer->load(['kontainer', 'creator', 'updater']);

        return view('perbaikan-kontainer.show', compact('perbaikanKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PerbaikanKontainer $perbaikanKontainer)
    {
        return view('perbaikan-kontainer.edit', compact('perbaikanKontainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PerbaikanKontainer $perbaikanKontainer)
    {
        $validated = $request->validate([
            'nomor_kontainer' => 'required|string|max:255',
            'tanggal_perbaikan' => 'required|date',
            'jenis_perbaikan' => 'required|string',
            'deskripsi_perbaikan' => 'required|string',
            'biaya_perbaikan' => 'nullable|numeric|min:0',
            'status_perbaikan' => 'required|in:belum_masuk_pranota,sudah_masuk_pranota,sudah_dibayar',
            'catatan' => 'nullable|string',
            'tanggal_selesai' => 'nullable|date',
        ]);

        // Find or create container based on nomor_kontainer
        $kontainer = Kontainer::firstOrCreate(
            ['nomor_kontainer' => $validated['nomor_kontainer']],
            ['ukuran' => '20ft', 'status_kontainer' => 'baik'] // Default values for new containers
        );

        $validated['kontainer_id'] = $kontainer->id;
        unset($validated['nomor_kontainer']); // Remove nomor_kontainer from validated data

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
}

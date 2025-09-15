<?php

namespace App\Http\Controllers;

use App\Models\PranotaPerbaikanKontainer;
use App\Models\PerbaikanKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaPerbaikanKontainer::with(['perbaikanKontainer.kontainer', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_pranota', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_pranota', '<=', $request->tanggal_sampai);
        }

        // Search by kontainer number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('perbaikanKontainer.kontainer', function($kontainer) use ($search) {
                    $kontainer->where('nomor_kontainer', 'like', "%{$search}%");
                })
                ->orWhere('deskripsi_pekerjaan', 'like', "%{$search}%")
                ->orWhere('nama_teknisi', 'like', "%{$search}%");
            });
        }

        $pranotaPerbaikanKontainers = $query->orderBy('tanggal_pranota', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('pranota-perbaikan-kontainer.index', compact('pranotaPerbaikanKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $perbaikanKontainers = PerbaikanKontainer::where('status_perbaikan', 'pending')
            ->orWhere('status_perbaikan', 'in_progress')
            ->with('kontainer')
            ->get();

        return view('pranota-perbaikan-kontainer.create', compact('perbaikanKontainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perbaikan_kontainer_id' => 'required|exists:perbaikan_kontainers,id',
            'tanggal_pranota' => 'required|date',
            'deskripsi_pekerjaan' => 'required|string',
            'nama_teknisi' => 'required|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'estimasi_waktu' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        PranotaPerbaikanKontainer::create($data);

        return redirect()->route('pranota-perbaikan-kontainer.index')
            ->with('success', 'Pranota perbaikan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        $pranotaPerbaikanKontainer->load(['perbaikanKontainer.kontainer', 'creator']);

        return view('pranota-perbaikan-kontainer.show', compact('pranotaPerbaikanKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        $perbaikanKontainers = PerbaikanKontainer::where('status_perbaikan', 'pending')
            ->orWhere('status_perbaikan', 'in_progress')
            ->with('kontainer')
            ->get();

        return view('pranota-perbaikan-kontainer.edit', compact('pranotaPerbaikanKontainer', 'perbaikanKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        $request->validate([
            'perbaikan_kontainer_id' => 'required|exists:perbaikan_kontainers,id',
            'tanggal_pranota' => 'required|date',
            'deskripsi_pekerjaan' => 'required|string',
            'nama_teknisi' => 'required|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'estimasi_waktu' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:draft,approved,rejected,completed',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $pranotaPerbaikanKontainer->update($data);

        return redirect()->route('pranota-perbaikan-kontainer.index')
            ->with('success', 'Pranota perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        $pranotaPerbaikanKontainer->delete();

        return redirect()->route('pranota-perbaikan-kontainer.index')
            ->with('success', 'Pranota perbaikan kontainer berhasil dihapus.');
    }
}

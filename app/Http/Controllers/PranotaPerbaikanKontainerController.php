<?php

namespace App\Http\Controllers;

use App\Models\PranotaPerbaikanKontainer;
use App\Models\PerbaikanKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaPerbaikanKontainer::with(['perbaikanKontainers.kontainer', 'creator']);

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
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('perbaikanKontainers', function($perbaikan) use ($search) {
                      $perbaikan->where('nomor_kontainer', 'like', "%{$search}%")
                               ->orWhere('nomor_tagihan', 'like', "%{$search}%");
                  })
                  ->orWhere('deskripsi_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('nama_teknisi', 'like', "%{$search}%");
            });
        }

        $pranotaPerbaikanKontainers = $query->orderBy('tanggal_pranota', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Get stats for dashboard cards
        $stats = [
            'total' => PranotaPerbaikanKontainer::count(),
            'draft' => PranotaPerbaikanKontainer::where('status', 'draft')->count(),
            'approved' => PranotaPerbaikanKontainer::where('status', 'approved')->count(),
            'completed' => PranotaPerbaikanKontainer::where('status', 'completed')->count(),
        ];

        return view('pranota-perbaikan-kontainer.index', compact('pranotaPerbaikanKontainers', 'stats'));
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
        // Check if this is a bulk submission from modal
        if ($request->has('perbaikan_ids')) {
            $request->validate([
                'perbaikan_ids' => 'required|string', // JSON string of IDs
                'nomor_pranota' => 'required|string',
                'tanggal_pranota' => 'required|date',
                'supplier' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);

            $perbaikanIds = json_decode($request->perbaikan_ids, true);

            if (!is_array($perbaikanIds) || empty($perbaikanIds)) {
                return redirect()->back()->with('error', 'Tidak ada item perbaikan yang dipilih.');
            }

            // Get all perbaikan data
            $perbaikans = PerbaikanKontainer::whereIn('id', $perbaikanIds)->get();

            if ($perbaikans->isEmpty()) {
                return redirect()->back()->with('error', 'Data perbaikan tidak ditemukan.');
            }

            // Calculate total biaya from all perbaikan items
            $totalBiaya = $perbaikans->sum('realisasi_biaya_perbaikan');

            // Create single pranota
            $pranota = PranotaPerbaikanKontainer::create([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'deskripsi_pekerjaan' => 'Perbaikan kontainer bulk - ' . $perbaikans->count() . ' item',
                'nama_teknisi' => $request->supplier ?? 'Supplier',
                'total_biaya' => $totalBiaya,
                'catatan' => $request->catatan,
                'status' => 'draft',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Attach all perbaikan to the pranota
            $pranota->perbaikanKontainers()->attach($perbaikanIds);

            // Update status perbaikan to "sudah_masuk_pranota"
            PerbaikanKontainer::whereIn('id', $perbaikanIds)
                ->update(['status_perbaikan' => 'sudah_masuk_pranota']);

            return redirect()->route('pranota-perbaikan-kontainer.index')
                ->with('success', 'Pranota berhasil dibuat dengan ' . count($perbaikanIds) . ' item perbaikan.');
        }

        // Handle single pranota creation (if needed for backward compatibility)
        return redirect()->back()->with('error', 'Gunakan fitur bulk pranota untuk membuat pranota baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        if (!Gate::allows('pranota-perbaikan-kontainer-view')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat detail pranota.');
        }

        $pranotaPerbaikanKontainer->load(['perbaikanKontainers.kontainer', 'creator']);

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
            'total_biaya' => 'required|numeric|min:0',
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
        if (!Gate::allows('pranota-perbaikan-kontainer-delete')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menghapus pranota.');
        }

        $pranotaPerbaikanKontainer->delete();

        return redirect()->route('pranota-perbaikan-kontainer.index')
            ->with('success', 'Pranota perbaikan kontainer berhasil dihapus.');
    }

    /**
     * Print the specified pranota.
     */
    public function print(PranotaPerbaikanKontainer $pranotaPerbaikanKontainer)
    {
        if (!Gate::allows('pranota-perbaikan-kontainer-print')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mencetak pranota.');
        }

        $pranotaPerbaikanKontainer->load(['perbaikanKontainers.kontainer', 'creator']);

        return view('pranota-perbaikan-kontainer.print', compact('pranotaPerbaikanKontainer'));
    }
}

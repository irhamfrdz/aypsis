<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaPerbaikanKontainer;
use App\Models\PranotaPerbaikanKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaPerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranPranotaPerbaikanKontainer::with(['pranotaPerbaikanKontainer.perbaikanKontainer.kontainer', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_pembayaran', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_pembayaran', '<=', $request->tanggal_sampai);
        }

        // Search by kontainer number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pranotaPerbaikanKontainer.perbaikanKontainer.kontainer', function($kontainer) use ($search) {
                    $kontainer->where('nomor_kontainer', 'like', "%{$search}%");
                })
                ->orWhere('nomor_invoice', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pembayaranPranotaPerbaikanKontainers = $query->orderBy('tanggal_pembayaran', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('pembayaran-pranota-perbaikan-kontainer.index', compact('pembayaranPranotaPerbaikanKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pranotaPerbaikanKontainers = PranotaPerbaikanKontainer::where('status', 'approved')
            ->whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
            ->with(['perbaikanKontainers.kontainer'])
            ->get();

        $akunCoa = \App\Models\Coa::all();

        return view('pembayaran-pranota-perbaikan-kontainer.create', compact('pranotaPerbaikanKontainers', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pranota_perbaikan_kontainer_id' => 'required|exists:pranota_perbaikan_kontainers,id',
            'tanggal_pembayaran' => 'required|date',
            'nominal_pembayaran' => 'required|numeric|min:0',
            'nomor_invoice' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'status_pembayaran' => 'required|in:pending,completed,cancelled',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        PembayaranPranotaPerbaikanKontainer::create($data);

        return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
            ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pembayaran->load(['pranotaPerbaikanKontainer.perbaikanKontainer.kontainer', 'creator']);

        return view('pembayaran-pranota-perbaikan-kontainer.show', compact('pembayaran'));
    }

    /**
     * Display the specified resource for printing.
     */
    public function print(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        return view('pembayaran-pranota-perbaikan-kontainer.print', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pranotaPerbaikanKontainers = PranotaPerbaikanKontainer::where('status', 'approved')
            ->with(['perbaikanKontainer.kontainer'])
            ->get();

        return view('pembayaran-pranota-perbaikan-kontainer.edit', compact('pembayaran', 'pranotaPerbaikanKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $request->validate([
            'pranota_perbaikan_kontainer_id' => 'required|exists:pranota_perbaikan_kontainers,id',
            'tanggal_pembayaran' => 'required|date',
            'nominal_pembayaran' => 'required|numeric|min:0',
            'nomor_invoice' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'status_pembayaran' => 'required|in:pending,completed,cancelled',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $pembayaran->update($data);

        return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
            ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranPranotaPerbaikanKontainer $pembayaran)
    {
        $pembayaran->delete();

        return redirect()->route('pembayaran-pranota-perbaikan-kontainer.index')
            ->with('success', 'Pembayaran pranota perbaikan kontainer berhasil dihapus.');
    }
}

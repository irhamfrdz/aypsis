<?php

namespace App\Http\Controllers;

use App\Models\PerbaikanKontainer;
use App\Models\VendorBengkel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerbaikanKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PerbaikanKontainer::with('bengkel');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_perbaikan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('vendor_bengkel_id')) {
            $query->where('vendor_bengkel_id', $request->input('vendor_bengkel_id'));
        }

        if ($request->filled('tanggal_masuk_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->input('tanggal_masuk_start'));
        }

        if ($request->filled('tanggal_masuk_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->input('tanggal_masuk_end'));
        }

        $perbaikanKontainers = $query->orderBy('created_at', 'desc')->paginate(15);
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.index', compact('perbaikanKontainers', 'bengkels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.create', compact('bengkels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'ukuran' => 'nullable|string|max:10',
            'tipe_kontainer' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'vendor_bengkel_id' => 'required|exists:vendor_bengkel,id',
            'keterangan_kerusakan' => 'required|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'status' => 'required|in:pending,proses,selesai,batal',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // If status is selesai, default real cost to estimate if not provided, and date out to today
        if ($data['status'] === 'selesai') {
            $data['tanggal_keluar'] = $data['tanggal_keluar'] ?? now()->format('Y-m-d');
            $data['biaya_riil'] = $data['biaya_riil'] ?? $data['estimasi_biaya'];
        }

        PerbaikanKontainer::create($data);

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $perbaikanKontainer = PerbaikanKontainer::with(['bengkel', 'creator', 'updater'])->findOrFail($id);

        return view('perbaikan-kontainer.show', compact('perbaikanKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $perbaikanKontainer = PerbaikanKontainer::findOrFail($id);
        $bengkels = VendorBengkel::all();

        return view('perbaikan-kontainer.edit', compact('perbaikanKontainer', 'bengkels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $perbaikan = PerbaikanKontainer::findOrFail($id);

        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'ukuran' => 'nullable|string|max:10',
            'tipe_kontainer' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required_if:status,selesai|nullable|date|after_or_equal:tanggal_masuk',
            'vendor_bengkel_id' => 'required|exists:vendor_bengkel,id',
            'keterangan_kerusakan' => 'required|string',
            'keterangan_perbaikan' => 'required_if:status,selesai|nullable|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'biaya_riil' => 'required_if:status,selesai|nullable|numeric|min:0',
            'status' => 'required|in:pending,proses,selesai,batal',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle auto-completion of selesai status
        if ($data['status'] === 'selesai') {
            $data['tanggal_keluar'] = $data['tanggal_keluar'] ?? now()->format('Y-m-d');
            $data['biaya_riil'] = $data['biaya_riil'] ?? $perbaikan->estimasi_biaya;
        }

        $perbaikan->update($data);

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perbaikan = PerbaikanKontainer::findOrFail($id);
        $perbaikan->delete();

        return redirect()->route('perbaikan-kontainer.index')
            ->with('success', 'Data perbaikan kontainer berhasil dihapus.');
    }
}

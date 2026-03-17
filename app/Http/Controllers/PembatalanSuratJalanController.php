<?php

namespace App\Http\Controllers;

use App\Models\PembatalanSuratJalan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembatalanSuratJalanController extends Controller
{
    /**
     * Display a listing of Pembatalan.
     */
    public function index(Request $request)
    {
        $query = PembatalanSuratJalan::with(['suratJalan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('alasan_batal', 'like', "%{$search}%");
            });
        }

        $pembatalans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('pembatalan-surat-jalan.index', compact('pembatalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // List cancelable surat jalans
        $query = \App\Models\SuratJalan::where('status', '!=', 'cancelled');
        
        if ($request->filled('search_sj')) {
            $query->where('no_surat_jalan', 'like', "%{$request->search_sj}%");
        }

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pembatalan-surat-jalan.create', compact('suratJalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            'alasan_batal' => 'required'
        ]);

        $suratJalan = \App\Models\SuratJalan::findOrFail($request->surat_jalan_id);

        if ($suratJalan->status === 'cancelled') {
            return redirect()->back()->with('error', 'Surat Jalan sudah dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($request, $suratJalan) {
            // Create Cancel Record
            PembatalanSuratJalan::create([
                'surat_jalan_id' => $suratJalan->id,
                'no_surat_jalan' => $suratJalan->no_surat_jalan,
                'alasan_batal' => $request->alasan_batal,
                'status' => 'approved', // auto-approve to cancel immediately
                'created_by' => auth()->id(),
            ]);

            // Update Surat Jalan
            $suratJalan->update(['status' => 'cancelled']);

            // Update Linked Prospeks
            if ($suratJalan->prospeks()->exists()) {
                $suratJalan->prospeks()->update(['status' => \App\Models\Prospek::STATUS_BATAL]);
            }
        });

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Surat Jalan berhasil dibatalkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $pembatalanSuratJalan->load('suratJalan');
        return view('pembatalan-surat-jalan.show', compact('pembatalanSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        return view('pembatalan-surat-jalan.edit', compact('pembatalanSuratJalan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $request->validate([
            'alasan_batal' => 'required'
        ]);

        $pembatalanSuratJalan->update([
            'alasan_batal' => $request->alasan_batal,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Catatan pembatalan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        return redirect()->route('pembatalan-surat-jalan.index')->with('error', 'Penghapusan transaksional dibatasi.');
    }
}

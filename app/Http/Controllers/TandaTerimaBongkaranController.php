<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TandaTerimaBongkaran;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\DB;

class TandaTerimaBongkaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalanBongkaran::with(['bl', 'uangJalanBongkaran']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'LIKE', "%{$search}%")
                  ->orWhere('no_kontainer', 'LIKE', "%{$search}%")
                  ->orWhere('no_seal', 'LIKE', "%{$search}%")
                  ->orWhere('no_bl', 'LIKE', "%{$search}%")
                  ->orWhere('pengirim', 'LIKE', "%{$search}%");
            });
        }

        // Status Pembayaran filter
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Kegiatan filter
        if ($request->filled('kegiatan')) {
            $query->where('kegiatan', $request->kegiatan);
        }

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tanda-terima-bongkaran.index', compact('suratJalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suratJalans = SuratJalanBongkaran::with(['bl'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tanda-terima-bongkaran.create', compact('suratJalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_bongkarans,nomor_tanda_terima',
            'tanggal_tanda_terima' => 'required|date',
            'surat_jalan_bongkaran_id' => 'required|exists:surat_jalan_bongkarans,id',
            'no_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'kegiatan' => 'required|string|in:muat,bongkar,stuffing,stripping',
            'status' => 'required|string|in:pending,approved,completed',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $tandaTerima = TandaTerimaBongkaran::create($validated);

            DB::commit();

            return redirect()
                ->route('tanda-terima-bongkaran.index')
                ->with('success', 'Tanda terima bongkaran berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TandaTerimaBongkaran $tandaTerimaBongkaran)
    {
        $tandaTerimaBongkaran->load(['suratJalanBongkaran.bl']);

        return view('tanda-terima-bongkaran.show', compact('tandaTerimaBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TandaTerimaBongkaran $tandaTerimaBongkaran)
    {
        $suratJalans = SuratJalanBongkaran::with(['bl'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tanda-terima-bongkaran.edit', compact('tandaTerimaBongkaran', 'suratJalans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TandaTerimaBongkaran $tandaTerimaBongkaran)
    {
        $validated = $request->validate([
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_bongkarans,nomor_tanda_terima,' . $tandaTerimaBongkaran->id,
            'tanggal_tanda_terima' => 'required|date',
            'surat_jalan_bongkaran_id' => 'required|exists:surat_jalan_bongkarans,id',
            'no_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'kegiatan' => 'required|string|in:muat,bongkar,stuffing,stripping',
            'status' => 'required|string|in:pending,approved,completed',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $tandaTerimaBongkaran->update($validated);

            DB::commit();

            return redirect()
                ->route('tanda-terima-bongkaran.index')
                ->with('success', 'Tanda terima bongkaran berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TandaTerimaBongkaran $tandaTerimaBongkaran)
    {
        try {
            $tandaTerimaBongkaran->delete();

            return redirect()
                ->route('tanda-terima-bongkaran.index')
                ->with('success', 'Tanda terima bongkaran berhasil dihapus!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Print tanda terima bongkaran
     */
    public function print(TandaTerimaBongkaran $tandaTerimaBongkaran)
    {
        $tandaTerimaBongkaran->load(['suratJalanBongkaran.bl']);

        return view('tanda-terima-bongkaran.print', compact('tandaTerimaBongkaran'));
    }

    /**
     * Export tanda terima bongkaran to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export
        return back()->with('info', 'Fitur export Excel akan segera tersedia.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceAktivitasLain;
use App\Models\Karyawan;
use App\Models\Mobil;

class InvoiceAktivitasLainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InvoiceAktivitasLain::query();

        // Filter by nomor_invoice
        if ($request->filled('nomor_invoice')) {
            $query->where('nomor_invoice', 'like', '%' . $request->nomor_invoice . '%');
        }

        // Filter by jenis_aktivitas
        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', 'like', '%' . $request->jenis_aktivitas . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $invoices = $query->paginate(20)->withQueryString();

        return view('invoice-aktivitas-lain.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        $mobils = Mobil::orderBy('nomor_polisi', 'asc')->get();
        
        // Get voyages from both bls and pergerakan_kapal tables
        $voyagesBl = \DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage')
            ->get();
            
        $voyagesPergerakan = \DB::table('pergerakan_kapal')
            ->select('voyage', 'nama_kapal')
            ->whereNotNull('voyage')
            ->where('voyage', '!=', '')
            ->distinct()
            ->orderBy('voyage')
            ->get();
            
        // Combine and deduplicate voyages
        $allVoyages = collect();
        
        foreach ($voyagesBl as $voyage) {
            $allVoyages->push((object)[
                'voyage' => $voyage->voyage,
                'nama_kapal' => $voyage->nama_kapal,
                'source' => 'BL'
            ]);
        }
        
        foreach ($voyagesPergerakan as $voyage) {
            // Only add if not already exists
            $exists = $allVoyages->where('voyage', $voyage->voyage)
                                ->where('nama_kapal', $voyage->nama_kapal)
                                ->first();
            if (!$exists) {
                $allVoyages->push((object)[
                    'voyage' => $voyage->voyage,
                    'nama_kapal' => $voyage->nama_kapal,
                    'source' => 'Pergerakan Kapal'
                ]);
            }
        }
        
        $voyages = $allVoyages->sortBy('voyage')->values();
        
        // Get surat jalans for adjustment payments
        $suratJalans = \DB::table('surat_jalans')
            ->select('id', 'no_surat_jalan', 'tujuan_pengiriman', 'uang_jalan')
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->orderBy('no_surat_jalan')
            ->get();
        
        return view('invoice-aktivitas-lain.create', compact('karyawans', 'mobils', 'voyages', 'suratJalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_invoice' => 'required|string|max:255|unique:invoice_aktivitas_lain,nomor_invoice',
            'tanggal_invoice' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'sub_jenis_kendaraan' => 'nullable|string',
            'nomor_polisi' => 'nullable|string',
            'nomor_voyage' => 'nullable|string',
            'surat_jalan_id' => 'nullable|integer',
            'jenis_penyesuaian' => 'nullable|string',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required_with:tipe_penyesuaian_detail|string',
            'tipe_penyesuaian_detail.*.nominal' => 'required_with:tipe_penyesuaian_detail|numeric|min:0',
            'penerima' => 'required|string',
            'total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);
        
        // Convert tipe_penyesuaian_detail array to JSON for storage
        if (isset($validated['tipe_penyesuaian_detail'])) {
            $validated['tipe_penyesuaian'] = json_encode($validated['tipe_penyesuaian_detail']);
            unset($validated['tipe_penyesuaian_detail']);
        }

        // Set default status
        $validated['status'] = 'draft';

        InvoiceAktivitasLain::create($validated);

        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('invoice-aktivitas-lain.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('invoice-aktivitas-lain.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implement update logic
        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement delete logic
        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Print invoice
     */
    public function print(string $id)
    {
        return view('invoice-aktivitas-lain.print', compact('id'));
    }
}

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
        $query = InvoiceAktivitasLain::query()->with(['klasifikasiBiaya']);

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
        
        // Get surat jalans for adjustment payments from surat_jalans table
        $suratJalansRegular = \DB::table('surat_jalans')
            ->select(
                'id',
                'no_surat_jalan',
                'tujuan_pengiriman',
                'uang_jalan',
                \DB::raw("'regular' as source")
            )
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->get();
        
        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = \DB::table('surat_jalan_bongkarans')
            ->select(
                'id',
                \DB::raw('nomor_surat_jalan as no_surat_jalan'),
                'tujuan_pengiriman',
                'uang_jalan',
                \DB::raw("'bongkar' as source")
            )
            ->whereNotNull('nomor_surat_jalan')
            ->where('nomor_surat_jalan', '!=', '')
            ->get();
        
        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();
        
        // Get BLs for pembayaran kapal
        $bls = \DB::table('bls')
            ->select('id', 'nomor_bl', 'nomor_kontainer', 'pengirim')
            ->whereNotNull('nomor_bl')
            ->where('nomor_bl', '!=', '')
            ->orderBy('nomor_bl')
            ->get();
        
        // Get klasifikasi biaya for pembayaran kapal
        $klasifikasiBiayas = \DB::table('klasifikasi_biayas')
            ->select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
        
        // Get pricelist buruh for pembayaran kapal with klasifikasi biaya "buruh"
        $pricelistBuruh = \DB::table('pricelist_buruh')
            ->select('id', 'barang', 'size', 'tipe', 'tarif')
            ->where('is_active', true)
            ->orderBy('barang')
            ->get();
        
        // Get list of penerima from karyawan for detail pembayaran dropdown
        $penerimaList = Karyawan::orderBy('nama_lengkap', 'asc')
            ->pluck('nama_lengkap')
            ->unique()
            ->values()
            ->toArray();
        
        return view('invoice-aktivitas-lain.create', compact('karyawans', 'mobils', 'voyages', 'suratJalans', 'bls', 'klasifikasiBiayas', 'pricelistBuruh', 'penerimaList'));
    }

    /**
     * Get next invoice number (AJAX endpoint)
     */
    public function getNextInvoiceNumber()
    {
        try {
            $now = now();
            $month = $now->format('m');
            $year = $now->format('y');
            $prefix = "IAL-{$month}-{$year}-";
            
            // Get last invoice number for current month and year
            $lastInvoice = InvoiceAktivitasLain::where('nomor_invoice', 'like', $prefix . '%')
                ->orderBy('nomor_invoice', 'desc')
                ->first();
            
            if ($lastInvoice) {
                // Extract running number from last invoice
                $lastNumber = substr($lastInvoice->nomor_invoice, -6);
                $nextNumber = str_pad((int)$lastNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                // First invoice of the month
                $nextNumber = '000001';
            }
            
            $invoiceNumber = $prefix . $nextNumber;
            
            return response()->json([
                'success' => true,
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor invoice: ' . $e->getMessage()
            ], 500);
        }
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
            'bl_details' => 'nullable|array',
            'bl_details.*.bl_id' => 'nullable|integer|exists:bls,id',
            'klasifikasi_biaya_id' => 'nullable|integer|exists:klasifikasi_biayas,id',
            'barang_detail' => 'nullable|array',
            'barang_detail.*.pricelist_buruh_id' => 'required_with:barang_detail|integer|exists:pricelist_buruh,id',
            'barang_detail.*.jumlah' => 'required_with:barang_detail|numeric|min:0',
            'surat_jalan_id' => 'nullable|integer',
            'jenis_penyesuaian' => 'nullable|string',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required_with:tipe_penyesuaian_detail|string',
            'tipe_penyesuaian_detail.*.nominal' => 'required_with:tipe_penyesuaian_detail|numeric|min:0',
            'detail_pembayaran' => 'nullable|array',
            'detail_pembayaran.*.jenis_biaya' => 'nullable|string',
            'detail_pembayaran.*.biaya' => 'nullable|string',
            'detail_pembayaran.*.keterangan' => 'nullable|string',
            'detail_pembayaran.*.tanggal_kas' => 'nullable|date',
            'detail_pembayaran.*.no_bukti' => 'nullable|string',
            'detail_pembayaran.*.penerima' => 'nullable|string',
            'penerima' => 'required|string',
            'total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);
        
        // Convert bl_details array to JSON for storage
        if (isset($validated['bl_details'])) {
            $validated['bl_details'] = json_encode($validated['bl_details']);
        }
        
        // Convert barang_detail array to JSON for storage
        if (isset($validated['barang_detail'])) {
            $validated['barang_detail'] = json_encode($validated['barang_detail']);
        }
        
        // Convert tipe_penyesuaian_detail array to JSON for storage
        if (isset($validated['tipe_penyesuaian_detail'])) {
            $validated['tipe_penyesuaian'] = json_encode($validated['tipe_penyesuaian_detail']);
            unset($validated['tipe_penyesuaian_detail']);
        }
        
        // Convert detail_pembayaran array to JSON for storage
        if (isset($validated['detail_pembayaran'])) {
            // Clean up biaya values - remove currency formatting
            foreach ($validated['detail_pembayaran'] as &$detail) {
                if (isset($detail['biaya'])) {
                    $detail['biaya'] = str_replace(['.', ','], '', $detail['biaya']);
                }
            }
            $validated['detail_pembayaran'] = json_encode($validated['detail_pembayaran']);
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
        $invoice = InvoiceAktivitasLain::with(['klasifikasiBiaya', 'pembayarans', 'creator'])
            ->findOrFail($id);
        
        return view('invoice-aktivitas-lain.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = InvoiceAktivitasLain::with(['klasifikasiBiaya', 'suratJalan'])->findOrFail($id);
        
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
        
        // Get surat jalans for adjustment payments from surat_jalans table
        $suratJalansRegular = \DB::table('surat_jalans')
            ->select(
                'id',
                'no_surat_jalan',
                'tujuan_pengiriman',
                'uang_jalan',
                \DB::raw("'regular' as source")
            )
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->get();
        
        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = \DB::table('surat_jalan_bongkarans')
            ->select(
                'id',
                \DB::raw('nomor_surat_jalan as no_surat_jalan'),
                'tujuan_pengiriman',
                'uang_jalan',
                \DB::raw("'bongkar' as source")
            )
            ->whereNotNull('nomor_surat_jalan')
            ->where('nomor_surat_jalan', '!=', '')
            ->get();
        
        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();
        
        // Get BLs for pembayaran kapal
        $bls = \DB::table('bls')
            ->select('id', 'nomor_bl', 'nomor_kontainer', 'pengirim')
            ->whereNotNull('nomor_bl')
            ->where('nomor_bl', '!=', '')
            ->orderBy('nomor_bl')
            ->get();
        
        // Get klasifikasi biaya for pembayaran kapal
        $klasifikasiBiayas = \DB::table('klasifikasi_biayas')
            ->select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
        
        // Get pricelist buruh for pembayaran kapal with klasifikasi biaya "buruh"
        $pricelistBuruh = \DB::table('pricelist_buruh')
            ->select('id', 'barang', 'size', 'tipe', 'tarif')
            ->where('is_active', true)
            ->orderBy('barang')
            ->get();
        
        // Get list of penerima from karyawan for detail pembayaran dropdown
        $penerimaList = Karyawan::orderBy('nama_lengkap', 'asc')
            ->pluck('nama_lengkap')
            ->unique()
            ->values()
            ->toArray();
        
        return view('invoice-aktivitas-lain.edit', compact('invoice', 'karyawans', 'mobils', 'voyages', 'suratJalans', 'bls', 'klasifikasiBiayas', 'pricelistBuruh', 'penerimaList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = InvoiceAktivitasLain::findOrFail($id);
        
        $validated = $request->validate([
            'nomor_invoice' => 'required|string|max:255|unique:invoice_aktivitas_lain,nomor_invoice,' . $id,
            'tanggal_invoice' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'sub_jenis_kendaraan' => 'nullable|string',
            'nomor_polisi' => 'nullable|string',
            'nomor_voyage' => 'nullable|string',
            'bl_details' => 'nullable|array',
            'bl_details.*.bl_id' => 'nullable|integer|exists:bls,id',
            'klasifikasi_biaya_id' => 'nullable|integer|exists:klasifikasi_biayas,id',
            'barang_detail' => 'nullable|array',
            'barang_detail.*.pricelist_buruh_id' => 'required_with:barang_detail|integer|exists:pricelist_buruh,id',
            'barang_detail.*.jumlah' => 'required_with:barang_detail|numeric|min:0',
            'surat_jalan_id' => 'nullable|integer',
            'jenis_penyesuaian' => 'nullable|string',
            'tipe_penyesuaian_detail' => 'nullable|array',
            'tipe_penyesuaian_detail.*.tipe' => 'required_with:tipe_penyesuaian_detail|string',
            'tipe_penyesuaian_detail.*.nominal' => 'required_with:tipe_penyesuaian_detail|numeric|min:0',
            'detail_pembayaran' => 'nullable|array',
            'detail_pembayaran.*.jenis_biaya' => 'nullable|string',
            'detail_pembayaran.*.biaya' => 'nullable|string',
            'detail_pembayaran.*.keterangan' => 'nullable|string',
            'detail_pembayaran.*.tanggal_kas' => 'nullable|date',
            'detail_pembayaran.*.no_bukti' => 'nullable|string',
            'detail_pembayaran.*.penerima' => 'nullable|string',
            'penerima' => 'required|string',
            'total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);
        
        // Convert bl_details array to JSON for storage
        if (isset($validated['bl_details'])) {
            $validated['bl_details'] = json_encode($validated['bl_details']);
        }
        
        // Convert barang_detail array to JSON for storage
        if (isset($validated['barang_detail'])) {
            $validated['barang_detail'] = json_encode($validated['barang_detail']);
        }
        
        // Convert tipe_penyesuaian_detail array to JSON for storage
        if (isset($validated['tipe_penyesuaian_detail'])) {
            $validated['tipe_penyesuaian'] = json_encode($validated['tipe_penyesuaian_detail']);
            unset($validated['tipe_penyesuaian_detail']);
        }
        
        // Convert detail_pembayaran array to JSON for storage
        if (isset($validated['detail_pembayaran'])) {
            // Clean up biaya values - remove currency formatting
            foreach ($validated['detail_pembayaran'] as &$detail) {
                if (isset($detail['biaya'])) {
                    $detail['biaya'] = str_replace(['.', ','], '', $detail['biaya']);
                }
            }
            $validated['detail_pembayaran'] = json_encode($validated['detail_pembayaran']);
        }

        $invoice->update($validated);

        return redirect()->route('invoice-aktivitas-lain.show', $id)
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
        $invoice = InvoiceAktivitasLain::with(['createdBy'])->findOrFail($id);
        
        return view('invoice-aktivitas-lain.print', compact('invoice'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = InvoiceAktivitasLain::query()->with(['klasifikasiBiaya', 'klasifikasiBiayaUmum', 'listrikData']);

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
        $voyagesBl = DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage')
            ->get();
            
        $voyagesPergerakan = DB::table('pergerakan_kapal')
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
        $suratJalansRegular = DB::table('surat_jalans')
            ->select(
                'id',
                'no_surat_jalan',
                'tujuan_pengiriman',
                'uang_jalan',
                DB::raw("'regular' as source")
            )
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->get();
        
        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = DB::table('surat_jalan_bongkarans')
            ->select(
                'id',
                DB::raw('nomor_surat_jalan as no_surat_jalan'),
                'tujuan_pengiriman',
                'uang_jalan',
                DB::raw("'bongkar' as source")
            )
            ->whereNotNull('nomor_surat_jalan')
            ->where('nomor_surat_jalan', '!=', '')
            ->get();
        
        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();
        
        // Get BLs for pembayaran kapal
        $bls = DB::table('bls')
            ->select('id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'no_voyage', 'pengirim')
            ->whereNotNull('nomor_bl')
            ->where('nomor_bl', '!=', '')
            ->orderBy('nomor_bl')
            ->get();
        
        // Get klasifikasi biaya for pembayaran kapal
        $klasifikasiBiayas = DB::table('klasifikasi_biayas')
            ->select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
        
        // Get pricelist buruh for pembayaran kapal with klasifikasi biaya "buruh"
        $pricelistBuruh = DB::table('pricelist_buruh')
            ->select('id', 'barang', 'size', 'tipe', 'tarif')
            ->where('is_active', true)
            ->orderBy('barang')
            ->get();
        
        // Get pricelist biaya dokumen for klasifikasi biaya "biaya dokumen"
        $pricelistBiayaDokumen = DB::table('pricelist_biaya_dokumen')
            ->select('id', 'nama_vendor', 'biaya')
            ->where('status', 'aktif')
            ->orderBy('nama_vendor')
            ->get();
        
        // Get list of penerima from karyawan for detail pembayaran dropdown
        $penerimaList = Karyawan::orderBy('nama_lengkap', 'asc')
            ->pluck('nama_lengkap')
            ->unique()
            ->values()
            ->toArray();
        
        // Get akun COA for biaya listrik
        $akunCoas = DB::table('akun_coa')
            ->select('id', 'nomor_akun', 'nama_akun')
            ->orderBy('nomor_akun')
            ->get();
        
        return view('invoice-aktivitas-lain.create', compact('karyawans', 'mobils', 'voyages', 'suratJalans', 'bls', 'klasifikasiBiayas', 'pricelistBuruh', 'pricelistBiayaDokumen', 'penerimaList', 'akunCoas'));
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
        // Clean up numeric fields - remove currency formatting
        $inputs = $request->all();
        $fieldsToClean = [
            'sub_total_labuh', 
            'pph_labuh', 
            'total', 
            'pph', 
            'grand_total',
            'subtotal',
            'lwbp_baru',
            'lwbp_lama',
            'lwbp',
            'wbp',
            'lwbp_tarif',
            'wbp_tarif',
            'tarif_1',
            'tarif_2',
            'biaya_beban',
            'ppju',
            'dpp',
            'nominal'
        ];
        
        foreach ($fieldsToClean as $field) {
            if (isset($inputs[$field]) && is_string($inputs[$field])) {
                $inputs[$field] = str_replace(['.', ','], '', $inputs[$field]);
            }
        }

        // Handle nested arrays cleaning
        if (isset($inputs['biaya_listrik']) && is_array($inputs['biaya_listrik'])) {
            foreach ($inputs['biaya_listrik'] as &$item) {
                foreach ($item as $key => $value) {
                    if (is_string($value) && in_array($key, ['nominal_debit', 'nominal_kredit', 'lwbp_baru', 'lwbp_lama', 'lwbp', 'wbp', 'lwbp_tarif', 'wbp_tarif', 'tarif_1', 'tarif_2', 'biaya_beban', 'ppju', 'dpp', 'pph', 'grand_total'])) {
                        $item[$key] = str_replace(['.', ','], '', $value);
                    }
                }
            }
        }
        
        $request->merge($inputs);
        // Check if this is biaya listrik invoice
        $isBiayaListrik = false;
        if ($request->has('klasifikasi_biaya_umum_id')) {
            $klasifikasiBiaya = \App\Models\KlasifikasiBiaya::find($request->klasifikasi_biaya_umum_id);
            if ($klasifikasiBiaya && stripos($klasifikasiBiaya->nama, 'listrik') !== false) {
                $isBiayaListrik = true;
            }
        }
        
        // Conditional validation rules
        $totalValidation = $isBiayaListrik ? 'nullable|numeric|min:0' : 'required|numeric|min:0';
        
        $isLabuhTambat = false;
        if ($request->has('klasifikasi_biaya_id')) {
            $klasifikasiBiaya = \App\Models\KlasifikasiBiaya::find($request->klasifikasi_biaya_id);
            if ($klasifikasiBiaya && (stripos($klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
                $isLabuhTambat = true;
            }
        }
        if (!$isLabuhTambat && $request->has('klasifikasi_biaya_umum_id')) {
            $klasifikasiBiaya = \App\Models\KlasifikasiBiaya::find($request->klasifikasi_biaya_umum_id);
            if ($klasifikasiBiaya && (stripos($klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
                $isLabuhTambat = true;
            }
        }
        $vendorLabuhTambatValidation = $isLabuhTambat ? 'required|string|max:255' : 'nullable|string|max:255';
        
        $validated = $request->validate([
            'nomor_invoice' => 'required|string|max:255|unique:invoice_aktivitas_lain,nomor_invoice',
            'tanggal_invoice' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'klasifikasi_biaya_umum_id' => 'nullable|integer|exists:klasifikasi_biayas,id',
            'referensi' => 'nullable|string|max:255',
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
            'penerima' => 'nullable|string', // Made nullable since biaya listrik entries have their own penerima
            'vendor_listrik' => 'nullable|string|max:255',
            'vendor_labuh_tambat' => $vendorLabuhTambatValidation,
            'tanggal_invoice_vendor' => 'nullable|date',
            'nomor_rekening_labuh' => 'nullable|string|max:255',
            'sub_total_labuh' => 'nullable|numeric|min:0',
            'pph_labuh' => 'nullable|numeric|min:0',
            'total' => $totalValidation, // Conditional: nullable for biaya listrik, required for others
            'pph' => 'nullable|numeric|min:0',
            'grand_total' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
            // Biaya Listrik fields - now accepts array for multiple entries
            'biaya_listrik' => 'nullable|array',
            'biaya_listrik.*.referensi' => 'nullable|string|max:255',
            'biaya_listrik.*.penerima' => 'nullable|string|max:255',
            'biaya_listrik.*.tanggal' => 'nullable|date',
            'biaya_listrik.*.akun_coa_id' => 'nullable|exists:akun_coa,id',
            'biaya_listrik.*.tipe_transaksi' => 'nullable|in:debit,kredit',
            'biaya_listrik.*.nominal_debit' => 'nullable|numeric|min:0',
            'biaya_listrik.*.nominal_kredit' => 'nullable|numeric|min:0',
            'biaya_listrik.*.lwbp_baru' => 'nullable|numeric|min:0',
            'biaya_listrik.*.lwbp_lama' => 'nullable|numeric|min:0',
            'biaya_listrik.*.lwbp' => 'nullable|numeric',
            'biaya_listrik.*.wbp' => 'nullable|numeric',
            'biaya_listrik.*.lwbp_tarif' => 'nullable|numeric|min:0',
            'biaya_listrik.*.wbp_tarif' => 'nullable|numeric|min:0',
            'biaya_listrik.*.tarif_1' => 'nullable|numeric',
            'biaya_listrik.*.tarif_2' => 'nullable|numeric',
            'biaya_listrik.*.biaya_beban' => 'nullable|numeric|min:0',
            'biaya_listrik.*.ppju' => 'nullable|numeric',
            'biaya_listrik.*.dpp' => 'nullable|numeric',
            'biaya_listrik.*.pph' => 'nullable|numeric',
            'biaya_listrik.*.grand_total' => 'nullable|numeric',
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

        // Map Labuh Tambat fields to database columns
        if ($isLabuhTambat) {
            $validated['subtotal'] = $request->sub_total_labuh;
            $validated['pph'] = $request->pph_labuh;
            $validated['grand_total'] = $request->total;
            
            // Remove request-only fields
            unset($validated['sub_total_labuh']);
            unset($validated['pph_labuh']);
        }

        // Set default status
        $validated['status'] = 'draft';

        // Extract biaya listrik data array before creating invoice
        $biayaListrikEntries = [];
        if (isset($validated['biaya_listrik']) && is_array($validated['biaya_listrik'])) {
            $biayaListrikEntries = $validated['biaya_listrik'];
            unset($validated['biaya_listrik']);
        }

        $invoice = InvoiceAktivitasLain::create($validated);

        // Create multiple biaya listrik records if data exists
        if (!empty($biayaListrikEntries)) {
            foreach ($biayaListrikEntries as $biayaListrikData) {
                // Add invoice_aktivitas_lain_id to each entry
                $biayaListrikData['invoice_aktivitas_lain_id'] = $invoice->id;
                \App\Models\InvoiceAktivitasLainListrik::create($biayaListrikData);
            }
        }

        return redirect()->route('invoice-aktivitas-lain.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = InvoiceAktivitasLain::with(['klasifikasiBiaya', 'klasifikasiBiayaUmum', 'pembayarans', 'creator', 'biayaListrik'])
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
        $voyagesBl = DB::table('bls')
            ->select('no_voyage as voyage', 'nama_kapal')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage')
            ->get();
            
        $voyagesPergerakan = DB::table('pergerakan_kapal')
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
        $suratJalansRegular = DB::table('surat_jalans')
            ->select(
                'id',
                'no_surat_jalan',
                'tujuan_pengiriman',
                'uang_jalan',
                DB::raw("'regular' as source")
            )
            ->whereNotNull('no_surat_jalan')
            ->where('no_surat_jalan', '!=', '')
            ->get();
        
        // Get surat jalans for adjustment payments from surat_jalan_bongkarans table
        $suratJalansBongkar = DB::table('surat_jalan_bongkarans')
            ->select(
                'id',
                DB::raw('nomor_surat_jalan as no_surat_jalan'),
                'tujuan_pengiriman',
                'uang_jalan',
                DB::raw("'bongkar' as source")
            )
            ->whereNotNull('nomor_surat_jalan')
            ->where('nomor_surat_jalan', '!=', '')
            ->get();
        
        // Combine both surat jalans
        $suratJalans = $suratJalansRegular->merge($suratJalansBongkar)
            ->sortBy('no_surat_jalan')
            ->values();
        
        // Get BLs for pembayaran kapal
        $bls = DB::table('bls')
            ->select('id', 'nomor_bl', 'nomor_kontainer', 'no_seal', 'no_voyage', 'pengirim')
            ->whereNotNull('nomor_bl')
            ->where('nomor_bl', '!=', '')
            ->orderBy('nomor_bl')
            ->get();
        
        // Get klasifikasi biaya for pembayaran kapal
        $klasifikasiBiayas = DB::table('klasifikasi_biayas')
            ->select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
        
        // Get pricelist buruh for pembayaran kapal with klasifikasi biaya "buruh"
        $pricelistBuruh = DB::table('pricelist_buruh')
            ->select('id', 'barang', 'size', 'tipe', 'tarif')
            ->where('is_active', true)
            ->orderBy('barang')
            ->get();
        
        // Get pricelist biaya dokumen for klasifikasi biaya "biaya dokumen"
        $pricelistBiayaDokumen = DB::table('pricelist_biaya_dokumen')
            ->select('id', 'nama_vendor', 'biaya')
            ->where('status', 'aktif')
            ->orderBy('nama_vendor')
            ->get();
        
        // Get list of penerima from karyawan for detail pembayaran dropdown
        $penerimaList = Karyawan::orderBy('nama_lengkap', 'asc')
            ->pluck('nama_lengkap')
            ->unique()
            ->values()
            ->toArray();
        
        return view('invoice-aktivitas-lain.edit', compact('invoice', 'karyawans', 'mobils', 'voyages', 'suratJalans', 'bls', 'klasifikasiBiayas', 'pricelistBuruh', 'pricelistBiayaDokumen', 'penerimaList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = InvoiceAktivitasLain::findOrFail($id);
        
        $isLabuhTambat = false;
        if ($request->has('klasifikasi_biaya_id')) {
            $klasifikasiBiaya = \App\Models\KlasifikasiBiaya::find($request->klasifikasi_biaya_id);
            if ($klasifikasiBiaya && (stripos($klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
                $isLabuhTambat = true;
            }
        }
        if (!$isLabuhTambat && $request->has('klasifikasi_biaya_umum_id')) {
            $klasifikasiBiaya = \App\Models\KlasifikasiBiaya::find($request->klasifikasi_biaya_umum_id);
            if ($klasifikasiBiaya && (stripos($klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
                $isLabuhTambat = true;
            }
        }
        $vendorLabuhTambatValidation = $isLabuhTambat ? 'required|string|max:255' : 'nullable|string|max:255';

        $validated = $request->validate([
            'nomor_invoice' => 'required|string|max:255|unique:invoice_aktivitas_lain,nomor_invoice,' . $id,
            'tanggal_invoice' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'klasifikasi_biaya_umum_id' => 'nullable|integer|exists:klasifikasi_biayas,id',
            'referensi' => 'nullable|string|max:255',            'sub_jenis_kendaraan' => 'nullable|string',
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
            'penerima' => 'nullable|string', // Made nullable since biaya listrik entries have their own penerima
            'vendor_labuh_tambat' => $vendorLabuhTambatValidation,
            'tanggal_invoice_vendor' => 'nullable|date',
            'nomor_rekening_labuh' => 'nullable|string|max:255',
            'sub_total_labuh' => 'nullable|numeric|min:0',
            'pph_labuh' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'grand_total' => 'nullable|numeric|min:0',
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

        // Map Labuh Tambat fields to database columns
        if ($isLabuhTambat) {
            $validated['subtotal'] = $request->sub_total_labuh;
            $validated['pph'] = $request->pph_labuh;
            $validated['grand_total'] = $request->total;

            // Remove request-only fields
            unset($validated['sub_total_labuh']);
            unset($validated['pph_labuh']);
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
        try {
            $invoice = InvoiceAktivitasLain::findOrFail($id);
            $invoice->delete();
            
            return redirect()->route('invoice-aktivitas-lain.index')
                ->with('success', 'Invoice berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('invoice-aktivitas-lain.index')
                ->with('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete invoices
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada invoice yang dipilih.'], 400);
            }
            
            $deleted = InvoiceAktivitasLain::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true, 
                'message' => $deleted . ' invoice berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print invoice
     */
    public function print(string $id)
    {
        $invoice = InvoiceAktivitasLain::with(['createdBy', 'klasifikasiBiaya', 'klasifikasiBiayaUmum', 'biayaListrik'])->findOrFail($id);

        // check for labuh tambat
        $isLabuhTambat = false;
        if ($invoice->klasifikasiBiaya && (stripos($invoice->klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($invoice->klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
            $isLabuhTambat = true;
        }
        if (!$isLabuhTambat && $invoice->klasifikasiBiayaUmum && (stripos($invoice->klasifikasiBiayaUmum->nama, 'labuh tambat') !== false || stripos($invoice->klasifikasiBiayaUmum->nama, 'labuh tambah') !== false)) {
            $isLabuhTambat = true;
        }

        if($isLabuhTambat){
            return view('invoice-aktivitas-lain.print-labuh-tambat', compact('invoice'));
        }

        // check for listrik
        if ($invoice->klasifikasiBiayaUmum && str_contains(strtolower($invoice->klasifikasiBiayaUmum->nama), 'listrik')) {
            $biayaListrikEntries = $invoice->biayaListrik;
            if ($biayaListrikEntries->isEmpty()) {
                return redirect()->route('invoice-aktivitas-lain.show', $id)
                    ->with('error', 'Data biaya listrik tidak ditemukan untuk invoice ini.');
            }
            return view('invoice-aktivitas-lain.print-listrik', compact('invoice', 'biayaListrikEntries'));
        }
        
        return view('invoice-aktivitas-lain.print', compact('invoice'));
    }

    /**
     * Print invoice khusus untuk Biaya Listrik (dengan PPH)
     */
    public function printListrik(string $id)
    {
        $invoice = InvoiceAktivitasLain::with(['creator', 'approver', 'klasifikasiBiayaUmum', 'biayaListrik'])->findOrFail($id);
        
        // Pastikan ini invoice biaya listrik
        if ($invoice->klasifikasiBiayaUmum && !str_contains(strtolower($invoice->klasifikasiBiayaUmum->nama), 'listrik')) {
            return redirect()->route('invoice-aktivitas-lain.print', $id)
                ->with('warning', 'Print khusus listrik hanya untuk invoice biaya listrik.');
        }
        
        // Pastikan ada data biaya listrik
        // Get all biaya listrik entries for this invoice
        $biayaListrikEntries = $invoice->biayaListrik;
        
        if ($biayaListrikEntries->isEmpty()) {
            return redirect()->route('invoice-aktivitas-lain.show', $id)
                ->with('error', 'Data biaya listrik tidak ditemukan untuk invoice ini.');
        }
        
        return view('invoice-aktivitas-lain.print-listrik', compact('invoice', 'biayaListrikEntries'));
    }

    /**
     * Print invoice khusus untuk Labuh Tambat (dengan PPH 2%)
     */
    public function printLabuhTambat(string $id)
    {
        $invoice = InvoiceAktivitasLain::with(['creator', 'approver', 'klasifikasiBiaya', 'klasifikasiBiayaUmum'])->findOrFail($id);
        
        // Pastikan ini invoice labuh tambat
        $isLabuhTambat = false;
        if ($invoice->klasifikasiBiaya && (stripos($invoice->klasifikasiBiaya->nama, 'labuh tambat') !== false || stripos($invoice->klasifikasiBiaya->nama, 'labuh tambah') !== false)) {
            $isLabuhTambat = true;
        }
        if (!$isLabuhTambat && $invoice->klasifikasiBiayaUmum && (stripos($invoice->klasifikasiBiayaUmum->nama, 'labuh tambat') !== false || stripos($invoice->klasifikasiBiayaUmum->nama, 'labuh tambah') !== false)) {
            $isLabuhTambat = true;
        }
        
        if (!$isLabuhTambat) {
            return redirect()->route('invoice-aktivitas-lain.print', $id)
                ->with('warning', 'Print khusus labuh tambat hanya untuk invoice labuh tambat.');
        }
        
        return view('invoice-aktivitas-lain.print-labuh-tambat', compact('invoice'));
    }
}

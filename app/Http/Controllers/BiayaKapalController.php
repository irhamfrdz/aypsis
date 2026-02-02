<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\MasterKapal;
use App\Models\KlasifikasiBiaya;
use App\Models\PricelistBuruh;
use App\Models\PricelistTkbm;
use App\Models\BiayaKapalBarang;
use App\Models\BiayaKapalAir;
use App\Models\BiayaKapalTkbm;
use App\Models\BiayaKapalOperasional;
use App\Models\BiayaKapalOperasionalItem;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiayaKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaKapal::with(['klasifikasiBiaya', 'barangDetails.pricelistBuruh', 'operasionalDetails']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%");
            });
        }

        // Filter by jenis biaya
        if ($request->has('jenis_biaya') && $request->jenis_biaya != '') {
            $query->where('jenis_biaya', $request->jenis_biaya);
        }

        // Sort by tanggal descending by default
        $query->orderBy('tanggal', 'desc');

        $biayaKapals = $query->paginate(10)->withQueryString();

        return view('biaya-kapal.index', compact('biayaKapals'));
    }

    /**
     * Generate next invoice number
     */
    public function getNextInvoiceNumber()
    {
        try {
            $currentMonth = date('m');
            $currentYear = date('y');
            $prefix = 'BKP';
            
            // Get last invoice for current month and year (include soft-deleted)
            $lastInvoice = BiayaKapal::withTrashed()
                ->where('nomor_invoice', 'like', "{$prefix}-{$currentMonth}-{$currentYear}-%")
                ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_invoice, "-", -1) AS UNSIGNED) DESC')
                ->first();
            
            if ($lastInvoice) {
                // Extract running number from last invoice
                $parts = explode('-', $lastInvoice->nomor_invoice);
                $lastNumber = intval(end($parts));
                $newNumber = $lastNumber + 1;
            } else {
                // First invoice of the month
                $newNumber = 1;
            }
            
            // Format: BKP-MM-YY-NNNNNN
            $invoiceNumber = sprintf("%s-%s-%s-%06d", $prefix, $currentMonth, $currentYear, $newNumber);
            
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get list of ships for dropdown
        $kapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();

        // Get active klasifikasi biaya for jenis biaya dropdown
        $klasifikasiBiayas = KlasifikasiBiaya::where('is_active', true)->orderBy('nama')->get();

        // Get active pricelist buruh for barang selection
        $pricelistBuruh = PricelistBuruh::where('is_active', true)->orderBy('barang')->get();

        // Get karyawans for penerima dropdown
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        // Get active pricelist biaya dokumen for vendor selection
        $pricelistBiayaDokumen = DB::table('pricelist_biaya_dokumen')
            ->where('status', 'aktif')
            ->orderBy('nama_vendor')
            ->get();
        
        // Get active pricelist air tawar for biaya air
        $pricelistAirTawar = \App\Models\MasterPricelistAirTawar::orderBy('nama_agen')->get();

        // Get active pricelist TKBM for biaya TKBM barang selection
        $pricelistTkbm = \App\Models\PricelistTkbm::where('status', 'active')->orderBy('nama_barang')->get();

        return view('biaya-kapal.create', compact('kapals', 'klasifikasiBiayas', 'pricelistBuruh', 'karyawans', 'pricelistBiayaDokumen', 'pricelistAirTawar', 'pricelistTkbm'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Clean up all currency and numeric fields before validation
        $data = $request->all();
        
        // Root fields
        $fieldsToClean = ['nominal', 'ppn', 'pph', 'total_biaya', 'dp', 'sisa_pembayaran', 'pph_dokumen', 'grand_total_dokumen', 'biaya_materai', 'jasa_air', 'pph_air', 'grand_total_air'];
        foreach ($fieldsToClean as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $data[$field] = str_replace(',', '.', str_replace('.', '', $data[$field]));
            }
        }
        
        // Kapal Sections (Buruh)
        if (isset($data['kapal_sections']) && is_array($data['kapal_sections'])) {
            foreach ($data['kapal_sections'] as &$section) {
                if (isset($section['total_nominal'])) $section['total_nominal'] = str_replace(',', '.', str_replace('.', '', $section['total_nominal']));
                if (isset($section['dp'])) $section['dp'] = str_replace(',', '.', str_replace('.', '', $section['dp']));
                if (isset($section['sisa_pembayaran'])) $section['sisa_pembayaran'] = str_replace(',', '.', str_replace('.', '', $section['sisa_pembayaran']));
                
                if (isset($section['barang']) && is_array($section['barang'])) {
                    foreach ($section['barang'] as &$barang) {
                        if (isset($barang['jumlah'])) $barang['jumlah'] = str_replace(',', '.', str_replace('.', '', $barang['jumlah']));
                    }
                }
            }
        }
        
        // Air Sections
        if (isset($data['air']) && is_array($data['air'])) {
            foreach ($data['air'] as &$section) {
                $numericAir = ['kuantitas', 'harga', 'jasa_air', 'biaya_agen', 'sub_total', 'pph', 'grand_total', 'sub_total_value', 'pph_value', 'grand_total_value'];
                foreach ($numericAir as $f) {
                    if (isset($section[$f]) && is_string($section[$f])) {
                        if (str_contains($section[$f], ',') && !str_contains($section[$f], '.')) {
                            $section[$f] = str_replace(',', '.', $section[$f]);
                        } elseif (str_contains($section[$f], '.') && str_contains($section[$f], ',')) {
                            $section[$f] = str_replace(',', '.', str_replace('.', '', $section[$f]));
                        } elseif (str_contains($section[$f], '.') && !str_contains($section[$f], ',')) {
                            if (preg_match('/\.\d{3}($|\.)/', $section[$f]) || substr_count($section[$f], '.') > 1) {
                                $section[$f] = str_replace('.', '', $section[$f]);
                            }
                        }
                    }
                }
            }
        }
        
        // TKBM Sections
        if (isset($data['tkbm_sections']) && is_array($data['tkbm_sections'])) {
            foreach ($data['tkbm_sections'] as &$section) {
                if (isset($section['total_nominal'])) $section['total_nominal'] = str_replace(',', '.', str_replace('.', '', $section['total_nominal']));
                if (isset($section['pph'])) $section['pph'] = str_replace(',', '.', str_replace('.', '', $section['pph']));
                if (isset($section['grand_total'])) $section['grand_total'] = str_replace(',', '.', str_replace('.', '', $section['grand_total']));
                if (isset($section['barang']) && is_array($section['barang'])) {
                    foreach ($section['barang'] as &$barang) {
                        if (isset($barang['jumlah'])) $barang['jumlah'] = str_replace(',', '.', str_replace('.', '', $barang['jumlah']));
                    }
                    unset($barang);
                }
            }
            unset($section);
        }
        
        // Operasional Sections
        if (isset($data['operasional_sections']) && is_array($data['operasional_sections'])) {
            foreach ($data['operasional_sections'] as &$section) {
                if (isset($section['nominal'])) $section['nominal'] = str_replace(',', '.', str_replace('.', '', $section['nominal']));
                if (isset($section['total_nominal'])) $section['total_nominal'] = str_replace(',', '.', str_replace('.', '', $section['total_nominal']));
                if (isset($section['dp'])) $section['dp'] = str_replace(',', '.', str_replace('.', '', $section['dp']));
                if (isset($section['sisa_pembayaran'])) $section['sisa_pembayaran'] = str_replace(',', '.', str_replace('.', '', $section['sisa_pembayaran']));
            }
            unset($section);
        }
        
        $request->replace($data);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nomor_referensi' => 'nullable|string|max:100',
            'nama_kapal' => 'nullable|array',
            'nama_kapal.*' => 'string|max:255',
            'no_voyage' => 'nullable|array',
            'no_voyage.*' => 'string',
            'no_bl' => 'nullable|array',
            'no_bl.*' => 'string',
            'jenis_biaya' => 'required|exists:klasifikasi_biayas,kode',
            'vendor_id' => 'nullable|exists:pricelist_biaya_dokumen,id',
            'nominal' => 'nullable|numeric|min:0',
            'penerima' => 'nullable|string|max:255',
            'nama_vendor' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'total_biaya' => 'nullable|numeric|min:0',
            'dp' => 'nullable|numeric|min:0',
            'sisa_pembayaran' => 'nullable|numeric|min:0',
            // Biaya Dokumen fields
            'pph_dokumen' => 'nullable|numeric|min:0',
            'grand_total_dokumen' => 'nullable|numeric|min:0',
            // Old structure (for backward compatibility)
            'barang' => 'nullable|array',
            'barang.*.barang_id' => 'required_with:barang|exists:pricelist_buruh,id',
            'barang.*.jumlah' => 'required_with:barang|numeric|min:0',
            // New kapal sections structure
            'kapal_sections' => 'nullable|array',
            'kapal_sections.*.kapal' => 'required_with:kapal_sections|string|max:255',
            'kapal_sections.*.voyage' => 'required_with:kapal_sections|string|max:255',
            'kapal_sections.*.barang' => 'required_with:kapal_sections|array',
            'kapal_sections.*.barang.*.barang_id' => 'required|exists:pricelist_buruh,id',
            'kapal_sections.*.barang.*.jumlah' => 'required|numeric|min:0',
            'kapal_sections.*.total_nominal' => 'nullable|numeric|min:0',
            'kapal_sections.*.dp' => 'nullable|numeric|min:0',
            'kapal_sections.*.sisa_pembayaran' => 'nullable|numeric|min:0',
            // Biaya Air sections structure
            'air' => 'nullable|array',
            'air.*.kapal' => 'nullable|string|max:255',
            'air.*.voyage' => 'nullable|string|max:255',
            'air.*.vendor' => 'nullable|string|max:255',
            'air.*.type' => 'nullable|integer',
            'air.*.kuantitas' => 'nullable|numeric|min:0',
            'air.*.harga' => 'nullable|numeric|min:0',
            'air.*.jasa_air' => 'nullable|numeric|min:0',
            'air.*.biaya_agen' => 'nullable|numeric|min:0',
            'air.*.lokasi' => 'nullable|string|max:255',
            'air.*.sub_total' => 'nullable|numeric|min:0',
            'air.*.pph' => 'nullable|numeric|min:0',
            'air.*.grand_total' => 'nullable|numeric|min:0',
            'air.*.penerima' => 'nullable|string|max:255',
            'air.*.nomor_rekening' => 'nullable|string|max:100',
            'air.*.tanggal_invoice_vendor' => 'nullable|date',
            // TKBM sections structure
            'tkbm_sections' => 'nullable|array',
            'tkbm_sections.*.kapal' => 'nullable|string|max:255',
            'tkbm_sections.*.voyage' => 'nullable|string|max:255',
            'tkbm_sections.*.no_referensi' => 'nullable|string|max:100',
            'tkbm_sections.*.barang' => 'nullable|array',
            'tkbm_sections.*.barang.*.barang_id' => 'nullable|exists:pricelist_tkbms,id',
            'tkbm_sections.*.barang.*.jumlah' => 'nullable|numeric|min:0',
            'tkbm_sections.*.tanggal_invoice_vendor' => 'nullable|date',

            // Operasional sections structure
            'operasional_sections' => 'nullable|array',
            'operasional_sections.*.kapal' => 'nullable|string|max:255',
            'operasional_sections.*.voyage' => 'nullable|string|max:255',
            'operasional_sections.*.nominal' => 'nullable|numeric|min:0',
            'operasional_sections.*.total_nominal' => 'nullable|numeric|min:0',
            'operasional_sections.*.dp' => 'nullable|numeric|min:0',
            'operasional_sections.*.sisa_pembayaran' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique invoice number inside transaction
            $currentMonth = date('m');
            $currentYear = date('y');
            $prefix = 'BKP';
            
            // Get last invoice for current month and year with lock
            // Use withTrashed() to include soft-deleted records (unique constraint includes them)
            $lastInvoice = BiayaKapal::withTrashed()
                ->where('nomor_invoice', 'like', "{$prefix}-{$currentMonth}-{$currentYear}-%")
                ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_invoice, "-", -1) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->nomor_invoice);
                $lastNumber = intval(end($parts));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $nomorInvoice = sprintf("%s-%s-%s-%06d", $prefix, $currentMonth, $currentYear, $newNumber);
            $validated['nomor_invoice'] = $nomorInvoice;

            // Handle file upload
            if ($request->hasFile('bukti')) {
                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }

            // Create BiayaKapal record
            $biayaKapal = BiayaKapal::create($validated);

            // Store barang details - Handle both old and new structure
            $barangDetails = [];
            
            // NEW STRUCTURE: kapal sections (for multi-kapal biaya buruh)
            if ($request->has('kapal_sections') && !empty($request->kapal_sections)) {
                // Debug log: Log all kapal sections received
                \Log::info('Kapal sections received in store method', [
                    'biaya_kapal_id' => $biayaKapal->id,
                    'sections_count' => count($request->kapal_sections),
                    'sections_data' => $request->kapal_sections,
                ]);
                
                foreach ($request->kapal_sections as $sectionIndex => $section) {
                    // Debug: Log raw section data
                    \Log::info("Raw section data for index $sectionIndex", ['section' => $section]);
                    
                    $kapalName = $section['kapal'] ?? null;
                    $voyageName = $section['voyage'] ?? null;
                    $sectionTotalNominal = $section['total_nominal'] ?? 0;
                    $sectionDp = $section['dp'] ?? 0;
                    $sectionSisa = $section['sisa_pembayaran'] ?? 0;
                    
                    \Log::info("Processing kapal section $sectionIndex", [
                        'kapal' => $kapalName,
                        'voyage' => $voyageName,
                        'total_nominal' => $sectionTotalNominal,
                        'barang_count' => isset($section['barang']) ? count($section['barang']) : 0,
                    ]);
                    
                    $sectionHasData = false; // Track if section has any saved items
                    
                    if (isset($section['barang']) && is_array($section['barang'])) {
                        foreach ($section['barang'] as $item) {
                            // Normalize inputs (trim strings, convert decimals)
                            $barangIdRaw = $item['barang_id'] ?? null;
                            $barangId = is_string($barangIdRaw) ? trim($barangIdRaw) : $barangIdRaw;

                            $jumlah = floatval($item['jumlah'] ?? 0);

                            // Basic validation: skip if missing barang id or non-positive jumlah
                            if (empty($barangId) || $jumlah <= 0) {
                                
                                // Log skipped items for easier debugging
                                
                                Log::warning('Skipping kapal section barang during save: missing barang_id or jumlah <= 0', [
                                    'biaya_kapal_id' => $biayaKapal->id ?? null,
                                    'section_index' => $sectionIndex,
                                    'kapal' => $kapalName,
                                    'voyage' => $voyageName,
                                    'item' => $item,
                                ]);
                                continue;
                            }

                            $barang = PricelistBuruh::find($barangId);
                            if (!$barang) {
                                Log::warning('PricelistBuruh not found for barang_id while saving kapal section', ['barang_id' => $barangId, 'item' => $item]);
                                continue;
                            }

                            $subtotal = $barang->tarif * $jumlah;

                            // Save to biaya_kapal_barang table with kapal, voyage, and DP info
                            BiayaKapalBarang::create([
                                'biaya_kapal_id' => $biayaKapal->id,
                                'pricelist_buruh_id' => $barang->id,
                                'kapal' => $kapalName,
                                'voyage' => $voyageName,
                                'jumlah' => $jumlah,
                                'tarif' => $barang->tarif,
                                'subtotal' => $subtotal,
                                'total_nominal' => $sectionTotalNominal,
                                'dp' => $sectionDp,
                                'sisa_pembayaran' => $sectionSisa,
                            ]);
                            
                            $sectionHasData = true; // Mark that section has saved data

                            // Build keterangan string with kapal, voyage, and DP info
                            $barangDetails[] = "[$kapalName - Voyage $voyageName] " . $barang->barang . ' x ' . $jumlah . ' = Rp ' . number_format($subtotal, 0, ',', '.');
                        }
                        
                        // Add section summary to barang details
                        if ($sectionDp > 0 || $sectionSisa > 0) {
                            $barangDetails[] = "  → Total: Rp " . number_format($sectionTotalNominal, 0, ',', '.') . 
                                             " | DP: Rp " . number_format($sectionDp, 0, ',', '.') . 
                                             " | Sisa: Rp " . number_format($sectionSisa, 0, ',', '.');
                        }
                    }
                    
                    // IMPORTANT: If section has kapal/voyage but no valid barang data saved,
                    // create a placeholder record so the kapal appears in print
                    if (!$sectionHasData && !empty($kapalName) && !empty($voyageName)) {
                        \Log::warning("Section $sectionIndex has no barang data, creating placeholder", [
                            'kapal' => $kapalName,
                            'voyage' => $voyageName,
                        ]);
                        
                        // Create placeholder with null pricelist_buruh_id and 0 values
                        BiayaKapalBarang::create([
                            'biaya_kapal_id' => $biayaKapal->id,
                            'pricelist_buruh_id' => null,
                            'kapal' => $kapalName,
                            'voyage' => $voyageName,
                            'jumlah' => 0,
                            'tarif' => 0,
                            'subtotal' => 0,
                            'total_nominal' => $sectionTotalNominal,
                            'dp' => $sectionDp,
                            'sisa_pembayaran' => $sectionSisa,
                        ]);
                    }
                }
            }
            // OLD STRUCTURE: flat barang array (for backward compatibility)
            elseif ($request->has('barang') && !empty($request->barang)) {
                foreach ($request->barang as $item) {
                    $barang = PricelistBuruh::find($item['barang_id']);
                    if ($barang) {
                        $subtotal = $barang->tarif * $item['jumlah'];
                        
                        // Save to biaya_kapal_barang table
                        BiayaKapalBarang::create([
                            'biaya_kapal_id' => $biayaKapal->id,
                            'pricelist_buruh_id' => $barang->id,
                            'jumlah' => $item['jumlah'],
                            'tarif' => $barang->tarif,
                            'subtotal' => $subtotal,
                        ]);

                        // Build keterangan string
                        $barangDetails[] = $barang->barang . ' x ' . $item['jumlah'] . ' = Rp ' . number_format($subtotal, 0, ',', '.');
                    }
                }
            }
            
            // Update keterangan with barang details (REMOVED as per user request to keep keterangan clean)
            /*
            if (!empty($barangDetails)) {
                $keteranganBarang = "Detail Barang Buruh:\n" . implode("\n", $barangDetails);
                $biayaKapal->keterangan = $biayaKapal->keterangan 
                    ? $biayaKapal->keterangan . "\n\n" . $keteranganBarang 
                    : $keteranganBarang;
                $biayaKapal->save();
            }
            */

            // BIAYA AIR SECTIONS: Store air details
            $airDetails = [];
            if ($request->has('air') && !empty($request->air)) {
                foreach ($request->air as $sectionIndex => $section) {
                    // Skip empty sections
                    if (empty($section['kapal']) && empty($section['vendor'])) {
                        continue;
                    }
                    
                    // Values are already cleaned before validation
                    $kuantitas = floatval($section['kuantitas'] ?? 0);
                    $harga = floatval($section['harga'] ?? 0);
                    $jasaAir = floatval($section['jasa_air'] ?? 0);
                    $biayaAgen = floatval($section['biaya_agen'] ?? 0);
                    
                    // Use already cleaned values
                    $subTotal = floatval($section['sub_total'] ?? $section['sub_total_value'] ?? 0);
                    $pph = floatval($section['pph'] ?? $section['pph_value'] ?? 0);
                    $grandTotal = floatval($section['grand_total'] ?? $section['grand_total_value'] ?? 0);
                    
                    // Get type keterangan from pricelist
                    $typeKeterangan = null;
                    if (!empty($section['type'])) {
                        // source of type records is master_pricelist_air_tawar (each row represents a type/price entry)
                        $typeData = DB::table('master_pricelist_air_tawar')
                            ->where('id', $section['type'])
                            ->first();
                        $typeKeterangan = $typeData ? $typeData->keterangan : null;
                    }
                    
                    // Create BiayaKapalAir record
                    BiayaKapalAir::create([
                        'biaya_kapal_id' => $biayaKapal->id,
                        'kapal' => $section['kapal'] ?? null,
                        'voyage' => $section['voyage'] ?? null,
                        'vendor' => $section['vendor'] ?? null,
                        'lokasi' => $section['lokasi'] ?? null,
                        'type_id' => $section['type'] ?? null,
                        'type_keterangan' => $typeKeterangan,
                        'kuantitas' => $kuantitas,
                        'harga' => $harga,
                        'jasa_air' => $jasaAir,
                        'biaya_agen' => $biayaAgen,
                        'sub_total' => $subTotal,
                        'pph' => $pph,
                        'grand_total' => $grandTotal,
                        'penerima' => $section['penerima'] ?? null,
                        'nomor_rekening' => $section['nomor_rekening'] ?? null,
                        'nomor_referensi' => $section['nomor_referensi'] ?? null,
                        'tanggal_invoice_vendor' => $section['tanggal_invoice_vendor'] ?? null,
                    ]);
                    
                    // Build keterangan string
                    $airDetails[] = "[" . ($section['kapal'] ?? 'N/A') . " - Voyage " . ($section['voyage'] ?? 'N/A') . "] " .
                        "Vendor: " . ($section['vendor'] ?? 'N/A') . " | " .
                        "Kuantitas: " . number_format($kuantitas, 2, ',', '.') . " ton | " .
                        "Jasa Air: Rp " . number_format($jasaAir, 0, ',', '.') . " | " .
                        "Biaya Agen: Rp " . number_format($biayaAgen, 0, ',', '.') . " | " .
                        "Sub Total: Rp " . number_format($subTotal, 0, ',', '.') . " | " .
                        "PPH: Rp " . number_format($pph, 0, ',', '.') . " | " .
                        "Grand Total: Rp " . number_format($grandTotal, 0, ',', '.');
                }
                
                // Update keterangan with air details (REMOVED as per user request to keep keterangan clean)
                /*
                if (!empty($airDetails)) {
                    $keteranganAir = "Detail Biaya Air:\n" . implode("\n", $airDetails);
                    $biayaKapal->keterangan = $biayaKapal->keterangan 
                        ? $biayaKapal->keterangan . "\n\n" . $keteranganAir 
                        : $keteranganAir;
                    $biayaKapal->save();
                }
                */
            }

            // BIAYA TKBM SECTIONS: Store TKBM details
            $tkbmDetails = [];
            if ($request->has('tkbm_sections') && !empty($request->tkbm_sections)) {
                foreach ($request->tkbm_sections as $sectionIndex => $section) {
                    // Skip empty sections
                    if (empty($section['kapal']) && empty($section['barang'])) {
                        continue;
                    }
                    
                    $kapalName = $section['kapal'] ?? null;
                    $voyageName = $section['voyage'] ?? null;
                    
                    if (isset($section['barang']) && is_array($section['barang'])) {
                        foreach ($section['barang'] as $item) {
                            // Normalize inputs
                            $barangIdRaw = $item['barang_id'] ?? null;
                            $barangId = is_string($barangIdRaw) ? trim($barangIdRaw) : $barangIdRaw;
                            
                            $jumlah = floatval($item['jumlah'] ?? 0);
                            
                            // Skip if missing barang id or non-positive jumlah
                            if (empty($barangId) || $jumlah <= 0) {
                                Log::warning('Skipping TKBM section barang during save: missing barang_id or jumlah <= 0', [
                                    'biaya_kapal_id' => $biayaKapal->id ?? null,
                                    'section_index' => $sectionIndex,
                                    'kapal' => $kapalName,
                                    'voyage' => $voyageName,
                                    'item' => $item,
                                ]);
                                continue;
                            }
                            
                            $barang = PricelistTkbm::find($barangId);
                            if (!$barang) {
                                Log::warning('PricelistTkbm not found for barang_id while saving TKBM section', ['barang_id' => $barangId, 'item' => $item]);
                                continue;
                            }
                            
                            $subtotal = $barang->tarif * $jumlah;
                            $sectionTotalNominal = $section['total_nominal'] ?? 0;
                            $sectionPph = $section['pph'] ?? 0;
                            $sectionGrandTotal = $section['grand_total'] ?? 0;
                            
                            // Save to biaya_kapal_tkbm table
                            BiayaKapalTkbm::create([
                                'biaya_kapal_id' => $biayaKapal->id,
                                'pricelist_tkbm_id' => $barang->id,
                                'kapal' => $kapalName,
                                'voyage' => $voyageName,
                                'no_referensi' => $section['no_referensi'] ?? null,
                                'jumlah' => $jumlah,
                                'tarif' => $barang->tarif,
                                'subtotal' => $subtotal,
                                'total_nominal' => $sectionTotalNominal,
                                'pph' => $sectionPph,
                                'grand_total' => $sectionGrandTotal,
                                'tanggal_invoice_vendor' => $section['tanggal_invoice_vendor'] ?? null,
                            ]);
                            
                            // Build keterangan string
                            $tkbmDetails[] = "[$kapalName - Voyage $voyageName] " . $barang->nama_barang . ' x ' . $jumlah . ' = Rp ' . number_format($subtotal, 0, ',', '.');
                        }
                    }
                }
            }

            // BIAYA OPERASIONAL SECTIONS: Store Operasional details
            $operasionalDetails = [];
            if ($request->has('operasional_sections') && !empty($request->operasional_sections)) {
                foreach ($request->operasional_sections as $sectionIndex => $section) {
                    $kapalName = $section['kapal'] ?? null;
                    $voyageName = $section['voyage'] ?? null;
                    
                    $nominal = floatval($section['nominal'] ?? 0);
                    
                    // Skip if completely empty
                    if (empty($kapalName) && $nominal <= 0) {
                        continue;
                    }
                    
                    BiayaKapalOperasional::create([
                        'biaya_kapal_id' => $biayaKapal->id,
                        'kapal' => $kapalName,
                        'voyage' => $voyageName,
                        'nominal' => $nominal,
                        'total_nominal' => 0,
                        'dp' => 0,
                        'sisa_pembayaran' => 0,
                    ]);
                    
                    $operasionalDetails[] = "[$kapalName - Voyage $voyageName] = Rp " . number_format($nominal, 0, ',', '.');
                }
            }

            // AUTO-CALCULATE NOMINAL FOR OPERASIONAL
            if (!empty($operasionalDetails)) {
                $totalOperasional = BiayaKapalOperasional::where('biaya_kapal_id', $biayaKapal->id)->sum('nominal');
                $biayaKapal->update(['nominal' => $totalOperasional]);
            }

            DB::commit();

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data biaya kapal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BiayaKapal $biayaKapal)
    {
        $biayaKapal->load(['klasifikasiBiaya', 'barangDetails.pricelistBuruh']);
        return view('biaya-kapal.show', compact('biayaKapal'));
    }

    /**
     * Print biaya kapal detail.
     */
    public function print(BiayaKapal $biayaKapal)
    {
        $biayaKapal->load(['klasifikasiBiaya', 'barangDetails.pricelistBuruh', 'airDetails', 'tkbmDetails.pricelistTkbm', 'operasionalDetails']);
        
        // Check if it's Biaya Dokumen and use specific print template
        if ($biayaKapal->klasifikasiBiaya && 
            (stripos($biayaKapal->klasifikasiBiaya->nama, 'dokumen') !== false || 
             $biayaKapal->jenis_biaya === 'KB001')) {
            return view('biaya-kapal.print-dokumen', compact('biayaKapal'));
        }
        
        // Check if it's Biaya Trucking and use specific print template
        if ($biayaKapal->klasifikasiBiaya && 
            stripos($biayaKapal->klasifikasiBiaya->nama, 'trucking') !== false) {
            return view('biaya-kapal.print-trucking', compact('biayaKapal'));
        }
        
        // Check if it's Biaya Air and use specific print template
        if ($biayaKapal->klasifikasiBiaya && 
            stripos($biayaKapal->klasifikasiBiaya->nama, 'air') !== false) {
            return view('biaya-kapal.print-air', compact('biayaKapal'));
        }
        
        // Check if it's Biaya TKBM and use specific print template
        if ($biayaKapal->klasifikasiBiaya && 
            stripos($biayaKapal->klasifikasiBiaya->nama, 'tkbm') !== false) {
            return view('biaya-kapal.print-tkbm', compact('biayaKapal'));
        }

        // Check if it's Biaya Operasional and use specific print template
        if ($biayaKapal->klasifikasiBiaya && 
            stripos($biayaKapal->klasifikasiBiaya->nama, 'operasional') !== false) {
            return view('biaya-kapal.print-operasional', compact('biayaKapal'));
        }
        
        return view('biaya-kapal.print', compact('biayaKapal'));
    }

    /**
     * Print biaya dokumen specifically.
     */
    public function printDokumen(BiayaKapal $biayaKapal)
    {
        $biayaKapal->load(['klasifikasiBiaya']);
        return view('biaya-kapal.print-dokumen', compact('biayaKapal'));
    }
    
    /**
     * Print biaya trucking specifically.
     */
    public function printTrucking(BiayaKapal $biayaKapal)
    {
        $biayaKapal->load(['klasifikasiBiaya']);
        return view('biaya-kapal.print-trucking', compact('biayaKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BiayaKapal $biayaKapal)
    {
        // Load relationships
        $biayaKapal->load(['barangDetails.pricelistBuruh', 'airDetails', 'tkbmDetails.pricelistTkbm', 'operasionalDetails']);

        // Get list of ships for dropdown
        $kapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();

        // Get active klasifikasi biaya for jenis biaya dropdown
        $klasifikasiBiayas = KlasifikasiBiaya::where('is_active', true)->orderBy('nama')->get();

        // Get active pricelist buruh for barang selection
        $pricelistBuruh = PricelistBuruh::where('is_active', true)->orderBy('barang')->get();

        // Get karyawans for penerima dropdown
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        // Get active pricelist biaya dokumen for vendor selection
        $pricelistBiayaDokumen = DB::table('pricelist_biaya_dokumen')
            ->where('status', 'aktif')
            ->orderBy('nama_vendor')
            ->get();
        
        // Get active pricelist air tawar for biaya air
        $pricelistAirTawar = \App\Models\MasterPricelistAirTawar::orderBy('nama_agen')->get();

        // Get active pricelist TKBM for biaya TKBM barang selection
        $pricelistTkbm = \App\Models\PricelistTkbm::where('status', 'active')->orderBy('nama_barang')->get();

        return view('biaya-kapal.edit', compact('biayaKapal', 'kapals', 'klasifikasiBiayas', 'pricelistBuruh', 'karyawans', 'pricelistBiayaDokumen', 'pricelistAirTawar', 'pricelistTkbm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BiayaKapal $biayaKapal)
    {
        // Clean up all currency and numeric fields before validation
        $data = $request->all();
        
        // Root fields
        $fieldsToClean = ['nominal', 'ppn', 'pph', 'total_biaya', 'dp', 'sisa_pembayaran', 'pph_dokumen', 'grand_total_dokumen', 'biaya_materai', 'jasa_air', 'pph_air', 'grand_total_air'];
        foreach ($fieldsToClean as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $data[$field] = str_replace(',', '.', str_replace('.', '', $data[$field]));
            }
        }
        
        // Operasional Sections
        if (isset($data['operasional_sections']) && is_array($data['operasional_sections'])) {
            foreach ($data['operasional_sections'] as &$section) {
                if (isset($section['nominal'])) $section['nominal'] = str_replace(',', '.', str_replace('.', '', $section['nominal']));
            }
            unset($section);
        }
        
        // Air Sections Cleaning
        if (isset($data['air']) && is_array($data['air'])) {
            foreach ($data['air'] as &$section) {
                $numericAir = ['kuantitas', 'harga', 'jasa_air', 'biaya_agen', 'sub_total', 'pph', 'grand_total', 'sub_total_value', 'pph_value', 'grand_total_value'];
                foreach ($numericAir as $f) {
                    if (isset($section[$f])) $section[$f] = str_replace(',', '.', str_replace('.', '', $section[$f]));
                }
            }
        }
        
        $request->replace($data);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nomor_invoice' => 'required|string|max:50|unique:biaya_kapals,nomor_invoice,' . $biayaKapal->id,
            'nomor_referensi' => 'nullable|string|max:100',
            'nama_kapal' => 'nullable|string|max:255', 
            'jenis_biaya' => 'required|exists:klasifikasi_biayas,kode',
            'nominal' => 'required|numeric|min:0',
            'nama_vendor' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
            
            // Operasional sections validation
            'operasional_sections' => 'nullable|array',
            'operasional_sections.*.kapal' => 'nullable|string|max:255',
            'operasional_sections.*.voyage' => 'nullable|string|max:255',
            'operasional_sections.*.nominal' => 'nullable|numeric|min:0',
            
            // Air sections validation
            'air' => 'nullable|array',
            'air.*.kapal' => 'nullable|string|max:255',
            'air.*.voyage' => 'nullable|string|max:255',
            'air.*.vendor' => 'nullable|string|max:255',
            'air.*.kuantitas' => 'nullable|numeric|min:0',
            'air.*.harga' => 'nullable|numeric|min:0',
            'air.*.penerima' => 'nullable|string|max:255',
            'air.*.nomor_rekening' => 'nullable|string|max:100',
            'air.*.nomor_referensi' => 'nullable|string|max:100',
            'air.*.tanggal_invoice_vendor' => 'nullable|date',
            
            // TKBM sections validation
            'tkbm_sections' => 'nullable|array',
            'tkbm_sections.*.kapal' => 'nullable|string|max:255',
            'tkbm_sections.*.voyage' => 'nullable|string|max:255',
            'tkbm_sections.*.no_referensi' => 'nullable|string|max:100',
            'tkbm_sections.*.tanggal_invoice_vendor' => 'nullable|date',
            'tkbm_sections.*.barang' => 'nullable|array',
            'tkbm_sections.*.barang.*.barang_id' => 'nullable|exists:pricelist_tkbms,id',
            'tkbm_sections.*.barang.*.jumlah' => 'nullable|numeric|min:0',
            'tkbm_sections.*.total_nominal' => 'nullable|numeric|min:0',
            'tkbm_sections.*.pph' => 'nullable|numeric|min:0',
            'tkbm_sections.*.grand_total' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('bukti')) {
                if ($biayaKapal->bukti) {
                    Storage::disk('public')->delete($biayaKapal->bukti);
                }
                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }
            
            $biayaKapal->update($validated);

            // AIR UPDATE
            if ($request->has('air')) {
                BiayaKapalAir::where('biaya_kapal_id', $biayaKapal->id)->delete();
                if (!empty($request->air)) {
                    foreach ($request->air as $section) {
                        if (empty($section['kapal']) && empty($section['vendor'])) continue;
                        
                        $subTotalRaw = $section['sub_total'] ?? $section['sub_total_value'] ?? 0;
                        $pphRaw = $section['pph'] ?? $section['pph_value'] ?? 0;
                        $grandTotalRaw = $section['grand_total'] ?? $section['grand_total_value'] ?? 0;

                        $subTotal = is_string($subTotalRaw) ? (floatval(str_replace(',', '.', str_replace('.', '', $subTotalRaw)))) : floatval($subTotalRaw);
                        $pph = is_string($pphRaw) ? (floatval(str_replace(',', '.', str_replace('.', '', $pphRaw)))) : floatval($pphRaw);
                        $grandTotal = is_string($grandTotalRaw) ? (floatval(str_replace(',', '.', str_replace('.', '', $grandTotalRaw)))) : floatval($grandTotalRaw);
                        
                        $typeKeterangan = null;
                        if (!empty($section['type'])) {
                            $typeData = DB::table('master_pricelist_air_tawar')->where('id', $section['type'])->first();
                            $typeKeterangan = $typeData ? $typeData->keterangan : null;
                        }

                        BiayaKapalAir::create([
                            'biaya_kapal_id' => $biayaKapal->id,
                            'kapal' => $section['kapal'] ?? null,
                            'voyage' => $section['voyage'] ?? null,
                            'vendor' => $section['vendor'] ?? null,
                            'lokasi' => $section['lokasi'] ?? null,
                            'type_id' => $section['type'] ?? null,
                            'type_keterangan' => $typeKeterangan,
                            'kuantitas' => floatval($section['kuantitas'] ?? 0),
                            'harga' => floatval($section['harga'] ?? 0),
                            'jasa_air' => floatval($section['jasa_air'] ?? 0),
                            'biaya_agen' => floatval($section['biaya_agen'] ?? 0),
                            'sub_total' => $subTotal,
                            'pph' => $pph,
                            'grand_total' => $grandTotal,
                            'penerima' => $section['penerima'] ?? null,
                            'nomor_rekening' => $section['nomor_rekening'] ?? null,
                            'nomor_referensi' => $section['nomor_referensi'] ?? null,
                            'tanggal_invoice_vendor' => $section['tanggal_invoice_vendor'] ?? null,
                        ]);
                    }
                }
            }

            // TKBM UPDATE
            if ($request->has('tkbm_sections')) {
                BiayaKapalTkbm::where('biaya_kapal_id', $biayaKapal->id)->delete();
                if (!empty($request->tkbm_sections)) {
                    foreach ($request->tkbm_sections as $section) {
                        if (empty($section['kapal']) && empty($section['barang'])) continue;
                        
                        if (isset($section['barang']) && is_array($section['barang'])) {
                            foreach ($section['barang'] as $item) {
                                $barangId = is_string($item['barang_id'] ?? null) ? trim($item['barang_id']) : ($item['barang_id'] ?? null);
                                $jumlah = floatval($item['jumlah'] ?? 0);
                                if (empty($barangId) || $jumlah <= 0) continue;
                                
                                $barang = PricelistTkbm::find($barangId);
                                if (!$barang) continue;
                                
                                BiayaKapalTkbm::create([
                                    'biaya_kapal_id' => $biayaKapal->id,
                                    'pricelist_tkbm_id' => $barang->id,
                                    'kapal' => $section['kapal'] ?? null,
                                    'voyage' => $section['voyage'] ?? null,
                                    'no_referensi' => $section['no_referensi'] ?? null,
                                    'tanggal_invoice_vendor' => $section['tanggal_invoice_vendor'] ?? null,
                                    'jumlah' => $jumlah,
                                    'tarif' => $barang->tarif,
                                    'subtotal' => $barang->tarif * $jumlah,
                                    'total_nominal' => is_string($section['total_nominal'] ?? 0) ? (floatval(str_replace(',', '.', str_replace('.', '', (string)$section['total_nominal'])))) : floatval($section['total_nominal'] ?? 0),
                                    'pph' => is_string($section['pph'] ?? 0) ? (floatval(str_replace(',', '.', str_replace('.', '', (string)$section['pph'])))) : floatval($section['pph'] ?? 0),
                                    'grand_total' => is_string($section['grand_total'] ?? 0) ? (floatval(str_replace(',', '.', str_replace('.', '', (string)$section['grand_total'])))) : floatval($section['grand_total'] ?? 0),
                                ]);
                            }
                        }
                    }
                }

                // AUTO-CALCULATE NOMINAL FOR TKBM
                $totalGrandTotal = BiayaKapalTkbm::where('biaya_kapal_id', $biayaKapal->id)
                    ->get()
                    ->groupBy(function($item) {
                        return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->no_referensi ?? '-') . '|' . ($item->tanggal_invoice_vendor ?? '-');
                    })
                    ->map(function($group) {
                        return $group->first()->grand_total ?? 0;
                    })
                    ->sum();
                $biayaKapal->update(['nominal' => $totalGrandTotal]);
            }

            // OPERASIONAL UPDATE
            if ($request->has('operasional_sections')) {
                BiayaKapalOperasional::where('biaya_kapal_id', $biayaKapal->id)->delete();
                if (!empty($request->operasional_sections)) {
                    foreach ($request->operasional_sections as $section) {
                        BiayaKapalOperasional::create([
                            'biaya_kapal_id' => $biayaKapal->id,
                            'kapal' => $section['kapal'] ?? null,
                            'voyage' => $section['voyage'] ?? null,
                            'nominal' => floatval($section['nominal'] ?? 0),
                            'total_nominal' => 0,
                            'dp' => 0,
                            'sisa_pembayaran' => 0,
                        ]);
                    }
                }
                $biayaKapal->update(['nominal' => BiayaKapalOperasional::where('biaya_kapal_id', $biayaKapal->id)->sum('nominal')]);
            }

            DB::commit();
            return redirect()->route('biaya-kapal.index')->with('success', 'Data biaya kapal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data biaya kapal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BiayaKapal $biayaKapal)
    {
        try {
            // Delete file if exists
            if ($biayaKapal->bukti) {
                Storage::disk('public')->delete($biayaKapal->bukti);
            }

            $biayaKapal->delete();

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data biaya kapal: ' . $e->getMessage());
        }
    }

    /**
     * Get voyages by ship name for AJAX request
     */
    public function getVoyagesByShip($namaKapal)
    {
        try {
            // Log incoming parameter for debugging
            Log::info('getVoyagesByShip called', ['nama_kapal' => $namaKapal]);

            // Normalize ship name for flexible matching (remove dots, extra spaces, lowercase)
            $normalizedKapal = strtolower(trim(preg_replace('/[.\s]+/', ' ', $namaKapal)));
            $allKeywords = explode(' ', $normalizedKapal);
            
            // List of common prefixes to ignore during search
            $ignorePrefixes = ['km', 'mv', 'mt', 'tb', 'spob', 'klm', 'lp', 'mp'];
            
            // Filter keywords
            $keywords = array_filter($allKeywords, function($word) use ($ignorePrefixes) {
                // Keep the word if it's NOT in the ignore list
                // Also keep numerical parts regardless (e.g., '178')
                return !in_array($word, $ignorePrefixes);
            });
            
            // If filtering resulted in empty array (unlikely but possible), revert to all keywords
            if (empty($keywords)) {
                $keywords = $allKeywords;
            }
            
            // Re-index array
            $keywords = array_values($keywords);
            
            Log::info('getVoyagesByShip keywords', ['original' => $allKeywords, 'filtered' => $keywords]);

            // Use robust keyword matching for Naik Kapal query
            $voyagesFromNaikKapalQuery = DB::table('naik_kapal')
                ->select('no_voyage')
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '');
            
            // Add where clause for each keyword
            $voyagesFromNaikKapalQuery->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where('nama_kapal', 'like', "%{$keyword}%");
                }
            });
            
            $voyagesFromNaikKapal = $voyagesFromNaikKapalQuery->distinct()->pluck('no_voyage');

            // Use robust keyword matching for BLs query
            $voyagesFromBlsQuery = DB::table('bls')
                ->select('no_voyage')
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '');
            
            // Add where clause for each keyword
            $voyagesFromBlsQuery->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where('nama_kapal', 'like', "%{$keyword}%");
                }
            });
            
            $voyagesFromBls = $voyagesFromBlsQuery->distinct()->pluck('no_voyage');

            // Merge and get unique voyages
            $voyages = $voyagesFromNaikKapal->merge($voyagesFromBls)
                ->unique()
                ->sort()
                ->values();

            Log::info('getVoyagesByShip results', ['nama_kapal' => $namaKapal, 'voyages_count' => count($voyages), 'voyages_sample' => array_slice($voyages->toArray(),0,5)]);

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
            Log::error('getVoyagesByShip error', ['error' => $e->getMessage(), 'nama_kapal' => $namaKapal]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data voyage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get BL numbers by voyages for AJAX request
     */
    public function getBlsByVoyages(Request $request)
    {
        try {
            $voyages = $request->input('voyages', []);
            
            if (empty($voyages)) {
                return response()->json([
                    'success' => true,
                    'bls' => []
                ]);
            }

            // Get BL data with kontainer and seal from bls table for the selected voyages
            $bls = DB::table('bls')
                ->select('id', 'nomor_kontainer', 'no_seal')
                ->whereIn('no_voyage', $voyages)
                ->whereNotNull('nomor_kontainer')
                ->where('nomor_kontainer', '!=', '')
                ->get()
                ->mapWithKeys(function($bl) {
                    return [$bl->id => [
                        'kontainer' => $bl->nomor_kontainer ?? 'N/A',
                        'seal' => $bl->no_seal ?? 'N/A'
                    ]];
                });

            return response()->json([
                'success' => true,
                'bls' => $bls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data BL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get container counts (FULL/EMPTY) grouped by size from BLs
     * Empty containers are determined by nama_barang containing "empty" or "kosong"
     */
    public function getContainerCounts(Request $request)
    {
        try {
            $kapalNama = $request->input('kapal');
            $voyage = $request->input('voyage');
            
            if (empty($kapalNama) || empty($voyage)) {
                return response()->json([
                    'success' => true,
                    'counts' => []
                ]);
            }

            // Get BL data for the selected kapal and voyage
            // Exclude CARGO containers from calculation
            $bls = DB::table('bls')
                ->select('nama_barang', 'size_kontainer', 'nomor_kontainer', 'tipe_kontainer')
                ->where('nama_kapal', $kapalNama)
                ->where('no_voyage', $voyage)
                ->whereNotNull('nomor_kontainer')
                ->where('nomor_kontainer', '!=', '')
                ->where('nomor_kontainer', '!=', 'CARGO')
                ->where(function($query) {
                    $query->where('tipe_kontainer', '!=', 'CARGO')
                          ->orWhereNull('tipe_kontainer');
                })
                ->get();

            // Count containers by size and type (FULL/EMPTY)
            $counts = [
                '20' => ['full' => 0, 'empty' => 0],
                '40' => ['full' => 0, 'empty' => 0],
            ];

            foreach ($bls as $bl) {
                // Determine size (default to 20 if not specified)
                $size = '20';
                if (!empty($bl->size_kontainer)) {
                    if (str_contains($bl->size_kontainer, '40')) {
                        $size = '40';
                    }
                }

                // Determine if EMPTY based on nama_barang
                $namaBarang = strtolower($bl->nama_barang ?? '');
                $isEmpty = str_contains($namaBarang, 'empty') || 
                           str_contains($namaBarang, 'kosong') ||
                           str_contains($namaBarang, 'mty') ||
                           str_contains($namaBarang, 'mt container');

                if ($isEmpty) {
                    $counts[$size]['empty']++;
                } else {
                    $counts[$size]['full']++;
                }
            }

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung kontainer: ' . $e->getMessage()
            ], 500);
        }
    }
}

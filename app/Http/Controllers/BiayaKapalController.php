<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\MasterKapal;
use App\Models\KlasifikasiBiaya;
use App\Models\PricelistBuruh;
use App\Models\BiayaKapalBarang;
use App\Models\BiayaKapalAir;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BiayaKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaKapal::with(['klasifikasiBiaya', 'barangDetails.pricelistBuruh']);

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
        $pricelistBiayaDokumen = \DB::table('pricelist_biaya_dokumen')
            ->where('status', 'aktif')
            ->orderBy('nama_vendor')
            ->get();
        
        // Get active pricelist air tawar for biaya air
        $pricelistAirTawar = \App\Models\MasterPricelistAirTawar::orderBy('nama_agen')->get();

        return view('biaya-kapal.create', compact('kapals', 'klasifikasiBiayas', 'pricelistBuruh', 'karyawans', 'pricelistBiayaDokumen', 'pricelistAirTawar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Clean up all currency fields before validation (remove thousand separator)
        $fieldsToClean = ['nominal', 'ppn', 'pph', 'total_biaya', 'dp', 'sisa_pembayaran', 'pph_dokumen', 'grand_total_dokumen', 'biaya_materai', 'jasa_air', 'pph_air', 'grand_total_air'];
        foreach ($fieldsToClean as $field) {
            if ($request->has($field) && $request->$field) {
                $request->merge([
                    $field => str_replace('.', '', $request->$field)
                ]);
            }
        }
        
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
            'air.*.lokasi' => 'nullable|string|max:255',
            'air.*.sub_total' => 'nullable|numeric|min:0',
            'air.*.pph' => 'nullable|numeric|min:0',
            'air.*.grand_total' => 'nullable|numeric|min:0',
            'air.*.penerima' => 'nullable|string|max:255',
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
                foreach ($request->kapal_sections as $sectionIndex => $section) {
                    $kapalName = $section['kapal'];
                    $voyageName = $section['voyage'];
                    $sectionTotalNominal = isset($section['total_nominal']) ? str_replace('.', '', $section['total_nominal']) : 0;
                    $sectionDp = isset($section['dp']) ? str_replace('.', '', $section['dp']) : 0;
                    $sectionSisa = isset($section['sisa_pembayaran']) ? str_replace('.', '', $section['sisa_pembayaran']) : 0;
                    
                    if (isset($section['barang']) && is_array($section['barang'])) {
                        foreach ($section['barang'] as $item) {
                            $barang = PricelistBuruh::find($item['barang_id']);
                            if ($barang) {
                                $subtotal = $barang->tarif * $item['jumlah'];
                                
                                // Save to biaya_kapal_barang table with kapal, voyage, and DP info
                                BiayaKapalBarang::create([
                                    'biaya_kapal_id' => $biayaKapal->id,
                                    'pricelist_buruh_id' => $barang->id,
                                    'kapal' => $kapalName,
                                    'voyage' => $voyageName,
                                    'jumlah' => $item['jumlah'],
                                    'tarif' => $barang->tarif,
                                    'subtotal' => $subtotal,
                                    'total_nominal' => $sectionTotalNominal,
                                    'dp' => $sectionDp,
                                    'sisa_pembayaran' => $sectionSisa,
                                ]);

                                // Build keterangan string with kapal, voyage, and DP info
                                $barangDetails[] = "[$kapalName - Voyage $voyageName] " . $barang->barang . ' x ' . $item['jumlah'] . ' = Rp ' . number_format($subtotal, 0, ',', '.');
                            }
                        }
                        
                        // Add section summary to barang details
                        if ($sectionDp > 0 || $sectionSisa > 0) {
                            $barangDetails[] = "  → Total: Rp " . number_format($sectionTotalNominal, 0, ',', '.') . 
                                             " | DP: Rp " . number_format($sectionDp, 0, ',', '.') . 
                                             " | Sisa: Rp " . number_format($sectionSisa, 0, ',', '.');
                        }
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
            
            // Update keterangan with barang details
            if (!empty($barangDetails)) {
                $keteranganBarang = "Detail Barang Buruh:\n" . implode("\n", $barangDetails);
                $biayaKapal->keterangan = $biayaKapal->keterangan 
                    ? $biayaKapal->keterangan . "\n\n" . $keteranganBarang 
                    : $keteranganBarang;
                $biayaKapal->save();
            }

            // BIAYA AIR SECTIONS: Store air details
            $airDetails = [];
            if ($request->has('air') && !empty($request->air)) {
                foreach ($request->air as $sectionIndex => $section) {
                    // Skip empty sections
                    if (empty($section['kapal']) && empty($section['vendor'])) {
                        continue;
                    }
                    
                    // Clean numeric values
                    $kuantitas = isset($section['kuantitas']) ? floatval(str_replace(['.', ','], ['', '.'], $section['kuantitas'])) : 0;
                    $harga = isset($section['harga']) ? floatval(str_replace(['.', ','], ['', '.'], $section['harga'])) : 0;
                    $jasaAir = isset($section['jasa_air']) ? floatval(str_replace(['.', ','], ['', '.'], $section['jasa_air'])) : 0;
                    // Prefer numeric fields submitted as 'sub_total', 'pph', 'grand_total' (hidden inputs). Fall back to older *_value names if present.
                    if (isset($section['sub_total'])) {
                        $subTotal = floatval(str_replace([',', '.'], ['', '.'], $section['sub_total']));
                    } elseif (isset($section['sub_total_value'])) {
                        $subTotal = floatval(str_replace([',', '.'], ['', '.'], $section['sub_total_value']));
                    } else {
                        $subTotal = 0;
                    }

                    if (isset($section['pph'])) {
                        $pph = floatval(str_replace([',', '.'], ['', '.'], $section['pph']));
                    } elseif (isset($section['pph_value'])) {
                        $pph = floatval(str_replace([',', '.'], ['', '.'], $section['pph_value']));
                    } else {
                        $pph = 0;
                    }

                    if (isset($section['grand_total'])) {
                        $grandTotal = floatval(str_replace([',', '.'], ['', '.'], $section['grand_total']));
                    } elseif (isset($section['grand_total_value'])) {
                        $grandTotal = floatval(str_replace([',', '.'], ['', '.'], $section['grand_total_value']));
                    } else {
                        $grandTotal = 0;
                    }
                    
                    // Get type keterangan from pricelist
                    $typeKeterangan = null;
                    if (!empty($section['type'])) {
                        $typeData = \DB::table('master_pricelist_air_tawar_type')
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
                        'sub_total' => $subTotal,
                        'pph' => $pph,
                        'grand_total' => $grandTotal,
                        'penerima' => $section['penerima'] ?? null,
                    ]);
                    
                    // Build keterangan string
                    $airDetails[] = "[" . ($section['kapal'] ?? 'N/A') . " - Voyage " . ($section['voyage'] ?? 'N/A') . "] " .
                        "Vendor: " . ($section['vendor'] ?? 'N/A') . " | " .
                        "Kuantitas: " . number_format($kuantitas, 2, ',', '.') . " ton | " .
                        "Sub Total: Rp " . number_format($subTotal, 0, ',', '.') . " | " .
                        "PPH: Rp " . number_format($pph, 0, ',', '.') . " | " .
                        "Grand Total: Rp " . number_format($grandTotal, 0, ',', '.');
                }
                
                // Update keterangan with air details
                if (!empty($airDetails)) {
                    $keteranganAir = "Detail Biaya Air:\n" . implode("\n", $airDetails);
                    $biayaKapal->keterangan = $biayaKapal->keterangan 
                        ? $biayaKapal->keterangan . "\n\n" . $keteranganAir 
                        : $keteranganAir;
                    $biayaKapal->save();
                }
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
        $biayaKapal->load(['klasifikasiBiaya', 'barangDetails.pricelistBuruh', 'airDetails']);
        
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
        // Get list of ships for dropdown (optional enhancement)
        $kapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();

        // Get active klasifikasi biaya for jenis biaya dropdown
        $klasifikasiBiayas = KlasifikasiBiaya::where('is_active', true)->orderBy('nama')->get();

        return view('biaya-kapal.edit', compact('biayaKapal', 'kapals', 'klasifikasiBiayas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BiayaKapal $biayaKapal)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nomor_invoice' => 'required|string|max:20|unique:biaya_kapals,nomor_invoice,' . $biayaKapal->id,
            'nomor_referensi' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'jenis_biaya' => 'required|exists:klasifikasi_biayas,kode',
            'nominal' => 'required|numeric|min:0',
            'nama_vendor' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ]);

        try {
            // Remove formatting from nominal (remove dots, convert comma to dot)
            $nominal = str_replace(['.', ','], ['', '.'], $validated['nominal']);
            $validated['nominal'] = $nominal;

            // Handle file upload
            if ($request->hasFile('bukti')) {
                // Delete old file if exists
                if ($biayaKapal->bukti) {
                    Storage::disk('public')->delete($biayaKapal->bukti);
                }

                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }

            $biayaKapal->update($validated);

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data biaya kapal: ' . $e->getMessage());
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
            \Log::info('getVoyagesByShip called', ['nama_kapal' => $namaKapal]);

            // Normalize ship name for flexible matching (remove dots, extra spaces, lowercase)
            $normalizedKapal = strtolower(trim(preg_replace('/[.\s]+/', ' ', $namaKapal)));
            \Log::info('getVoyagesByShip normalized', ['normalized' => $normalizedKapal]);

            // Primary attempt: Use REGEXP_REPLACE normalization if available in DB (MySQL 8+ / Postgres)
            $voyagesFromNaikKapal = \DB::table('naik_kapal')
                ->select('no_voyage')
                ->whereRaw('LOWER(TRIM(REGEXP_REPLACE(nama_kapal, "[.\\\\s]+", " "))) LIKE ?', ["%{$normalizedKapal}%"])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->pluck('no_voyage');

            $voyagesFromBls = \DB::table('bls')
                ->select('no_voyage')
                ->whereRaw('LOWER(TRIM(REGEXP_REPLACE(nama_kapal, "[.\\\\s]+", " "))) LIKE ?', ["%{$normalizedKapal}%"])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->pluck('no_voyage');

            // Fallback: if no results (maybe REGEXP_REPLACE unsupported or data mismatch), try simpler LIKE matching
            if ((empty($voyagesFromNaikKapal) || $voyagesFromNaikKapal->count() === 0) && (empty($voyagesFromBls) || $voyagesFromBls->count() === 0)) {
                \Log::info('getVoyagesByShip fallback to simple LIKE');

                $voyagesFromNaikKapal = \DB::table('naik_kapal')
                    ->select('no_voyage')
                    ->where('nama_kapal', 'like', "%{$namaKapal}%")
                    ->whereNotNull('no_voyage')
                    ->where('no_voyage', '!=', '')
                    ->distinct()
                    ->pluck('no_voyage');

                $voyagesFromBls = \DB::table('bls')
                    ->select('no_voyage')
                    ->where('nama_kapal', 'like', "%{$namaKapal}%")
                    ->whereNotNull('no_voyage')
                    ->where('no_voyage', '!=', '')
                    ->distinct()
                    ->pluck('no_voyage');
            }

            // Merge and get unique voyages
            $voyages = $voyagesFromNaikKapal->merge($voyagesFromBls)
                ->unique()
                ->sort()
                ->values();

            \Log::info('getVoyagesByShip results', ['nama_kapal' => $namaKapal, 'voyages_count' => count($voyages), 'voyages_sample' => array_slice($voyages->toArray(),0,5)]);

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
            \Log::error('getVoyagesByShip error', ['error' => $e->getMessage(), 'nama_kapal' => $namaKapal]);
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
            $bls = \DB::table('bls')
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
            $bls = \DB::table('bls')
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

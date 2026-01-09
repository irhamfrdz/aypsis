<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\MasterKapal;
use App\Models\KlasifikasiBiaya;
use App\Models\PricelistBuruh;
use App\Models\BiayaKapalBarang;
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
        $query = BiayaKapal::query();

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

        return view('biaya-kapal.create', compact('kapals', 'klasifikasiBiayas', 'pricelistBuruh'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nomor_referensi' => 'nullable|string|max:100',
            'nama_kapal' => 'required|array|min:1',
            'nama_kapal.*' => 'string|max:255',
            'no_voyage' => 'nullable|array',
            'no_voyage.*' => 'string',
            'jenis_biaya' => 'required|exists:klasifikasi_biayas,kode',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
            'barang' => 'nullable|array',
            'barang.*.barang_id' => 'required|exists:pricelist_buruh,id',
            'barang.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Remove formatting from nominal (remove dots, convert comma to dot)
            $nominal = str_replace(['.', ','], ['', '.'], $validated['nominal']);
            $validated['nominal'] = $nominal;

            // Handle file upload
            if ($request->hasFile('bukti')) {
                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }

            // Create BiayaKapal record
            $biayaKapal = BiayaKapal::create($validated);

            // Store barang details in separate table if exists
            if ($request->has('barang') && !empty($request->barang)) {
                $barangDetails = [];
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
                
                // Update keterangan with barang details
                if (!empty($barangDetails)) {
                    $keteranganBarang = "Detail Barang Buruh:\n" . implode("\n", $barangDetails);
                    $biayaKapal->keterangan = $biayaKapal->keterangan 
                        ? $biayaKapal->keterangan . "\n\n" . $keteranganBarang 
                        : $keteranganBarang;
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
        return view('biaya-kapal.show', compact('biayaKapal'));
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
            'nomor_referensi' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'jenis_biaya' => 'required|exists:klasifikasi_biayas,kode',
            'nominal' => 'required|numeric|min:0',
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
            // Normalize ship name for flexible matching (remove dots, extra spaces, lowercase)
            $normalizedKapal = strtolower(trim(preg_replace('/[.\s]+/', ' ', $namaKapal)));
            
            // Get distinct no_voyage from naik_kapal for the selected ship
            $voyagesFromNaikKapal = \DB::table('naik_kapal')
                ->select('no_voyage')
                ->whereRaw('LOWER(TRIM(REGEXP_REPLACE(nama_kapal, "[.\\\\s]+", " "))) LIKE ?', ["%{$normalizedKapal}%"])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->pluck('no_voyage');

            // Get distinct no_voyage from bls for the selected ship
            $voyagesFromBls = \DB::table('bls')
                ->select('no_voyage')
                ->whereRaw('LOWER(TRIM(REGEXP_REPLACE(nama_kapal, "[.\\\\s]+", " "))) LIKE ?', ["%{$normalizedKapal}%"])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->pluck('no_voyage');

            // Merge and get unique voyages
            $voyages = $voyagesFromNaikKapal->merge($voyagesFromBls)
                ->unique()
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);
        } catch (\Exception $e) {
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
}

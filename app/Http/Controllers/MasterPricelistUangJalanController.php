<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistUangJalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterPricelistUangJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistUangJalan::with(['creator', 'updater']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan cabang
        if ($request->filled('cabang')) {
            $query->byCabang($request->cabang);
        }

        // Filter berdasarkan wilayah
        if ($request->filled('wilayah')) {
            $query->byWilayah($request->wilayah);
        }

        // Search berdasarkan dari/ke
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dari', 'LIKE', "%{$search}%")
                  ->orWhere('ke', 'LIKE', "%{$search}%")
                  ->orWhere('kode', 'LIKE', "%{$search}%")
                  ->orWhere('wilayah', 'LIKE', "%{$search}%");
            });
        }

        $pricelistData = $query->orderBy('cabang')
                              ->orderBy('wilayah')
                              ->orderBy('ke')
                              ->paginate(20);

        // Get unique values untuk filter dropdown
        $cabangList = MasterPricelistUangJalan::select('cabang')
                                              ->distinct()
                                              ->orderBy('cabang')
                                              ->pluck('cabang');

        $wilayahList = MasterPricelistUangJalan::select('wilayah')
                                               ->distinct()
                                               ->orderBy('wilayah')
                                               ->pluck('wilayah');

        return view('master-pricelist-uang-jalan.index', compact(
            'pricelistData',
            'cabangList',
            'wilayahList'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get existing cabang untuk dropdown
        $cabangList = MasterPricelistUangJalan::select('cabang')
                                              ->distinct()
                                              ->orderBy('cabang')
                                              ->pluck('cabang');

        return view('master-pricelist-uang-jalan.create', compact('cabangList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cabang' => 'required|string|max:50',
            'wilayah' => 'required|string|max:100',
            'dari' => 'required|string|max:100',
            'ke' => 'required|string|max:100',
            'uang_jalan_20ft' => 'required|numeric|min:0',
            'uang_jalan_40ft' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'liter' => 'nullable|integer|min:0',
            'jarak_km' => 'nullable|numeric|min:0',
            'mel_20ft' => 'nullable|numeric|min:0',
            'mel_40ft' => 'nullable|numeric|min:0',
            'ongkos_truk_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_40ft' => 'nullable|numeric|min:0',
            'berlaku_dari' => 'required|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_dari'
        ]);

        try {
            DB::beginTransaction();

            $pricelist = MasterPricelistUangJalan::create(array_merge(
                $request->all(),
                [
                    'created_by' => Auth::id(),
                    'status' => 'active'
                ]
            ));

            DB::commit();

            return redirect()
                ->route('master-pricelist-uang-jalan.show', $pricelist)
                ->with('success', "Pricelist uang jalan berhasil dibuat dengan kode: {$pricelist->kode}");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat pricelist: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistUangJalan $masterPricelistUangJalan)
    {
        $pricelist = $masterPricelistUangJalan->load(['creator', 'updater']);

        return view('master-pricelist-uang-jalan.show', compact('pricelist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistUangJalan $masterPricelistUangJalan)
    {
        $pricelist = $masterPricelistUangJalan;

        // Get existing cabang untuk dropdown
        $cabangList = MasterPricelistUangJalan::select('cabang')
                                              ->distinct()
                                              ->orderBy('cabang')
                                              ->pluck('cabang');

        return view('master-pricelist-uang-jalan.edit', compact('pricelist', 'cabangList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistUangJalan $masterPricelistUangJalan)
    {
        $request->validate([
            'cabang' => 'required|string|max:50',
            'wilayah' => 'required|string|max:100',
            'dari' => 'required|string|max:100',
            'ke' => 'required|string|max:100',
            'uang_jalan_20ft' => 'required|numeric|min:0',
            'uang_jalan_40ft' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'liter' => 'nullable|integer|min:0',
            'jarak_km' => 'nullable|numeric|min:0',
            'mel_20ft' => 'nullable|numeric|min:0',
            'mel_40ft' => 'nullable|numeric|min:0',
            'ongkos_truk_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_40ft' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'berlaku_dari' => 'required|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_dari'
        ]);

        try {
            DB::beginTransaction();

            $masterPricelistUangJalan->update(array_merge(
                $request->all(),
                ['updated_by' => Auth::id()]
            ));

            DB::commit();

            return redirect()
                ->route('master-pricelist-uang-jalan.show', $masterPricelistUangJalan)
                ->with('success', 'Pricelist uang jalan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal update pricelist: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistUangJalan $masterPricelistUangJalan)
    {
        try {
            DB::beginTransaction();

            // Soft delete - ubah status jadi inactive
            $masterPricelistUangJalan->update([
                'status' => 'inactive',
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()
                ->route('master-pricelist-uang-jalan.index')
                ->with('success', 'Pricelist uang jalan berhasil dinonaktifkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus pricelist: ' . $e->getMessage());
        }
    }

    /**
     * Import dari CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $handle = fopen($request->file('csv_file')->getRealPath(), 'r');

            // Skip header
            fgetcsv($handle, 1000, ';');

            $imported = 0;
            $errors = [];

            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if (count($data) < 13) continue;

                try {
                    // Parse data from CSV
                    $pricelistData = [
                        'kode' => trim($data[0]),
                        'cabang' => trim($data[1]) ?: 'JKT',
                        'wilayah' => trim($data[2]),
                        'dari' => trim($data[3]),
                        'ke' => trim($data[4]),
                        'uang_jalan_20ft' => $this->parseNumber($data[5]),
                        'uang_jalan_40ft' => $this->parseNumber($data[6]),
                        'keterangan' => trim($data[7]),
                        'liter' => (int) ($data[8] ?: 0),
                        'jarak_km' => $this->parseNumber($data[9]),
                        'mel_20ft' => $this->parseNumber($data[10]),
                        'mel_40ft' => $this->parseNumber($data[11]),
                        'ongkos_truk_20ft' => $this->parseNumber($data[12]),
                        'antar_lokasi_20ft' => isset($data[13]) ? $this->parseNumber($data[13]) : 0,
                        'antar_lokasi_40ft' => isset($data[14]) ? $this->parseNumber($data[14]) : 0,
                        'status' => 'active',
                        'berlaku_dari' => now(),
                        'created_by' => Auth::id()
                    ];

                    // Skip jika data kosong
                    if (empty($pricelistData['wilayah']) || empty($pricelistData['ke'])) {
                        continue;
                    }

                    MasterPricelistUangJalan::create($pricelistData);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + count($errors) + 2) . ": " . $e->getMessage();
                }
            }

            fclose($handle);
            DB::commit();

            $message = "Import berhasil: {$imported} data ditambahkan";
            if (!empty($errors)) {
                $message .= ". " . count($errors) . " error: " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()
                ->route('master-pricelist-uang-jalan.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * API untuk cari pricelist berdasarkan rute
     */
    public function findByRoute(Request $request)
    {
        $dari = $request->get('dari');
        $ke = $request->get('ke');
        $ukuran = $request->get('ukuran');

        $pricelist = MasterPricelistUangJalan::findByRoute($dari, $ke, $ukuran);

        if ($pricelist) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pricelist->id,
                    'kode' => $pricelist->kode,
                    'uang_jalan_20ft' => $pricelist->uang_jalan_20ft,
                    'uang_jalan_40ft' => $pricelist->uang_jalan_40ft,
                    'uang_jalan_by_size' => $pricelist->getUangJalanBySize($ukuran),
                    'mel_by_size' => $pricelist->getMelBySize($ukuran),
                    'antar_lokasi_by_size' => $pricelist->getAntarLokasiBySize($ukuran),
                    'total_biaya' => $pricelist->getTotalBiaya($ukuran)
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pricelist tidak ditemukan untuk rute ini'
        ]);
    }

    /**
     * Parse number dari string (handle format ribuan dengan titik)
     */
    private function parseNumber($value)
    {
        if (empty($value)) return 0;

        // Remove spaces dan currency symbols
        $cleaned = preg_replace('/[^\d,.]/', '', $value);

        // Convert to standard decimal format
        $cleaned = str_replace('.', '', $cleaned); // Remove thousands separator
        $cleaned = str_replace(',', '.', $cleaned); // Convert comma to decimal point

        return (float) $cleaned;
    }
}

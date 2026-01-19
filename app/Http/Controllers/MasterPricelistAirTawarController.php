<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPricelistAirTawar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterPricelistAirTawarImport;
use Illuminate\Support\Facades\Log;

class MasterPricelistAirTawarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistAirTawar::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pricelistAirTawar = $query->orderBy('nama_agen')
                                  ->paginate($perPage)
                                  ->withQueryString();

        return view('master.pricelist-air-tawar.index', compact('pricelistAirTawar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-air-tawar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_agen' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|in:Jakarta,Batam,Pinang',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'nama_agen.required' => 'Nama agen harus diisi',
            'nama_agen.max' => 'Nama agen maksimal 255 karakter',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh negatif',
            'lokasi.required' => 'Lokasi harus dipilih',
            'lokasi.in' => 'Lokasi tidak valid',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            MasterPricelistAirTawar::create($request->all());
            
            return redirect()->route('master.pricelist-air-tawar.index')
                           ->with('success', 'Master Pricelist Air Tawar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistAirTawar $pricelistAirTawar)
    {
        return view('master.pricelist-air-tawar.show', compact('pricelistAirTawar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistAirTawar $pricelistAirTawar)
    {
        return view('master.pricelist-air-tawar.edit', compact('pricelistAirTawar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistAirTawar $pricelistAirTawar)
    {
        $validator = Validator::make($request->all(), [
            'nama_agen' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|in:Jakarta,Batam,Pinang',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'nama_agen.required' => 'Nama agen harus diisi',
            'nama_agen.max' => 'Nama agen maksimal 255 karakter',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh negatif',
            'lokasi.required' => 'Lokasi harus dipilih',
            'lokasi.in' => 'Lokasi tidak valid',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $pricelistAirTawar->update($request->all());
            
            return redirect()->route('master.pricelist-air-tawar.index')
                           ->with('success', 'Master Pricelist Air Tawar berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistAirTawar $pricelistAirTawar)
    {
        try {
            $pricelistAirTawar->delete();
            
            return redirect()->route('master.pricelist-air-tawar.index')
                           ->with('success', 'Master Pricelist Air Tawar berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Download a CSV template for pricelist air tawar import
     */
    public function exportTemplate()
    {
        $filename = 'template_pricelist_air_tawar_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Write header with semicolon delimiter
            fputcsv($file, ['nama_agen', 'harga', 'lokasi', 'keterangan'], ';');
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import pricelist air tawar from CSV/XLSX
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120'
        ]);

        $file = $request->file('file');
        $import = new MasterPricelistAirTawarImport();

        try {
            Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $errors = $import->getErrors();

            $message = "Import selesai. {$successCount} data berhasil diimpor.";
            if (!empty($errors)) {
                $message .= ' Error: ' . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
            }

            return redirect()->route('master.pricelist-air-tawar.index')->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\Exception $e) {
            // Extract readable errors
            $failures = [];
            if (method_exists($e, 'failures')) {
                foreach ($e->failures() as $failure) {
                    $failures[] = "Baris " . $failure->row() . ': ' . implode(', ', $failure->errors());
                }
            }
            $message = "Import gagal: " . implode('; ', $failures);
            return redirect()->route('master.pricelist-air-tawar.index')->with('error', $message);
        } catch (\Exception $e) {
            return redirect()->route('master.pricelist-air-tawar.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}

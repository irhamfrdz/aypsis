<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UangJalanBatam;
use App\Exports\UangJalanBatamTemplateExport;
use App\Imports\UangJalanBatamImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UangJalanBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:uang-jalan-batam.view')->only(['index', 'show']);
        $this->middleware('permission:uang-jalan-batam.create')->only(['create', 'store']);
        $this->middleware('permission:uang-jalan-batam.edit')->only(['edit', 'update']);
        $this->middleware('permission:uang-jalan-batam.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = UangJalanBatam::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('wilayah', 'like', "%{$search}%")
                  ->orWhere('rute', 'like', "%{$search}%")
                  ->orWhere('expedisi', 'like', "%{$search}%")
                  ->orWhere('ring', 'like', "%{$search}%")
                  ->orWhere('ft', 'like', "%{$search}%")
                  ->orWhere('f_e', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        $uangJalanBatams = $query->orderBy('wilayah')
                                ->orderBy('rute')
                                ->orderBy('expedisi')
                                ->paginate(15);
        
        return view('uang-jalan-batam.index', compact('uangJalanBatams', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('uang-jalan-batam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wilayah' => 'required|string|max:255',
            'rute' => 'required|string|max:255',
            'expedisi' => 'required|string|max:255',
            'ring' => 'required|string|max:255',
            'ft' => 'required|string|max:255',
            'f_e' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:aqua,chasis PB',
            'tanggal_awal_berlaku' => 'required|date',
            'tanggal_akhir_berlaku' => 'required|date|after_or_equal:tanggal_awal_berlaku',
        ]);

        UangJalanBatam::create($validated);

        return redirect()->route('uang-jalan-batam.index')
                        ->with('success', 'Data uang jalan Batam berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UangJalanBatam $uangJalanBatam)
    {
        return view('uang-jalan-batam.show', compact('uangJalanBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UangJalanBatam $uangJalanBatam)
    {
        return view('uang-jalan-batam.edit', compact('uangJalanBatam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UangJalanBatam $uangJalanBatam)
    {
        $validated = $request->validate([
            'wilayah' => 'required|string|max:255',
            'rute' => 'required|string|max:255',
            'expedisi' => 'required|string|max:255',
            'ring' => 'required|string|max:255',
            'ft' => 'required|string|max:255',
            'f_e' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:aqua,chasis PB',
            'tanggal_awal_berlaku' => 'required|date',
            'tanggal_akhir_berlaku' => 'required|date|after_or_equal:tanggal_awal_berlaku',
        ]);

        $uangJalanBatam->update($validated);

        return redirect()->route('uang-jalan-batam.index')
                        ->with('success', 'Data uang jalan Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UangJalanBatam $uangJalanBatam)
    {
        $uangJalanBatam->delete();

        return redirect()->route('uang-jalan-batam.index')
                        ->with('success', 'Data uang jalan Batam berhasil dihapus.');
    }

    /**
     * Download template for import
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new UangJalanBatamTemplateExport, 
            'template_uang_jalan_batam.xlsx'
        );
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('uang-jalan-batam.import');
    }

    /**
     * Import data from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new UangJalanBatamImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $failures = $import->failures();
            $errors = $import->errors();

            $message = "Import berhasil! {$importedCount} data berhasil diimport.";
            
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} data dilewati karena error.";
            }

            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                
                return redirect()->route('uang-jalan-batam.import-form')
                    ->with('import_errors', $errorMessages)
                    ->with('success', $message);
            }

            return redirect()->route('uang-jalan-batam.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('uang-jalan-batam.import-form')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}

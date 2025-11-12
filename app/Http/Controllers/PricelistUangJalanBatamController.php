<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PricelistUangJalanBatam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PricelistUangJalanBatamTemplateExport;
use App\Imports\PricelistUangJalanBatamImport;

class PricelistUangJalanBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = PricelistUangJalanBatam::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('expedisi', 'like', "%{$search}%")
                  ->orWhere('ring', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('f_e', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        $pricelists = $query->orderBy('expedisi')
                           ->orderBy('ring')
                           ->orderBy('size')
                           ->orderBy('f_e')
                           ->paginate($request->get('per_page', 15));
        
        return view('pricelist-uang-jalan-batam.index', compact('pricelists', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pricelist-uang-jalan-batam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expedisi' => 'required|string|max:255',
            'ring' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'f_e' => 'required|in:Full,Empty',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|in:AQUA,CHASIS PB',
        ]);

        PricelistUangJalanBatam::create($validated);

        return redirect()->route('pricelist-uang-jalan-batam.index')
            ->with('success', 'Pricelist berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistUangJalanBatam $pricelistUangJalanBatam)
    {
        return view('pricelist-uang-jalan-batam.show', compact('pricelistUangJalanBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistUangJalanBatam $pricelistUangJalanBatam)
    {
        return view('pricelist-uang-jalan-batam.edit', compact('pricelistUangJalanBatam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistUangJalanBatam $pricelistUangJalanBatam)
    {
        $validated = $request->validate([
            'expedisi' => 'required|string|max:255',
            'ring' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'f_e' => 'required|in:Full,Empty',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|in:AQUA,CHASIS PB',
        ]);

        $pricelistUangJalanBatam->update($validated);

        return redirect()->route('pricelist-uang-jalan-batam.index')
            ->with('success', 'Pricelist berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistUangJalanBatam $pricelistUangJalanBatam)
    {
        $pricelistUangJalanBatam->delete();

        return redirect()->route('pricelist-uang-jalan-batam.index')
            ->with('success', 'Pricelist berhasil dihapus!');
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new PricelistUangJalanBatamTemplateExport(), 
            'template_pricelist_uang_jalan_batam_' . date('YmdHis') . '.xlsx'
        );
    }

    /**
     * Import data from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls, atau csv)',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        try {
            $import = new PricelistUangJalanBatamImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $errors = $import->getErrors();
            
            $message = "Import selesai! Berhasil: {$successCount} data";
            
            if ($errorCount > 0) {
                $message .= ", Gagal: {$errorCount} data";
                
                return redirect()->route('pricelist-uang-jalan-batam.index')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('pricelist-uang-jalan-batam.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('pricelist-uang-jalan-batam.index')
                ->with('error', 'Import gagal! Error: ' . $e->getMessage() . ' (Cek log untuk detail)');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPricelistOb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterPricelistObImport;
use Illuminate\Support\Facades\Log;

class MasterPricelistObController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistOb::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('size_kontainer', 'like', "%{$search}%")
                  ->orWhere('status_kontainer', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan size kontainer
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        // Filter berdasarkan status kontainer
        if ($request->filled('status_kontainer')) {
            $query->where('status_kontainer', $request->status_kontainer);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pricelistOb = $query->orderBy('size_kontainer')
                            ->orderBy('status_kontainer')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('master.pricelist-ob.index', compact('pricelistOb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sizeOptions = MasterPricelistOb::getSizeKontainerOptions();
        $statusOptions = MasterPricelistOb::getStatusKontainerOptions();
        
        return view('master.pricelist-ob.create', compact('sizeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size_kontainer' => 'required|in:20ft,40ft',
            'status_kontainer' => 'required|in:full,empty',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'size_kontainer.required' => 'Size kontainer harus diisi',
            'size_kontainer.in' => 'Size kontainer tidak valid',
            'status_kontainer.required' => 'Status kontainer harus diisi',
            'status_kontainer.in' => 'Status kontainer tidak valid',
            'biaya.required' => 'Biaya harus diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya tidak boleh negatif',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek duplikasi
        $exists = MasterPricelistOb::where('size_kontainer', $request->size_kontainer)
                                  ->where('status_kontainer', $request->status_kontainer)
                                  ->exists();

        if ($exists) {
            return back()->with('error', 'Kombinasi size kontainer dan status kontainer sudah ada!')
                        ->withInput();
        }

        try {
            MasterPricelistOb::create($request->all());
            
            return redirect()->route('master.pricelist-ob.index')
                           ->with('success', 'Master Pricelist OB berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                        ->withInput();
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistOb $pricelistOb)
    {
        return view('master.pricelist-ob.show', compact('pricelistOb'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistOb $pricelistOb)
    {
        $sizeOptions = MasterPricelistOb::getSizeKontainerOptions();
        $statusOptions = MasterPricelistOb::getStatusKontainerOptions();
        
        return view('master.pricelist-ob.edit', compact('pricelistOb', 'sizeOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistOb $pricelistOb)
    {
        $validator = Validator::make($request->all(), [
            'size_kontainer' => 'required|in:20ft,40ft',
            'status_kontainer' => 'required|in:full,empty',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'size_kontainer.required' => 'Size kontainer harus diisi',
            'size_kontainer.in' => 'Size kontainer tidak valid',
            'status_kontainer.required' => 'Status kontainer harus diisi',
            'status_kontainer.in' => 'Status kontainer tidak valid',
            'biaya.required' => 'Biaya harus diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya tidak boleh negatif',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek duplikasi kecuali untuk record yang sedang diedit
        $exists = MasterPricelistOb::where('size_kontainer', $request->size_kontainer)
                                  ->where('status_kontainer', $request->status_kontainer)
                                  ->where('id', '!=', $pricelistOb->id)
                                  ->exists();

        if ($exists) {
            return back()->with('error', 'Kombinasi size kontainer dan status kontainer sudah ada!')
                        ->withInput();
        }

        try {
            $pricelistOb->update($request->all());
            
            return redirect()->route('master.pricelist-ob.index')
                           ->with('success', 'Master Pricelist OB berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistOb $pricelistOb)
    {
        try {
            $pricelistOb->delete();
            
            return redirect()->route('master.pricelist-ob.index')
                           ->with('success', 'Master Pricelist OB berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Download a CSV template for pricelist OB import
     */
    public function exportTemplate()
    {
        $filename = 'template_pricelist_ob_' . date('Y-m-d') . '.csv';

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
            fputcsv($file, ['size_kontainer', 'status_kontainer', 'biaya', 'keterangan'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import pricelist OB from CSV/XLSX
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120'
        ]);

        $file = $request->file('file');
        $import = new MasterPricelistObImport();

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

            return redirect()->route('master.pricelist-ob.index')->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\Exception $e) {
            // Extract readable errors
            $failures = [];
            if (method_exists($e, 'failures')) {
                foreach ($e->failures() as $failure) {
                    $failures[] = "Baris " . $failure->row() . ': ' . implode(', ', $failure->errors());
                }
            }
            $message = "Import gagal: " . implode('; ', $failures);
            return redirect()->route('master.pricelist-ob.index')->with('error', $message);
        } catch (\Exception $e) {
            return redirect()->route('master.pricelist-ob.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}

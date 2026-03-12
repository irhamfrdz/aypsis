<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPricelistFreight;
use App\Models\MasterPelabuhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterPricelistFreightImport;
use App\Exports\MasterPricelistFreightTemplateExport;

class MasterPricelistFreightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPricelistFreight::with(['asal', 'tujuan']);

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('size_kontainer', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pricelistFreight = $query->latest()
                            ->paginate($perPage)
                            ->withQueryString();

        return view('master.pricelist-freight.index', compact('pricelistFreight'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelabuhans = MasterPelabuhan::aktif()->orderBy('nama_pelabuhan')->get();
        $sizeOptions = MasterPricelistFreight::getSizeKontainerOptions();
        
        return view('master.pricelist-freight.create', compact('pelabuhans', 'sizeOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|string|in:Aktif,Tidak Aktif',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'nama_barang.required' => 'Nama barang harus diisi',
            'tarif.required' => 'Tarif harus diisi',
            'tarif.numeric' => 'Tarif harus berupa angka',
            'tarif.min' => 'Tarif tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            MasterPricelistFreight::create($request->all());
            
            return redirect()->route('master-pricelist-freight.index')
                           ->with('success', 'Master Pricelist Freight berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPricelistFreight $masterPricelistFreight)
    {
        return view('master.pricelist-freight.show', compact('masterPricelistFreight'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPricelistFreight $masterPricelistFreight)
    {
        $pelabuhans = MasterPelabuhan::aktif()->orderBy('nama_pelabuhan')->get();
        $sizeOptions = MasterPricelistFreight::getSizeKontainerOptions();
        
        return view('master.pricelist-freight.edit', compact('masterPricelistFreight', 'pelabuhans', 'sizeOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPricelistFreight $masterPricelistFreight)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|string|in:Aktif,Tidak Aktif',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'nama_barang.required' => 'Nama barang harus diisi',
            'tarif.required' => 'Tarif harus diisi',
            'tarif.numeric' => 'Tarif harus berupa angka',
            'tarif.min' => 'Tarif tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $masterPricelistFreight->update($request->all());
            
            return redirect()->route('master-pricelist-freight.index')
                           ->with('success', 'Master Pricelist Freight berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPricelistFreight $masterPricelistFreight)
    {
        try {
            $masterPricelistFreight->delete();
            
            return redirect()->route('master-pricelist-freight.index')
                           ->with('success', 'Master Pricelist Freight berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $import = new MasterPricelistFreightImport();
            Excel::import($import, $request->file('file'));

            $message = "Import berhasil: {$import->getSuccessCount()} data berhasil diimpor.";
            if ($import->getErrorCount() > 0) {
                $message .= " Namun ada {$import->getErrorCount()} baris yang bermasalah.";
                return redirect()->back()->with('success', $message)->with('import_errors', $import->getErrors());
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new MasterPricelistFreightTemplateExport(), 'template-pricelist-freight.xlsx');
    }
}

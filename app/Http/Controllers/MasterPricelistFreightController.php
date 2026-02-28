<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPricelistFreight;
use App\Models\MasterPelabuhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                $q->whereHas('asal', function($q2) use ($search) {
                        $q2->where('nama_pelabuhan', 'like', "%{$search}%");
                    })
                  ->orWhereHas('tujuan', function($q2) use ($search) {
                        $q2->where('nama_pelabuhan', 'like', "%{$search}%");
                    })
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
            'pelabuhan_asal_id' => 'required|exists:master_pelabuhans,id',
            'pelabuhan_tujuan_id' => 'required|exists:master_pelabuhans,id|different:pelabuhan_asal_id',
            'size_kontainer' => 'required|string',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'pelabuhan_asal_id.required' => 'Pelabuhan asal harus diisi',
            'pelabuhan_tujuan_id.required' => 'Pelabuhan tujuan harus diisi',
            'pelabuhan_tujuan_id.different' => 'Pelabuhan tujuan harus berbeda dengan pelabuhan asal',
            'size_kontainer.required' => 'Size kontainer harus diisi',
            'biaya.required' => 'Biaya harus diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya tidak boleh negatif',
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
            'pelabuhan_asal_id' => 'required|exists:master_pelabuhans,id',
            'pelabuhan_tujuan_id' => 'required|exists:master_pelabuhans,id|different:pelabuhan_asal_id',
            'size_kontainer' => 'required|string',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'pelabuhan_asal_id.required' => 'Pelabuhan asal harus diisi',
            'pelabuhan_tujuan_id.required' => 'Pelabuhan tujuan harus diisi',
            'pelabuhan_tujuan_id.different' => 'Pelabuhan tujuan harus berbeda dengan pelabuhan asal',
            'size_kontainer.required' => 'Size kontainer harus diisi',
            'biaya.required' => 'Biaya harus diisi',
            'biaya.numeric' => 'Biaya harus berupa angka',
            'biaya.min' => 'Biaya tidak boleh negatif',
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPricelistOb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}

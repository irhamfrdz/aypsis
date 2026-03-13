<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PricelistPerijinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterPricelistPerijinanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistPerijinan::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pricelistPerijinan = $query->latest()
                            ->paginate($perPage)
                            ->withQueryString();

        return view('master.pricelist-perijinan.index', compact('pricelistPerijinan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-perijinan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|string|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            PricelistPerijinan::create($request->all());
            
            return redirect()->route('master.pricelist-perijinan.index')
                           ->with('success', 'Master Pricelist Perijinan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistPerijinan $pricelistPerijinan)
    {
        return view('master.pricelist-perijinan.edit', compact('pricelistPerijinan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistPerijinan $pricelistPerijinan)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|string|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $pricelistPerijinan->update($request->all());
            
            return redirect()->route('master.pricelist-perijinan.index')
                           ->with('success', 'Master Pricelist Perijinan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistPerijinan $pricelistPerijinan)
    {
        try {
            $pricelistPerijinan->delete();
            
            return redirect()->route('master.pricelist-perijinan.index')
                           ->with('success', 'Master Pricelist Perijinan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UangJalanBatam;
use Illuminate\Http\Request;

class UangJalanBatamController extends Controller
{
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
            'tanggal_berlaku' => 'required|date',
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
            'tanggal_berlaku' => 'required|date',
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
}

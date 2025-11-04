<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TarifBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarifBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tarif-batam.view')->only(['index', 'show']);
        $this->middleware('permission:tarif-batam.create')->only(['create', 'store']);
        $this->middleware('permission:tarif-batam.edit')->only(['edit', 'update']);
        $this->middleware('permission:tarif-batam.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TarifBatam::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan masa berlaku
        if ($request->filled('masa_berlaku')) {
            if ($request->masa_berlaku === 'berlaku') {
                $query->berlaku();
            } elseif ($request->masa_berlaku === 'expired') {
                $query->where('masa_berlaku', '<', now()->toDateString());
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $tarifBatam = $query->orderBy('masa_berlaku', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate($perPage);

        return view('tarif-batam.index', compact('tarifBatam'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tarif-batam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chasis_ayp' => 'nullable|numeric|min:0',
            '20ft_full' => 'nullable|numeric|min:0',
            '20ft_empty' => 'nullable|numeric|min:0',
            'antar_lokasi' => 'nullable|numeric|min:0',
            '40ft_full' => 'nullable|numeric|min:0',
            '40ft_empty' => 'nullable|numeric|min:0',
            '40ft_antar_lokasi' => 'nullable|numeric|min:0',
            'masa_berlaku' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif'
        ], [
            'masa_berlaku.required' => 'Masa berlaku wajib diisi',
            'masa_berlaku.date' => 'Format tanggal masa berlaku tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau nonaktif',
            '*.numeric' => 'Tarif harus berupa angka',
            '*.min' => 'Tarif tidak boleh negatif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        TarifBatam::create($request->all());

        return redirect()->route('tarif-batam.index')
                        ->with('success', 'Tarif Batam berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TarifBatam $tarifBatam)
    {
        return view('tarif-batam.show', compact('tarifBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TarifBatam $tarifBatam)
    {
        return view('tarif-batam.edit', compact('tarifBatam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TarifBatam $tarifBatam)
    {
        $validator = Validator::make($request->all(), [
            'chasis_ayp' => 'nullable|numeric|min:0',
            '20ft_full' => 'nullable|numeric|min:0',
            '20ft_empty' => 'nullable|numeric|min:0',
            'antar_lokasi' => 'nullable|numeric|min:0',
            '40ft_full' => 'nullable|numeric|min:0',
            '40ft_empty' => 'nullable|numeric|min:0',
            '40ft_antar_lokasi' => 'nullable|numeric|min:0',
            'masa_berlaku' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif'
        ], [
            'masa_berlaku.required' => 'Masa berlaku wajib diisi',
            'masa_berlaku.date' => 'Format tanggal masa berlaku tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau nonaktif',
            '*.numeric' => 'Tarif harus berupa angka',
            '*.min' => 'Tarif tidak boleh negatif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $tarifBatam->update($request->all());

        return redirect()->route('tarif-batam.index')
                        ->with('success', 'Tarif Batam berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TarifBatam $tarifBatam)
    {
        $tarifBatam->delete();

        return redirect()->route('tarif-batam.index')
                        ->with('success', 'Tarif Batam berhasil dihapus!');
    }
}

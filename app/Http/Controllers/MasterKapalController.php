<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterKapal::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('kode_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $kapals = $query->paginate(10)->withQueryString();

        return view('master-kapal.index', compact('kapals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-kapal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_kapals,kode',
            'kode_kapal' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            MasterKapal::create($validated);

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data kapal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterKapal $masterKapal)
    {
        return view('master-kapal.show', compact('masterKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterKapal $masterKapal)
    {
        return view('master-kapal.edit', compact('masterKapal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterKapal $masterKapal)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_kapals,kode,' . $masterKapal->id,
            'kode_kapal' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $masterKapal->update($validated);

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data kapal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterKapal $masterKapal)
    {
        try {
            $masterKapal->delete();

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data kapal: ' . $e->getMessage());
        }
    }
}

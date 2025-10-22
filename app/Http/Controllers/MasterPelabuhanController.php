<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPelabuhan;
use Illuminate\Http\Request;

class MasterPelabuhanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-pelabuhan-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:master-pelabuhan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master-pelabuhan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master-pelabuhan-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPelabuhan::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $pelabuhans = $query->orderBy('nama_pelabuhan', 'asc')->paginate(15);

        // Statistics
        $stats = [
            'total' => MasterPelabuhan::count(),
            'aktif' => MasterPelabuhan::aktif()->count(),
            'nonaktif' => MasterPelabuhan::nonaktif()->count(),
        ];

        return view('master-pelabuhan.index', compact('pelabuhans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pelabuhan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelabuhan' => 'required|string|max:255|unique:master_pelabuhans,nama_pelabuhan',
            'kota' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_pelabuhan.required' => 'Nama pelabuhan wajib diisi.',
            'nama_pelabuhan.unique' => 'Nama pelabuhan sudah ada.',
            'kota.required' => 'Kota wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus aktif atau nonaktif.',
        ]);

        MasterPelabuhan::create($validated);

        return redirect()->route('master-pelabuhan.index')
                        ->with('success', 'Data pelabuhan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPelabuhan $masterPelabuhan)
    {
        return view('master-pelabuhan.show', compact('masterPelabuhan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPelabuhan $masterPelabuhan)
    {
        return view('master-pelabuhan.edit', compact('masterPelabuhan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPelabuhan $masterPelabuhan)
    {
        $validated = $request->validate([
            'nama_pelabuhan' => 'required|string|max:255|unique:master_pelabuhans,nama_pelabuhan,' . $masterPelabuhan->id,
            'kota' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_pelabuhan.required' => 'Nama pelabuhan wajib diisi.',
            'nama_pelabuhan.unique' => 'Nama pelabuhan sudah ada.',
            'kota.required' => 'Kota wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus aktif atau nonaktif.',
        ]);

        $masterPelabuhan->update($validated);

        return redirect()->route('master-pelabuhan.index')
                        ->with('success', 'Data pelabuhan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPelabuhan $masterPelabuhan)
    {
        try {
            $masterPelabuhan->delete();

            return redirect()->route('master-pelabuhan.index')
                            ->with('success', 'Data pelabuhan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('master-pelabuhan.index')
                            ->with('error', 'Gagal menghapus data pelabuhan. Data mungkin sedang digunakan.');
        }
    }
}

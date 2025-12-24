<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPelayananPelabuhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPelayananPelabuhanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat master pelayanan pelabuhan.');
        }

        $query = MasterPelayananPelabuhan::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pelayanan', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%')
                  ->orWhere('satuan', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 10);
        $pelayananPelabuhans = $query->orderBy('nama_pelayanan', 'asc')->paginate($perPage);

        return view('master-pelayanan-pelabuhan.index', compact('pelayananPelabuhans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat master pelayanan pelabuhan.');
        }

        return view('master-pelayanan-pelabuhan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat master pelayanan pelabuhan.');
        }

        $validated = $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'biaya' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        MasterPelayananPelabuhan::create($validated);

        return redirect()->route('master-pelayanan-pelabuhan.index')
            ->with('success', 'Data pelayanan pelabuhan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit master pelayanan pelabuhan.');
        }

        $pelayananPelabuhan = MasterPelayananPelabuhan::findOrFail($id);
        return view('master-pelayanan-pelabuhan.edit', compact('pelayananPelabuhan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit master pelayanan pelabuhan.');
        }

        $pelayananPelabuhan = MasterPelayananPelabuhan::findOrFail($id);

        $validated = $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'biaya' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $pelayananPelabuhan->update($validated);

        return redirect()->route('master-pelayanan-pelabuhan.index')
            ->with('success', 'Data pelayanan pelabuhan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pelayanan-pelabuhan-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus master pelayanan pelabuhan.');
        }

        $pelayananPelabuhan = MasterPelayananPelabuhan::findOrFail($id);
        $pelayananPelabuhan->delete();

        return redirect()->route('master-pelayanan-pelabuhan.index')
            ->with('success', 'Data pelayanan pelabuhan berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Divisi::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Handle status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $divisis = $query->orderBy('nama_divisi')->paginate(15);

        return view('master-divisi.index', compact('divisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi',
            'kode_divisi' => 'required|string|max:20|unique:divisis,kode_divisi',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        try {
            Divisi::create([
                'nama_divisi' => $request->nama_divisi,
                'kode_divisi' => strtoupper($request->kode_divisi),
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $divisi = Divisi::with('karyawans')->findOrFail($id);
        return view('master-divisi.show', compact('divisi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('master-divisi.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $divisi = Divisi::findOrFail($id);

        $request->validate([
            'nama_divisi' => ['required', 'string', 'max:100', Rule::unique('divisis')->ignore($divisi->id)],
            'kode_divisi' => ['required', 'string', 'max:20', Rule::unique('divisis')->ignore($divisi->id)],
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        try {
            $divisi->update([
                'nama_divisi' => $request->nama_divisi,
                'kode_divisi' => strtoupper($request->kode_divisi),
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $divisi = Divisi::findOrFail($id);

            // Check if divisi has related karyawans
            if ($divisi->karyawans()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus divisi yang masih memiliki karyawan terkait.');
            }

            $divisi->delete();

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(string $id)
    {
        try {
            $divisi = Divisi::findOrFail($id);
            $divisi->update(['is_active' => !$divisi->is_active]);

            $status = $divisi->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil ' . $status . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cabang::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_cabang', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        $cabangs = $query->orderBy('nama_cabang')->paginate(15);

        return view('master-cabang.index', compact('cabangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-cabang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:100|unique:cabangs,nama_cabang',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            Cabang::create([
                'nama_cabang' => $request->nama_cabang,
                'keterangan' => $request->keterangan
            ]);

            return redirect()->route('master.cabang.index')
                           ->with('success', 'Cabang berhasil ditambahkan!');
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
        $cabang = Cabang::findOrFail($id);
        return view('master-cabang.show', compact('cabang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cabang = Cabang::findOrFail($id);
        return view('master-cabang.edit', compact('cabang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cabang = Cabang::findOrFail($id);

        $request->validate([
            'nama_cabang' => ['required', 'string', 'max:100', Rule::unique('cabangs')->ignore($cabang->id)],
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            $cabang->update([
                'nama_cabang' => $request->nama_cabang,
                'keterangan' => $request->keterangan
            ]);

            return redirect()->route('master.cabang.index')
                           ->with('success', 'Cabang berhasil diperbarui!');
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
            $cabang = Cabang::findOrFail($id);
            $cabang->delete();

            return redirect()->route('master.cabang.index')
                           ->with('success', 'Cabang berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aktivitas::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_aktivitas', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $aktivitas = $query->orderBy('kode')->paginate($perPage);

        return view('master-aktivitas.index', compact('aktivitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-aktivitas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:aktivitas,kode',
            'nama_aktivitas' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            Aktivitas::create($request->all());

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Aktivitas $aktivitas)
    {
        return view('master-aktivitas.show', compact('aktivitas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aktivitas $aktivitas)
    {
        return view('master-aktivitas.edit', compact('aktivitas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aktivitas $aktivitas)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:aktivitas,kode,' . $aktivitas->id,
            'nama_aktivitas' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $aktivitas->update($request->all());

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aktivitas $aktivitas)
    {
        try {
            $aktivitas->delete();

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}

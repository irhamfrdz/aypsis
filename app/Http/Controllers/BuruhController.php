<?php

namespace App\Http\Controllers;

use App\Models\Buruh;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuruhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Buruh::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }
        
        $buruhs = $query->orderBy('nama', 'asc')->paginate(20);
        return view('buruh.index', compact('buruhs'));
    }

    public function create()
    {
        return view('buruh.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        Buruh::create($validated);
        return redirect()->route('buruh.index')->with('success', 'Buruh berhasil ditambahkan');
    }

    public function show(Buruh $buruh)
    {
        return view('buruh.show', compact('buruh'));
    }

    public function edit(Buruh $buruh)
    {
        return view('buruh.edit', compact('buruh'));
    }

    public function update(Request $request, Buruh $buruh)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $buruh->update($validated);
        return redirect()->route('buruh.index')->with('success', 'Buruh berhasil diperbarui');
    }

    public function destroy(Buruh $buruh)
    {
        $buruh->delete();
        return redirect()->route('buruh.index')->with('success', 'Buruh berhasil dihapus');
    }
}

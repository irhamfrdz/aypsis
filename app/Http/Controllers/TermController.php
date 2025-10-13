<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Term::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_status', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $terms = $query->paginate(15);

        return view('master-term.index', compact('terms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-term.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:terms,kode',
            'nama_status' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Term::create($request->all());

        return redirect()->route('term.index')->with('success', 'Term berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $term = Term::findOrFail($id);
        return view('master-term.show', compact('term'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $term = Term::findOrFail($id);
        return view('master-term.edit', compact('term'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $term = Term::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:terms,kode,' . $id,
            'nama_status' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $term->update($request->all());

        return redirect()->route('term.index')->with('success', 'Term berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $term = Term::findOrFail($id);
        $term->delete();

        return redirect()->route('term.index')->with('success', 'Term berhasil dihapus.');
    }
}

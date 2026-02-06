<?php

namespace App\Http\Controllers;

use App\Models\MasterGudangAmprahan;
use Illuminate\Http\Request;

class MasterGudangAmprahanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:master-gudang-amprahan-view')->only(['index', 'show']);
        $this->middleware('can:master-gudang-amprahan-create')->only(['create', 'store']);
        $this->middleware('can:master-gudang-amprahan-update')->only(['edit', 'update']);
        $this->middleware('can:master-gudang-amprahan-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterGudangAmprahan::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_gudang', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('lokasi', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $gudangAmprahans = $query->latest()->paginate(15);

        return view('master-gudang-amprahan.index', compact('gudangAmprahans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-gudang-amprahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_gudang' => 'required|string|max:255|unique:master_gudang_amprahans,nama_gudang',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        MasterGudangAmprahan::create($request->all());

        return redirect()->route('master.gudang-amprahan.index')
            ->with('success', 'Gudang Amprahan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $gudangAmprahan = MasterGudangAmprahan::findOrFail($id);
        return view('master-gudang-amprahan.show', compact('gudangAmprahan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gudangAmprahan = MasterGudangAmprahan::findOrFail($id);
        return view('master-gudang-amprahan.edit', compact('gudangAmprahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gudangAmprahan = MasterGudangAmprahan::findOrFail($id);

        $request->validate([
            'nama_gudang' => 'required|string|max:255|unique:master_gudang_amprahans,nama_gudang,' . $id,
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $gudangAmprahan->update($request->all());

        return redirect()->route('master.gudang-amprahan.index')
            ->with('success', 'Gudang Amprahan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gudangAmprahan = MasterGudangAmprahan::findOrFail($id);
        $gudangAmprahan->delete();

        return redirect()->route('master.gudang-amprahan.index')
            ->with('success', 'Gudang Amprahan berhasil dihapus.');
    }
}

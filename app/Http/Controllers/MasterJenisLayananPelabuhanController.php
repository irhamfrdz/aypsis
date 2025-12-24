<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterJenisLayananPelabuhan;
use Illuminate\Http\Request;

class MasterJenisLayananPelabuhanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-jenis-layanan-pelabuhan-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:master-jenis-layanan-pelabuhan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master-jenis-layanan-pelabuhan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master-jenis-layanan-pelabuhan-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = MasterJenisLayananPelabuhan::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('nama', 'asc')->paginate($request->get('per_page', 15));

        return view('master-jenis-layanan-pelabuhan.index', compact('items'));
    }

    public function create()
    {
        return view('master-jenis-layanan-pelabuhan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_layanan_pelabuhans,nama'
        ]);

        MasterJenisLayananPelabuhan::create($request->only('nama'));

        return redirect()->route('master.jenis-layanan-pelabuhan.index')->with('success', 'Jenis layanan pelabuhan berhasil ditambahkan.');
    }

    public function show(MasterJenisLayananPelabuhan $masterJenisLayananPelabuhan)
    {
        return view('master-jenis-layanan-pelabuhan.show', compact('masterJenisLayananPelabuhan'));
    }

    public function edit(MasterJenisLayananPelabuhan $masterJenisLayananPelabuhan)
    {
        return view('master-jenis-layanan-pelabuhan.edit', compact('masterJenisLayananPelabuhan'));
    }

    public function update(Request $request, MasterJenisLayananPelabuhan $masterJenisLayananPelabuhan)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_layanan_pelabuhans,nama,' . $masterJenisLayananPelabuhan->id
        ]);

        $masterJenisLayananPelabuhan->update($request->only('nama'));

        return redirect()->route('master.jenis-layanan-pelabuhan.index')->with('success', 'Jenis layanan pelabuhan berhasil diperbarui.');
    }

    public function destroy(MasterJenisLayananPelabuhan $masterJenisLayananPelabuhan)
    {
        try {
            $masterJenisLayananPelabuhan->delete();
            return redirect()->route('master.jenis-layanan-pelabuhan.index')->with('success', 'Jenis layanan pelabuhan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('master.jenis-layanan-pelabuhan.index')->with('error', 'Gagal menghapus data.');
        }
    }
}

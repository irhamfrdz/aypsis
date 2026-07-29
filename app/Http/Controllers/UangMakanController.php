<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UangMakanController extends Controller
{
    public function index()
    {
        $uangMakans = \App\Models\UangMakan::with('karyawan')->latest()->paginate(10);
        return view('uang-makan.index', compact('uangMakans'));
    }

    public function create()
    {
        $karyawans = \App\Models\Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get();
        return view('uang-makan.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        \App\Models\UangMakan::create($validated);
        return redirect()->route('uang-makan.index')->with('success', 'Data uang makan berhasil ditambahkan.');
    }

    public function edit(\App\Models\UangMakan $uangMakan)
    {
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        return view('uang-makan.edit', compact('uangMakan', 'karyawans'));
    }

    public function update(Request $request, \App\Models\UangMakan $uangMakan)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $uangMakan->update($validated);
        return redirect()->route('uang-makan.index')->with('success', 'Data uang makan berhasil diupdate.');
    }

    public function destroy(\App\Models\UangMakan $uangMakan)
    {
        $uangMakan->delete();
        return redirect()->route('uang-makan.index')->with('success', 'Data uang makan berhasil dihapus.');
    }
}

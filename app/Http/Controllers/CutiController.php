<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index()
    {
        $cutis = \App\Models\Cuti::with('karyawan')->latest()->paginate(10);
        return view('cuti.index', compact('cutis'));
    }

    public function create()
    {
        $karyawans = \App\Models\Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get();
        $penempatans = $karyawans->pluck('penempatan')->filter()->unique()->values();
        return view('cuti.create', compact('karyawans', 'penempatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|array|min:1',
            'karyawan_id.*' => 'exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        foreach ($validated['karyawan_id'] as $id) {
            \App\Models\Cuti::create([
                'karyawan_id' => $id,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'jenis_cuti' => $validated['jenis_cuti'],
                'keterangan' => $validated['keterangan'],
            ]);
        }
        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil ditambahkan.');
    }

    public function edit(\App\Models\Cuti $cuti)
    {
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        return view('cuti.edit', compact('cuti', 'karyawans'));
    }

    public function update(Request $request, \App\Models\Cuti $cuti)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        $cuti->update($validated);
        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil diupdate.');
    }

    public function destroy(\App\Models\Cuti $cuti)
    {
        $cuti->delete();
        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil dihapus.');
    }
}

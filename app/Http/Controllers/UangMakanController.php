<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UangMakanController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\UangMakan::with('karyawan')->latest();
        
        if ($request->filled('penempatan')) {
            $query->whereHas('karyawan', function($q) use ($request) {
                $q->where('penempatan', $request->penempatan);
            });
        }
        
        $uangMakans = $query->paginate(10)->withQueryString();
        $penempatans = \App\Models\Karyawan::whereNotNull('penempatan')->distinct()->pluck('penempatan');
        
        return view('uang-makan.index', compact('uangMakans', 'penempatans'));
    }

    public function create()
    {
        $karyawans = \App\Models\Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get();
        $penempatans = $karyawans->pluck('penempatan')->filter()->unique()->values();
        return view('uang-makan.create', compact('karyawans', 'penempatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|array|min:1',
            'karyawan_id.*' => 'exists:karyawans,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        foreach ($validated['karyawan_id'] as $id) {
            \App\Models\UangMakan::create([
                'karyawan_id' => $id,
                'tanggal' => $validated['tanggal'],
                'nominal' => $validated['nominal'],
                'keterangan' => $validated['keterangan'],
            ]);
        }
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

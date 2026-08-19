<?php

namespace App\Http\Controllers;

use App\Models\SaldoCuti;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class SaldoCutiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tahun' => 'required|integer',
            'total_cuti' => 'required|integer|min:0',
            'sisa_cuti' => 'required|integer',
            'cuti_terpakai' => 'required|integer|min:0',
            'keterangan' => 'nullable|string'
        ]);

        // Cek duplikasi
        $exists = SaldoCuti::where('karyawan_id', $request->karyawan_id)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Saldo cuti untuk tahun ' . $request->tahun . ' sudah ada.');
        }

        SaldoCuti::create($validated);

        return back()->with('success', 'Saldo cuti berhasil ditambahkan.');
    }

    public function update(Request $request, SaldoCuti $saldoCuti)
    {
        $validated = $request->validate([
            'total_cuti' => 'required|integer|min:0',
            'sisa_cuti' => 'required|integer',
            'cuti_terpakai' => 'required|integer|min:0',
            'keterangan' => 'nullable|string'
        ]);

        $saldoCuti->update($validated);

        return back()->with('success', 'Saldo cuti berhasil diupdate.');
    }

    public function destroy(SaldoCuti $saldoCuti)
    {
        $saldoCuti->delete();

        return back()->with('success', 'Saldo cuti berhasil dihapus.');
    }
}

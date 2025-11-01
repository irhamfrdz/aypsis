<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;

class MobilController extends Controller
{
    /**
     * Menampilkan daftar semua mobil.
     */
    public function index()
    {
        $mobils = Mobil::with('karyawan')->latest()->paginate(10);
        return view('master-mobil.index', compact('mobils'));
    }

    /**
     * Menampilkan form untuk membuat mobil baru.
     */
    public function create()
    {
        $karyawans = \App\Models\Karyawan::select('id', 'nama_lengkap', 'nik')
            ->orderBy('nama_lengkap')
            ->get();
            
        return view('master-mobil.create', compact('karyawans'));
    }

    /**
     * Menyimpan mobil baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_no' => 'required|string|max:50|unique:mobils,kode_no',
            'nomor_polisi' => 'required|string|max:20|unique:mobils,nomor_polisi',
            'lokasi' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:50',
            'jenis' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'bpkb' => 'nullable|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'nomor_rangka' => 'nullable|string|max:50',
            'pajak_stnk' => 'nullable|date',
            'pajak_plat' => 'nullable|date',
            'no_kir' => 'nullable|string|max:50',
            'pajak_kir' => 'nullable|date',
            'atas_nama' => 'nullable|string|max:100',
            'karyawan_id' => 'nullable|exists:karyawans,id',
        ]);

        $mobil = Mobil::create($validated);

        // Update nomor polisi pada karyawan jika ada
        if ($validated['karyawan_id']) {
            \App\Models\Karyawan::where('id', $validated['karyawan_id'])
                ->update(['plat' => $validated['nomor_polisi']]);
        }

        return redirect()->route('master-mobil.index')->with('success', 'Data mobil berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mobil.
     */
    public function show($id)
    {
        $mobil = Mobil::with('karyawan')->findOrFail($id);
        return view('master-mobil.show', compact('mobil'));
    }

    /**
     * Menampilkan form untuk mengedit mobil.
     */
    public function edit($id)
    {
        $mobil = Mobil::findOrFail($id);
        $karyawans = \App\Models\Karyawan::select('id', 'nama_lengkap', 'nik')
            ->orderBy('nama_lengkap')
            ->get();
            
        return view('master-mobil.edit', compact('mobil', 'karyawans'));
    }

    /**
     * Memperbarui data mobil di database.
     */
    public function update(Request $request, $id)
    {
        $mobil = Mobil::findOrFail($id);
        
        $validated = $request->validate([
            'kode_no' => 'required|string|max:50|unique:mobils,kode_no,' . $id,
            'nomor_polisi' => 'required|string|max:20|unique:mobils,nomor_polisi,' . $id,
            'lokasi' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:50',
            'jenis' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'bpkb' => 'nullable|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'nomor_rangka' => 'nullable|string|max:50',
            'pajak_stnk' => 'nullable|date',
            'pajak_plat' => 'nullable|date',
            'no_kir' => 'nullable|string|max:50',
            'pajak_kir' => 'nullable|date',
            'atas_nama' => 'nullable|string|max:100',
            'karyawan_id' => 'nullable|exists:karyawans,id',
        ]);

        // Jika karyawan lama ada, hapus nomor plat dari karyawan lama
        if ($mobil->karyawan_id && $mobil->karyawan_id != $validated['karyawan_id']) {
            \App\Models\Karyawan::where('id', $mobil->karyawan_id)
                ->update(['plat' => null]);
        }

        // Update data mobil
        $mobil->update($validated);

        // Update nomor polisi pada karyawan baru jika ada
        if ($validated['karyawan_id']) {
            \App\Models\Karyawan::where('id', $validated['karyawan_id'])
                ->update(['plat' => $validated['nomor_polisi']]);
        }

        return redirect()->route('master-mobil.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    /**
     * Menghapus mobil dari database.
     */
    public function destroy(Mobil $mobil)
    {
        // Hapus nomor plat dari karyawan jika ada
        if ($mobil->karyawan_id) {
            \App\Models\Karyawan::where('id', $mobil->karyawan_id)
                ->update(['plat' => null]);
        }

        $mobil->delete();

        return redirect()->route('master-mobil.index')
                         ->with('success', 'Mobil berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MasterTunjangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MasterTunjanganController extends Controller
{
    /**
     * Tampilkan daftar tunjangan.
     */
    public function index()
    {
        // Pengecekan permission opsional jika route middleware kurang cukup
        // if (!Gate::allows('master-tunjangan')) {
        //     abort(403);
        // }

        $tunjangans = MasterTunjangan::latest()->get();

        return view('master.tunjangan.index', compact('tunjangans'));
    }

    /**
     * Simpan data tunjangan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tunjangan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        MasterTunjangan::create($request->all());

        return redirect()->route('master.tunjangan.index')->with('success', 'Master Tunjangan berhasil ditambahkan.');
    }

    /**
     * Update data tunjangan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tunjangan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $tunjangan = MasterTunjangan::findOrFail($id);
        $tunjangan->update($request->all());

        return redirect()->route('master.tunjangan.index')->with('success', 'Master Tunjangan berhasil diperbarui.');
    }

    /**
     * Hapus data tunjangan.
     */
    public function destroy($id)
    {
        $tunjangan = MasterTunjangan::findOrFail($id);
        $tunjangan->delete();

        return redirect()->route('master.tunjangan.index')->with('success', 'Master Tunjangan berhasil dihapus.');
    }
}

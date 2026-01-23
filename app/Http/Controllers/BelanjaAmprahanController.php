<?php

namespace App\Http\Controllers;

use App\Models\BelanjaAmprahan;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class BelanjaAmprahanController extends Controller
{
    public function index()
    {
        $items = BelanjaAmprahan::latest()->paginate(20);
        return view('belanja-amprahan.index', compact('items'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_panggilan')->get();
        return view('belanja-amprahan.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'total' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'penerima_id' => 'nullable|exists:karyawans,id',
        ]);

        BelanjaAmprahan::create($data);

        return redirect()->route('belanja-amprahan.index')->with('success', 'Belanja amprahan berhasil ditambahkan');
    }

    public function show($id)
    {
        $item = BelanjaAmprahan::findOrFail($id);
        return view('belanja-amprahan.show', compact('item'));
    }

    public function edit($id)
    {
        $item = BelanjaAmprahan::findOrFail($id);
        $karyawans = Karyawan::orderBy('nama_panggilan')->get();
        return view('belanja-amprahan.edit', compact('item', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $item = BelanjaAmprahan::findOrFail($id);
        $data = $request->validate([
            'nomor' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'total' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'penerima_id' => 'nullable|exists:karyawans,id',
        ]);

        $item->update($data);

        return redirect()->route('belanja-amprahan.index')->with('success', 'Belanja amprahan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = BelanjaAmprahan::findOrFail($id);
        $item->delete();
        return redirect()->route('belanja-amprahan.index')->with('success', 'Data dihapus');
    }
}

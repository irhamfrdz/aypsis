<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterDokumenKapalAlexindoController extends Controller
{
    public function index()
    {
        $dokumens = \App\Models\MasterDokumenKapalAlexindo::with('kapal')->get();
        return view('master-dokumen-kapal-alexindo.index', compact('dokumens'));
    }

    public function create()
    {
        $kapals = \App\Models\MasterKapal::all();
        return view('master-dokumen-kapal-alexindo.create', compact('kapals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'nama_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:255',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string'
        ]);

        $data = $request->except('file_dokumen');

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_kapal', $filename);
            $data['file_dokumen'] = 'storage/dokumen_kapal/' . $filename;
        }

        \App\Models\MasterDokumenKapalAlexindo::create($data);

        return redirect()->route('master-dokumen-kapal-alexindo.index')
                         ->with('success', 'Dokumen Kapal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $dokumen = \App\Models\MasterDokumenKapalAlexindo::findOrFail($id);
        $kapals = \App\Models\MasterKapal::all();
        return view('master-dokumen-kapal-alexindo.edit', compact('dokumen', 'kapals'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'nama_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:255',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string'
        ]);

        $dokumen = \App\Models\MasterDokumenKapalAlexindo::findOrFail($id);
        $data = $request->except('file_dokumen');

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_kapal', $filename);
            $data['file_dokumen'] = 'storage/dokumen_kapal/' . $filename;

            if ($dokumen->file_dokumen && file_exists(public_path($dokumen->file_dokumen))) {
                unlink(public_path($dokumen->file_dokumen));
            }
        }

        $dokumen->update($data);

        return redirect()->route('master-dokumen-kapal-alexindo.index')
                         ->with('success', 'Dokumen Kapal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = \App\Models\MasterDokumenKapalAlexindo::findOrFail($id);
        
        if ($dokumen->file_dokumen && file_exists(public_path($dokumen->file_dokumen))) {
            unlink(public_path($dokumen->file_dokumen));
        }

        $dokumen->delete();

        return redirect()->route('master-dokumen-kapal-alexindo.index')
                         ->with('success', 'Dokumen Kapal berhasil dihapus.');
    }
}

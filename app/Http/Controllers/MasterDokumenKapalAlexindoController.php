<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterDokumenKapalAlexindoController extends Controller
{
    public function index()
    {
        $kapals = \App\Models\MasterKapal::withCount('dokumenKapalAlexindos')->get();
        return view('master-dokumen-kapal-alexindo.index', compact('kapals'));
    }

    public function show($id)
    {
        $kapal = \App\Models\MasterKapal::findOrFail($id);
        $dokumens = \App\Models\MasterDokumenKapalAlexindo::with('sertifikatKapal')->where('kapal_id', $id)->get();
        return view('master-dokumen-kapal-alexindo.show', compact('kapal', 'dokumens'));
    }

    public function create(Request $request)
    {
        $kapals = \App\Models\MasterKapal::all();
        $sertifikat_kapals = \App\Models\SertifikatKapal::aktif()->get();
        $selected_kapal_id = $request->query('kapal_id');
        return view('master-dokumen-kapal-alexindo.create', compact('kapals', 'sertifikat_kapals', 'selected_kapal_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'sertifikat_kapal_id' => 'required|exists:sertifikat_kapals,id',
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

        return redirect()->route('master-dokumen-kapal-alexindo.show', $data['kapal_id'])
                         ->with('success', 'Dokumen Kapal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $dokumen = \App\Models\MasterDokumenKapalAlexindo::findOrFail($id);
        $kapals = \App\Models\MasterKapal::all();
        $sertifikat_kapals = \App\Models\SertifikatKapal::aktif()->get();
        return view('master-dokumen-kapal-alexindo.edit', compact('dokumen', 'kapals', 'sertifikat_kapals'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'sertifikat_kapal_id' => 'required|exists:sertifikat_kapals,id',
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

        return redirect()->route('master-dokumen-kapal-alexindo.show', $dokumen->kapal_id)
                         ->with('success', 'Dokumen Kapal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = \App\Models\MasterDokumenKapalAlexindo::findOrFail($id);
        $kapal_id = $dokumen->kapal_id;
        
        if ($dokumen->file_dokumen && file_exists(public_path($dokumen->file_dokumen))) {
            unlink(public_path($dokumen->file_dokumen));
        }

        $dokumen->delete();

        return redirect()->route('master-dokumen-kapal-alexindo.show', $kapal_id)
                         ->with('success', 'Dokumen Kapal berhasil dihapus.');
    }
}

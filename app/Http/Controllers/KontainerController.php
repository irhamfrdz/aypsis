<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use Illuminate\Http\Request;

class KontainerController extends Controller
{
    /**
     * Menampilkan daftar semua kontainer.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Menggunakan paginasi untuk performa yang lebih baik
        $kontainers = Kontainer::latest()->paginate(15);
        return view('master-kontainer.index', compact('kontainers'));
    }

    /**
     * Menampilkan formulir untuk membuat kontainer baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master-kontainer.create');
    }

    /**
     * Menyimpan kontainer baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Gabungkan awalan, nomor seri, dan akhiran untuk membuat nomor seri gabungan
        $nomor_seri_gabungan = $request->input('awalan_kontainer') .
                               $request->input('nomor_seri_kontainer') .
                               $request->input('akhiran_kontainer');

        $request->merge(['nomor_seri_gabungan' => $nomor_seri_gabungan]);

        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'nomor_seri_gabungan' => 'required|string|size:11|unique:kontainers,nomor_seri_gabungan',
            'ukuran' => 'required|in:10,20,40',
            'tipe_kontainer' => 'required|string',
            'tanggal_beli' => 'nullable|date',
            'tanggal_jual' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'kondisi_kontainer' => 'nullable|string',
            'tanggal_masuk_sewa' => 'nullable|date',
            'tanggal_selesai_sewa' => 'nullable|date|after_or_equal:tanggal_masuk_sewa',
            'pemilik_kontainer' => 'nullable|string|max:255',
            'tahun_pembuatan' => 'nullable|string|size:4',
            'kontainer_asal' => 'nullable|string|max:255',
            'keterangan1' => 'nullable|string',
            'keterangan2' => 'nullable|string',
            'status' => 'nullable|string|in:Tersedia,Disewa',
        ]);

        // Tambahkan tanggal kondisi terakhir
        $data = $request->all();
        if ($request->filled('kondisi_kontainer')) {
            $data['tanggal_kondisi_terakhir'] = now();
        }

        Kontainer::create($data);

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit kontainer.
     *
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\View\View
     */
    public function edit(Kontainer $kontainer)
    {
        return view('master-kontainer.edit', compact('kontainer'));
    }

    /**
     * Memperbarui data kontainer di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Kontainer $kontainer)
    {
        // Gabungkan awalan, nomor seri, dan akhiran untuk membuat nomor seri gabungan
        $nomor_seri_gabungan = $request->input('awalan_kontainer') .
                               $request->input('nomor_seri_kontainer') .
                               $request->input('akhiran_kontainer');

        $request->merge(['nomor_seri_gabungan' => $nomor_seri_gabungan]);

        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'nomor_seri_gabungan' => 'required|string|size:11|unique:kontainers,nomor_seri_gabungan,' . $kontainer->id,
            'ukuran' => 'required|in:10,20,40',
            'tipe_kontainer' => 'required|string',
            'tanggal_beli' => 'nullable|date',
            'tanggal_jual' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'kondisi_kontainer' => 'nullable|string',
            'tanggal_masuk_sewa' => 'nullable|date',
            'tanggal_selesai_sewa' => 'nullable|date|after_or_equal:tanggal_masuk_sewa',
            'pemilik_kontainer' => 'nullable|string|max:255',
            'tahun_pembuatan' => 'nullable|string|size:4',
            'kontainer_asal' => 'nullable|string|max:255',
            'keterangan1' => 'nullable|string',
            'keterangan2' => 'nullable|string',
            'status' => 'nullable|string|in:Tersedia,Disewa',
        ]);

        $data = $request->all();

        // Perbarui tanggal kondisi terakhir jika kondisi diubah
        if ($kontainer->kondisi_kontainer !== $request->input('kondisi_kontainer')) {
            $data['tanggal_kondisi_terakhir'] = now();
        }

        $kontainer->update($data);

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil diperbarui!');
    }

    /**
     * Menghapus kontainer dari database.
     *
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Kontainer $kontainer)
    {
        $kontainer->delete();

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil dihapus!');
    }
}

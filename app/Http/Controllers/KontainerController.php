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
    public function index(Request $request)
    {
        $query = Kontainer::query();

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where('nomor_seri_gabungan', 'like', '%' . $search . '%')
                  ->orWhere('awalan_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('nomor_seri_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('akhiran_kontainer', 'like', '%' . $search . '%');
        }

        // Vendor filter
        if ($vendor = $request->get('vendor')) {
            $query->where('vendor', $vendor);
        }

        // Ukuran filter
        if ($ukuran = $request->get('ukuran')) {
            $query->where('ukuran', $ukuran);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Get distinct vendors for filter dropdown
        $vendors = Kontainer::distinct()
                           ->whereNotNull('vendor')
                           ->where('vendor', '!=', '')
                           ->orderBy('vendor')
                           ->pluck('vendor');

        // Menggunakan paginasi untuk performa yang lebih baik
        $perPage = $request->input('per_page', 15); // Default 15 jika tidak ada parameter
        $kontainers = $query->latest()->paginate($perPage);
        $kontainers->appends($request->query());
        
        return view('master-kontainer.index', compact('kontainers', 'vendors'));
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
            'nomor_seri_gabungan' => 'required|string|size:11',
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

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
        $existingWithSameSerialAndSuffix = Kontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);
            
            $warningMessage = "Kontainer dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Tambahkan tanggal kondisi terakhir
        $data = $request->all();
        if ($request->filled('kondisi_kontainer')) {
            $data['tanggal_kondisi_terakhir'] = now();
        }

        // Set status default jika tidak ada
        if (!$request->filled('status')) {
            $data['status'] = 'active';
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
            'nomor_seri_gabungan' => 'required|string|size:11',
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

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer (selain diri sendiri)
        $existingWithSameSerialAndSuffix = Kontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->where('id', '!=', $kontainer->id)
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);
            
            $warningMessage = "Kontainer lain dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

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

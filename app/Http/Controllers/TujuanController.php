<?php
namespace App\Http\Controllers;

use App\Models\Tujuan;
use Illuminate\Http\Request;

class TujuanController extends Controller
{
    // Catatan: Constructor yang sebelumnya menyebabkan error telah dihapus.
    // Middleware untuk proteksi rute sekarang ditangani di file routes/web.php.
    // Anda perlu mendefinisikan Gate 'master-tujuan' di AuthServiceProvider.

    /**
     * Tampilkan daftar tujuan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
    $tujuans = Tujuan::paginate(10);
    return view('master-tujuan.index', compact('tujuans'));
    }

    /**
     * Tampilkan form untuk membuat tujuan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
    return view('master-tujuan.create');
    }

    /**
     * Simpan tujuan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'deskripsi' => 'nullable|string',
            'uang_jalan' => 'nullable|numeric|min:0',
            'cabang' => 'nullable|string|max:255',
            'wilayah' => 'nullable|string|max:255',
            'rute' => 'nullable|string|max:255',
            'uang_jalan_20' => 'nullable|numeric|min:0',
            'ongkos_truk_20' => 'nullable|numeric|min:0',
            'uang_jalan_40' => 'nullable|numeric|min:0',
            'ongkos_truk_40' => 'nullable|numeric|min:0',
            'antar_20' => 'nullable|numeric|min:0',
            'antar_40' => 'nullable|numeric|min:0',
        ]);

        // ensure defaults for numeric fields
        $numericDefaults = [
            'uang_jalan_20','ongkos_truk_20','uang_jalan_40','ongkos_truk_40','antar_20','antar_40'
        ];
        foreach ($numericDefaults as $nf) {
            if (!isset($validated[$nf])) {
                $validated[$nf] = 0;
            }
        }

        Tujuan::create($validated);

        return redirect()->route('master.tujuan.index')->with('success', 'Tujuan berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit tujuan yang ada.
     *
     * @param  \App\Models\Tujuan  $tujuan
     * @return \Illuminate\View\View
     */
    public function edit(Tujuan $tujuan)
    {
    return view('master-tujuan.edit', compact('tujuan'));
    }

    /**
     * Perbarui tujuan yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tujuan  $tujuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tujuan $tujuan)
    {
        $validated = $request->validate([
            'deskripsi' => 'nullable|string',
            'uang_jalan' => 'nullable|numeric|min:0',
            'cabang' => 'nullable|string|max:255',
            'wilayah' => 'nullable|string|max:255',
            'rute' => 'nullable|string|max:255',
            'uang_jalan_20' => 'nullable|numeric|min:0',
            'ongkos_truk_20' => 'nullable|numeric|min:0',
            'uang_jalan_40' => 'nullable|numeric|min:0',
            'ongkos_truk_40' => 'nullable|numeric|min:0',
            'antar_20' => 'nullable|numeric|min:0',
            'antar_40' => 'nullable|numeric|min:0',
        ]);

        $numericDefaults = [
            'uang_jalan_20','ongkos_truk_20','uang_jalan_40','ongkos_truk_40','antar_20','antar_40'
        ];
        foreach ($numericDefaults as $nf) {
            if (!isset($validated[$nf])) {
                $validated[$nf] = 0;
            }
        }

        $tujuan->update($validated);

        return redirect()->route('master.tujuan.index')->with('success', 'Tujuan berhasil diperbarui!');
    }

    /**
     * Hapus tujuan dari database.
     *
     * @param  \App\Models\Tujuan  $tujuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tujuan $tujuan)
    {
        $tujuan->delete();

        return redirect()->route('master.tujuan.index')->with('success', 'Tujuan berhasil dihapus!');
    }
}

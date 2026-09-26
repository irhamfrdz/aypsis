<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('creator')->orderByDesc('pinned')->orderByDesc('created_at');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $beritas = $query->paginate(15)->withQueryString();
        return view('berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('berita.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'        => 'required|string|max:255',
            'konten'       => 'nullable|string',
            'tipe'         => 'required|in:berita,pamflet',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar') === false && $request->file('gambar')) {
            $err = $request->file('gambar')->getErrorMessage();
            return back()->withInput()->withErrors(['gambar' => "Gagal upload gambar: {$err}. Pastikan ukuran file tidak melebihi batas upload server."]);
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $this->storeGambar($request->file('gambar'), $request->tipe);
        }

        Berita::create([
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'tipe'         => $request->tipe,
            'gambar'       => $gambarPath,
            'is_active'    => $request->boolean('is_active', true),
            'pinned'       => $request->boolean('pinned', false),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : Carbon::now(),
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita/Pamflet berhasil ditambahkan.');
    }

    public function edit(Berita $berita)
    {
        return view('berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul'        => 'required|string|max:255',
            'konten'       => 'nullable|string',
            'tipe'         => 'required|in:berita,pamflet',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar') === false && $request->file('gambar')) {
            $err = $request->file('gambar')->getErrorMessage();
            return back()->withInput()->withErrors(['gambar' => "Gagal upload gambar: {$err}. Pastikan ukuran file tidak melebihi batas upload server."]);
        }

        $gambarPath = $berita->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            $this->deleteGambar($berita->gambar);
            $gambarPath = $this->storeGambar($request->file('gambar'), $request->tipe);
        } elseif ($berita->gambar && $berita->tipe !== $request->tipe) {
            // Tipe berubah tapi tidak ada gambar baru — pindahkan gambar ke folder tipe baru
            $gambarPath = $this->moveGambar($berita->gambar, $request->tipe);
        }

        $berita->update([
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'tipe'         => $request->tipe,
            'gambar'       => $gambarPath,
            'is_active'    => $request->boolean('is_active', true),
            'pinned'       => $request->boolean('pinned', false),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : $berita->published_at,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita/Pamflet berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        $this->deleteGambar($berita->gambar);
        $berita->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Berita/Pamflet berhasil dihapus.']);
        }
        return redirect()->route('berita.index')->with('success', 'Berita/Pamflet berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif (AJAX)
     */
    public function toggleActive(Berita $berita)
    {
        $berita->update(['is_active' => !$berita->is_active]);
        return response()->json([
            'success'   => true,
            'is_active' => $berita->is_active,
            'message'   => $berita->is_active ? 'Berita diaktifkan.' : 'Berita dinonaktifkan.',
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Helper: Manajemen File Gambar                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Simpan file gambar ke folder sesuai tipe (berita / pamflet).
     * Folder dibuat otomatis jika belum ada.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $tipe  'berita' | 'pamflet'
     * @return string  Path relatif dari public/ (misal: uploads/pamflet/xxx.jpg)
     */
    private function storeGambar($file, string $tipe): string
    {
        $folder  = 'uploads/' . $tipe;          // uploads/berita  atau  uploads/pamflet
        $destDir = public_path($folder);

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $fileName  = $tipe . '_' . time() . '_' . uniqid() . '.' . $extension;
        $file->move($destDir, $fileName);
        @chmod($destDir . DIRECTORY_SEPARATOR . $fileName, 0664);

        return $folder . '/' . $fileName;
    }

    /**
     * Hapus file gambar dari disk (public/).
     *
     * @param  string|null  $path  Path relatif dari public/
     */
    private function deleteGambar(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    /**
     * Pindahkan gambar ke folder tipe yang berbeda.
     * Dipanggil saat tipe konten diubah (misal dari berita ke pamflet)
     * tanpa mengganti file gambar.
     *
     * @param  string  $oldPath  Path relatif lama (misal: uploads/berita/xxx.jpg)
     * @param  string  $newTipe  Tipe baru ('berita' | 'pamflet')
     * @return string  Path relatif baru, atau path lama jika file tidak ditemukan
     */
    private function moveGambar(string $oldPath, string $newTipe): string
    {
        $oldAbsolute = public_path($oldPath);

        if (!file_exists($oldAbsolute)) {
            return $oldPath; // file tidak ada, kembalikan path lama
        }

        $newFolder  = 'uploads/' . $newTipe;
        $newDestDir = public_path($newFolder);

        if (!is_dir($newDestDir)) {
            mkdir($newDestDir, 0755, true);
        }

        $fileName    = basename($oldAbsolute);
        $newAbsolute = $newDestDir . DIRECTORY_SEPARATOR . $fileName;

        rename($oldAbsolute, $newAbsolute);

        return $newFolder . '/' . $fileName;
    }
}

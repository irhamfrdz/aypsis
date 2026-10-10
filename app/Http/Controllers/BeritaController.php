<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('creator')->orderByDesc('pinned')->orderByDesc('created_at');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('departemen')) {
            $deptFilter = $request->departemen;
            $query->where(function ($q) use ($deptFilter) {
                $q->whereNull('target_departemen')
                    ->orWhereJsonContains('target_departemen', $deptFilter);
            });
        }
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%'.$request->search.'%');
        }

        $beritas = $query->paginate(15)->withQueryString();
        $departemens = $this->getDepartemenList();

        return view('berita.index', compact('beritas', 'departemens'));
    }

    public function create()
    {
        return view('berita.create', [
            'tipeDefault' => request()->query('tipe') === 'pengumuman' ? 'pengumuman' : 'berita',
            'departemens' => $this->getDepartemenList(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string|required_if:tipe,pengumuman',
            'tipe' => 'required|in:berita,pamflet,pengumuman',
            'gambar' => 'nullable|prohibited_if:tipe,pengumuman|image|mimes:jpg,jpeg,png,webp|max:5120',
            'kecepatan_teks' => 'nullable|integer|min:1|max:10',
            'published_at' => 'nullable|date',
            'target_departemen_mode' => 'nullable|in:all,custom',
            'target_departemen' => 'nullable|array',
            'target_departemen.*' => 'string|max:100',
        ]);

        if ($request->hasFile('gambar') === false && $request->file('gambar')) {
            $err = $request->file('gambar')->getErrorMessage();

            return back()->withInput()->withErrors(['gambar' => "Gagal upload gambar: {$err}. Pastikan ukuran file tidak melebihi batas upload server."]);
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $this->storeGambar($request->file('gambar'), $request->tipe);
        }

        $targetDepartemen = null;
        if ($request->tipe === 'pengumuman' && $request->input('target_departemen_mode') === 'custom') {
            $rawDepts = (array) $request->input('target_departemen', []);
            $cleanDepts = array_values(array_unique(array_filter(array_map('trim', $rawDepts))));
            $targetDepartemen = count($cleanDepts) > 0 ? $cleanDepts : null;
        }

        Berita::create([
            'judul' => $request->judul,
            'konten' => $request->tipe === 'pengumuman' ? $this->sanitizeRichText($request->konten) : $request->konten,
            'target_departemen' => $targetDepartemen,
            'tipe' => $request->tipe,
            'gambar' => $gambarPath,
            'is_active' => $request->boolean('is_active', true),
            'pinned' => $request->boolean('pinned', false),
            'kecepatan_teks' => $request->input('kecepatan_teks', 5),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : Carbon::now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('berita.index')->with('success', 'Konten berhasil ditambahkan dan akan tampil di PWA sesuai status publish.');
    }

    public function edit(Berita $berita)
    {
        $kontenEditor = $this->sanitizeRichText($berita->konten ?? '');
        $departemens = $this->getDepartemenList();

        return view('berita.edit', compact('berita', 'kontenEditor', 'departemens'));
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string|required_if:tipe,pengumuman',
            'tipe' => 'required|in:berita,pamflet,pengumuman',
            'gambar' => 'nullable|prohibited_if:tipe,pengumuman|image|mimes:jpg,jpeg,png,webp|max:5120',
            'kecepatan_teks' => 'nullable|integer|min:1|max:10',
            'published_at' => 'nullable|date',
            'target_departemen_mode' => 'nullable|in:all,custom',
            'target_departemen' => 'nullable|array',
            'target_departemen.*' => 'string|max:100',
        ]);

        if ($request->hasFile('gambar') === false && $request->file('gambar')) {
            $err = $request->file('gambar')->getErrorMessage();

            return back()->withInput()->withErrors(['gambar' => "Gagal upload gambar: {$err}. Pastikan ukuran file tidak melebihi batas upload server."]);
        }

        $gambarPath = $berita->gambar;
        if ($request->tipe === 'pengumuman') {
            $this->deleteGambar($berita->gambar);
            $gambarPath = null;
        } elseif ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            $this->deleteGambar($berita->gambar);
            $gambarPath = $this->storeGambar($request->file('gambar'), $request->tipe);
        } elseif ($berita->gambar && $berita->tipe !== $request->tipe) {
            // Tipe berubah tapi tidak ada gambar baru — pindahkan gambar ke folder tipe baru
            $gambarPath = $this->moveGambar($berita->gambar, $request->tipe);
        }

        $targetDepartemen = null;
        if ($request->tipe === 'pengumuman' && $request->input('target_departemen_mode') === 'custom') {
            $rawDepts = (array) $request->input('target_departemen', []);
            $cleanDepts = array_values(array_unique(array_filter(array_map('trim', $rawDepts))));
            $targetDepartemen = count($cleanDepts) > 0 ? $cleanDepts : null;
        }

        $berita->update([
            'judul' => $request->judul,
            'konten' => $request->tipe === 'pengumuman' ? $this->sanitizeRichText($request->konten) : $request->konten,
            'target_departemen' => $targetDepartemen,
            'tipe' => $request->tipe,
            'gambar' => $gambarPath,
            'is_active' => $request->boolean('is_active', true),
            'pinned' => $request->boolean('pinned', false),
            'kecepatan_teks' => $request->input('kecepatan_teks', $berita->kecepatan_teks ?? 5),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : $berita->published_at,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita/Pamflet berhasil diperbarui.');
    }

    /**
     * Ambil daftar semua nama departemen unik dari data karyawan
     */
    private function getDepartemenList(): array
    {
        return Karyawan::whereNotNull('departemen')
            ->where('departemen', '!=', '')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen')
            ->values()
            ->all();
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
        $berita->update(['is_active' => ! $berita->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $berita->is_active,
            'message' => $berita->is_active ? 'Berita diaktifkan.' : 'Berita dinonaktifkan.',
        ]);
    }

    /** Bersihkan HTML editor pengumuman agar hanya format teks yang diizinkan tersimpan. */
    private function sanitizeRichText(?string $html): string
    {
        if (! $html) {
            return '';
        }

        $previousErrors = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML('<?xml encoding="UTF-8"><div id="rich-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        $root = (new \DOMXPath($document))->query('//*[@id="rich-root"]')->item(0);
        if (! $root) {
            return strip_tags($html);
        }

        $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'del', 'ul', 'ol', 'li', 'div', 'span', 'font'];
        $cleanNode = function (\DOMNode $node) use (&$cleanNode, $allowedTags): void {
            foreach (iterator_to_array($node->childNodes) as $child) {
                if ($child instanceof \DOMComment) {
                    $node->removeChild($child);

                    continue;
                }

                if (! $child instanceof \DOMElement) {
                    continue;
                }

                $cleanNode($child);
                if (! in_array(strtolower($child->tagName), $allowedTags, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);

                    continue;
                }

                $tag = strtolower($child->tagName);
                foreach (iterator_to_array($child->attributes) as $attribute) {
                    $name = strtolower($attribute->name);
                    $value = trim($attribute->value);
                    $allowed = ($tag === 'font' && $name === 'size' && preg_match('/^[1-7]$/', $value))
                        || ($tag === 'font' && $name === 'color' && preg_match('/^#[0-9a-f]{3,8}$/i', $value))
                        || ($name === 'style' && preg_match('/^(text-align:\s*(left|center|right|justify)|font-size:\s*(8|[9]|[1-6][0-9]|7[0-2])px);?$/i', $value));

                    if (! $allowed) {
                        $child->removeAttribute($attribute->name);
                    }
                }
            }
        };
        $cleanNode($root);

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return trim($result);
    }

    /* ------------------------------------------------------------------ */
    /*  Helper: Manajemen File Gambar */
    /* ------------------------------------------------------------------ */

    /**
     * Simpan file gambar ke folder sesuai tipe (berita / pamflet).
     * Folder dibuat otomatis jika belum ada.
     * Gambar juga di-copy ke folder PWA_UPLOAD_DIR (jika dikonfigurasi di .env).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $tipe  'berita' | 'pamflet'
     * @return string Path relatif dari public/ (misal: uploads/pamflet/xxx.jpg)
     */
    private function storeGambar($file, string $tipe): string
    {
        $folder = 'uploads/'.$tipe;          // uploads/berita  atau  uploads/pamflet
        $destDir = public_path($folder);

        if (! is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $fileName = $tipe.'_'.time().'_'.uniqid().'.'.$extension;
        $file->move($destDir, $fileName);
        @chmod($destDir.DIRECTORY_SEPARATOR.$fileName, 0664);

        // Sync: copy gambar ke folder PWA (jika PWA_UPLOAD_DIR dikonfigurasi)
        $this->syncToPwa($folder.'/'.$fileName);

        return $folder.'/'.$fileName;
    }

    /**
     * Sync / copy file gambar dari folder public AYPSIS ke folder PWA uploads.
     * Folder PWA dikonfigurasi via env PWA_UPLOAD_DIR.
     *
     * @param  string  $relativePath  Path relatif dari public/ (misal: uploads/pamflet/xxx.jpg)
     */
    private function syncToPwa(string $relativePath): void
    {
        $pwaUploadDir = env('PWA_UPLOAD_DIR');
        if (empty($pwaUploadDir)) {
            return; // Tidak dikonfigurasi, skip
        }

        $srcPath = public_path($relativePath);
        if (! file_exists($srcPath)) {
            return;
        }

        // Buat struktur folder di PWA jika belum ada
        // relativePath contoh: uploads/pamflet/pamflet_xxx.jpg
        // destPath: {PWA_UPLOAD_DIR}/pamflet/pamflet_xxx.jpg (tanpa prefix 'uploads/')
        $withoutUploadsPrefix = preg_replace('#^uploads/#', '', $relativePath);
        $destDir = rtrim($pwaUploadDir, '/\\').DIRECTORY_SEPARATOR.dirname($withoutUploadsPrefix);
        $destPath = rtrim($pwaUploadDir, '/\\').DIRECTORY_SEPARATOR.$withoutUploadsPrefix;

        if (! is_dir($destDir)) {
            @mkdir($destDir, 0775, true);
        }

        @copy($srcPath, $destPath);
        @chmod($destPath, 0664);
    }

    /**
     * Hapus file gambar dari disk (public/) dan dari folder PWA (jika ada).
     *
     * @param  string|null  $path  Path relatif dari public/
     */
    private function deleteGambar(?string $path): void
    {
        if (! $path) {
            return;
        }

        // Hapus dari AYPSIS public/
        if (file_exists(public_path($path))) {
            unlink(public_path($path));
        }

        // Hapus dari PWA uploads/
        $pwaUploadDir = env('PWA_UPLOAD_DIR');
        if (! empty($pwaUploadDir)) {
            $withoutUploadsPrefix = preg_replace('#^uploads/#', '', $path);
            $pwaPath = rtrim($pwaUploadDir, '/\\').DIRECTORY_SEPARATOR.$withoutUploadsPrefix;
            if (file_exists($pwaPath)) {
                @unlink($pwaPath);
            }
        }
    }

    /**
     * Pindahkan gambar ke folder tipe yang berbeda.
     * Dipanggil saat tipe konten diubah (misal dari berita ke pamflet)
     * tanpa mengganti file gambar.
     *
     * @param  string  $oldPath  Path relatif lama (misal: uploads/berita/xxx.jpg)
     * @param  string  $newTipe  Tipe baru ('berita' | 'pamflet')
     * @return string Path relatif baru, atau path lama jika file tidak ditemukan
     */
    private function moveGambar(string $oldPath, string $newTipe): string
    {
        $oldAbsolute = public_path($oldPath);

        if (! file_exists($oldAbsolute)) {
            return $oldPath; // file tidak ada, kembalikan path lama
        }

        $newFolder = 'uploads/'.$newTipe;
        $newDestDir = public_path($newFolder);

        if (! is_dir($newDestDir)) {
            mkdir($newDestDir, 0755, true);
        }

        $fileName = basename($oldAbsolute);
        $newAbsolute = $newDestDir.DIRECTORY_SEPARATOR.$fileName;

        rename($oldAbsolute, $newAbsolute);

        $newRelativePath = $newFolder.'/'.$fileName;

        // Sync: pindahkan juga di folder PWA
        $pwaUploadDir = env('PWA_UPLOAD_DIR');
        if (! empty($pwaUploadDir)) {
            // Hapus file lama di PWA
            $oldWithoutPrefix = preg_replace('#^uploads/#', '', $oldPath);
            $pwaOldPath = rtrim($pwaUploadDir, '/\\').DIRECTORY_SEPARATOR.$oldWithoutPrefix;
            if (file_exists($pwaOldPath)) {
                @unlink($pwaOldPath);
            }
            // Copy file baru ke lokasi baru di PWA
            $this->syncToPwa($newRelativePath);
        }

        return $newRelativePath;
    }
}

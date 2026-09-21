<?php

namespace App\Http\Controllers;

use App\Models\Berita;
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
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%'.$request->search.'%');
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
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string',
            'tipe' => 'required|in:berita,pamflet',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gambar_cropped' => 'nullable|string',
            'aspect_ratio' => 'nullable|string|max:20',
            'published_at' => 'nullable|date',
        ]);

        $gambarPath = $this->processImage($request);

        Berita::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'tipe' => $request->tipe,
            'gambar' => $gambarPath,
            'aspect_ratio' => $request->input('aspect_ratio', '2.2:1') ?: '2.2:1',
            'is_active' => $request->boolean('is_active', true),
            'pinned' => $request->boolean('pinned', false),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : Carbon::now(),
            'created_by' => Auth::id(),
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
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string',
            'tipe' => 'required|in:berita,pamflet',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gambar_cropped' => 'nullable|string',
            'aspect_ratio' => 'nullable|string|max:20',
            'published_at' => 'nullable|date',
        ]);

        $gambarPath = $this->processImage($request, $berita->gambar);

        $berita->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'tipe' => $request->tipe,
            'gambar' => $gambarPath,
            'aspect_ratio' => $request->input('aspect_ratio', $berita->aspect_ratio ?? '2.2:1') ?: '2.2:1',
            'is_active' => $request->boolean('is_active', true),
            'pinned' => $request->boolean('pinned', false),
            'published_at' => $request->published_at ? Carbon::parse($request->published_at) : $berita->published_at,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita/Pamflet berhasil diperbarui.');
    }

    /**
     * Proses penyimpanan gambar (baik dari file upload standar ataupun base64 hasil crop)
     */
    private function processImage(Request $request, ?string $oldImage = null): ?string
    {
        $destinationPath = public_path('uploads/berita');
        if (! file_exists($destinationPath)) {
            @mkdir($destinationPath, 0755, true);
        }

        // 1. Jika ada gambar hasil crop (base64)
        if ($request->filled('gambar_cropped') && str_starts_with($request->gambar_cropped, 'data:image')) {
            if ($oldImage && file_exists(public_path($oldImage))) {
                @unlink(public_path($oldImage));
            }

            $raw = $request->gambar_cropped;
            [$header, $data] = explode(';', $raw, 2);
            [, $data] = explode(',', $data, 2);
            $decodedData = base64_decode($data);

            $extension = 'jpg';
            if (str_contains($header, 'png')) {
                $extension = 'png';
            } elseif (str_contains($header, 'webp')) {
                $extension = 'webp';
            }

            $fileName = 'berita_'.time().'_'.uniqid().'.'.$extension;
            file_put_contents($destinationPath.'/'.$fileName, $decodedData);

            return 'uploads/berita/'.$fileName;
        }

        // 2. Jika ada upload file biasa (tanpa base64 crop)
        if ($request->hasFile('gambar')) {
            if ($oldImage && file_exists(public_path($oldImage))) {
                @unlink(public_path($oldImage));
            }

            $file = $request->file('gambar');
            $fileName = 'berita_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);

            return 'uploads/berita/'.$fileName;
        }

        return $oldImage;
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar && file_exists(public_path($berita->gambar))) {
            unlink(public_path($berita->gambar));
        }
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
}

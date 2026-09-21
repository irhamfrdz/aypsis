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

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file     = $request->file('gambar');
            $fileName = 'berita_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/berita'), $fileName);
            $gambarPath = 'uploads/berita/' . $fileName;
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

        $gambarPath = $berita->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($berita->gambar && file_exists(public_path($berita->gambar))) {
                unlink(public_path($berita->gambar));
            }
            $file     = $request->file('gambar');
            $fileName = 'berita_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/berita'), $fileName);
            $gambarPath = 'uploads/berita/' . $fileName;
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
        $berita->update(['is_active' => !$berita->is_active]);
        return response()->json([
            'success'   => true,
            'is_active' => $berita->is_active,
            'message'   => $berita->is_active ? 'Berita diaktifkan.' : 'Berita dinonaktifkan.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BiayaKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaKapal::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%");
            });
        }

        // Filter by jenis biaya
        if ($request->has('jenis_biaya') && $request->jenis_biaya != '') {
            $query->where('jenis_biaya', $request->jenis_biaya);
        }

        // Sort by tanggal descending by default
        $query->orderBy('tanggal', 'desc');

        $biayaKapals = $query->paginate(10)->withQueryString();

        return view('biaya-kapal.index', compact('biayaKapals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get list of ships for dropdown
        $kapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();

        // Get distinct no_voyage from naik_kapal and bls tables
        $voyagesFromNaikKapal = \DB::table('naik_kapal')
            ->select('no_voyage')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->pluck('no_voyage');

        $voyagesFromBls = \DB::table('bls')
            ->select('no_voyage')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->pluck('no_voyage');

        // Merge and get unique voyages
        $voyages = $voyagesFromNaikKapal->merge($voyagesFromBls)
            ->unique()
            ->sort()
            ->values();

        return view('biaya-kapal.create', compact('kapals', 'voyages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_kapal' => 'required|string|max:255',
            'jenis_biaya' => 'required|in:bahan_bakar,pelabuhan,perbaikan,awak_kapal,asuransi,lainnya',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ]);

        try {
            // Remove formatting from nominal (remove dots, convert comma to dot)
            $nominal = str_replace(['.', ','], ['', '.'], $validated['nominal']);
            $validated['nominal'] = $nominal;

            // Handle file upload
            if ($request->hasFile('bukti')) {
                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }

            BiayaKapal::create($validated);

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data biaya kapal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BiayaKapal $biayaKapal)
    {
        return view('biaya-kapal.show', compact('biayaKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BiayaKapal $biayaKapal)
    {
        // Get list of ships for dropdown (optional enhancement)
        $kapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();

        return view('biaya-kapal.edit', compact('biayaKapal', 'kapals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BiayaKapal $biayaKapal)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_kapal' => 'required|string|max:255',
            'jenis_biaya' => 'required|in:bahan_bakar,pelabuhan,perbaikan,awak_kapal,asuransi,lainnya',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ]);

        try {
            // Remove formatting from nominal (remove dots, convert comma to dot)
            $nominal = str_replace(['.', ','], ['', '.'], $validated['nominal']);
            $validated['nominal'] = $nominal;

            // Handle file upload
            if ($request->hasFile('bukti')) {
                // Delete old file if exists
                if ($biayaKapal->bukti) {
                    Storage::disk('public')->delete($biayaKapal->bukti);
                }

                $file = $request->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biaya-kapal', $fileName, 'public');
                $validated['bukti'] = $filePath;
            }

            $biayaKapal->update($validated);

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data biaya kapal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BiayaKapal $biayaKapal)
    {
        try {
            // Delete file if exists
            if ($biayaKapal->bukti) {
                Storage::disk('public')->delete($biayaKapal->bukti);
            }

            $biayaKapal->delete();

            return redirect()
                ->route('biaya-kapal.index')
                ->with('success', 'Data biaya kapal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data biaya kapal: ' . $e->getMessage());
        }
    }
}

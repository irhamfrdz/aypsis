<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use App\Exports\AssetTemplateExport;
use App\Imports\AssetImport;
use App\Models\Asset;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    /**
     * Display a listing of assets with search, filters, and statistics.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['karyawan', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_asset', 'like', "%{$search}%")
                    ->orWhere('nama_asset', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('tipe_model', 'like', "%{$search}%")
                    ->orWhere('nomor_seri', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Summary Statistics (calculated over full dataset)
        $stats = [
            'total' => Asset::count(),
            'total_nilai_perolehan' => Asset::sum('nilai_perolehan'),
            'total_nilai_buku' => Asset::sum('nilai_buku'),
            'total_tersedia' => Asset::where('status', 'Tersedia')->count(),
            'total_digunakan' => Asset::where('status', 'Digunakan')->count(),
            'total_maintenance' => Asset::where('status', 'Dalam Pemeliharaan')->count(),
            'total_rusak' => Asset::whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Afkir'])->count(),
        ];

        $assets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = Asset::KONDISI_OPTIONS;

        return view('master-asset.index', compact('assets', 'stats', 'kategoris', 'statuses', 'kondisis'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $nextKode = Asset::generateNextKode();
        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = Asset::KONDISI_OPTIONS;
        $karyawans = Karyawan::where('status', 'aktif')
            ->orWhereNull('status')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik', 'posisi']);

        return view('master-asset.create', compact('nextKode', 'kategoris', 'statuses', 'kondisis', 'karyawans'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_asset' => 'required|string|max:50|unique:assets,kode_asset',
            'nama_asset' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'merk' => 'nullable|string|max:100',
            'tipe_model' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:150',
            'tanggal_perolehan' => 'nullable|date',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'masa_manfaat_bulan' => 'nullable|integer|min:0',
            'nilai_residu' => 'nullable|numeric|min:0',
            'nilai_buku' => 'nullable|numeric|min:0',
            'kondisi' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'penanggung_jawab' => 'nullable|string|max:150',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'vendor' => 'nullable|string|max:150',
            'nomor_faktur' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        // Fallback nilai buku
        if (! isset($validated['nilai_buku']) || $validated['nilai_buku'] === null || $validated['nilai_buku'] == 0) {
            $validated['nilai_buku'] = $validated['nilai_perolehan'] ?? 0;
        }

        // PIC name fallback
        if (empty($validated['penanggung_jawab']) && ! empty($validated['karyawan_id'])) {
            $karyawan = Karyawan::find($validated['karyawan_id']);
            if ($karyawan) {
                $validated['penanggung_jawab'] = $karyawan->nama_lengkap ?? $karyawan->nama;
            }
        }

        // Handle Foto upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time().'_asset_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/assets/photos');
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
            $file->move($path, $filename);
            $validated['foto'] = 'uploads/assets/photos/'.$filename;
        }

        // Handle Lampiran upload
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time().'_doc_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/assets/documents');
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
            $file->move($path, $filename);
            $validated['lampiran'] = 'uploads/assets/documents/'.$filename;
        }

        $validated['created_by'] = Auth::id();

        Asset::create($validated);

        return redirect()->route('asset.index')->with('success', 'Data Asset berhasil ditambahkan');
    }

    /**
     * Display the specified asset details.
     */
    public function show(Asset $asset)
    {
        $asset->load(['karyawan', 'creator', 'updater']);

        return view('master-asset.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = Asset::KONDISI_OPTIONS;
        $karyawans = Karyawan::where('status', 'aktif')
            ->orWhereNull('status')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik', 'posisi']);

        return view('master-asset.edit', compact('asset', 'kategoris', 'statuses', 'kondisis', 'karyawans'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'kode_asset' => 'required|string|max:50|unique:assets,kode_asset,'.$asset->id,
            'nama_asset' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'merk' => 'nullable|string|max:100',
            'tipe_model' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:150',
            'tanggal_perolehan' => 'nullable|date',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'masa_manfaat_bulan' => 'nullable|integer|min:0',
            'nilai_residu' => 'nullable|numeric|min:0',
            'nilai_buku' => 'nullable|numeric|min:0',
            'kondisi' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'penanggung_jawab' => 'nullable|string|max:150',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'vendor' => 'nullable|string|max:150',
            'nomor_faktur' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        // PIC name fallback
        if (empty($validated['penanggung_jawab']) && ! empty($validated['karyawan_id'])) {
            $karyawan = Karyawan::find($validated['karyawan_id']);
            if ($karyawan) {
                $validated['penanggung_jawab'] = $karyawan->nama_lengkap ?? $karyawan->nama;
            }
        }

        // Handle Foto upload & replacement
        if ($request->hasFile('foto')) {
            if ($asset->foto && File::exists(public_path($asset->foto))) {
                File::delete(public_path($asset->foto));
            }
            $file = $request->file('foto');
            $filename = time().'_asset_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/assets/photos');
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
            $file->move($path, $filename);
            $validated['foto'] = 'uploads/assets/photos/'.$filename;
        }

        // Handle Lampiran upload & replacement
        if ($request->hasFile('lampiran')) {
            if ($asset->lampiran && File::exists(public_path($asset->lampiran))) {
                File::delete(public_path($asset->lampiran));
            }
            $file = $request->file('lampiran');
            $filename = time().'_doc_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/assets/documents');
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
            $file->move($path, $filename);
            $validated['lampiran'] = 'uploads/assets/documents/'.$filename;
        }

        $validated['updated_by'] = Auth::id();

        $asset->update($validated);

        return redirect()->route('asset.index')->with('success', 'Data Asset berhasil diperbarui');
    }

    /**
     * Remove the specified asset from storage (soft delete).
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('asset.index')->with('success', 'Data Asset berhasil dihapus');
    }

    /**
     * Export assets to Excel.
     */
    public function export(Request $request)
    {
        $filename = 'data_asset_'.date('Ymd_His').'.xlsx';

        return Excel::download(new AssetExport($request), $filename);
    }

    /**
     * Download Excel import template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new AssetTemplateExport, 'template_import_asset.xlsx');
    }

    /**
     * Import assets from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new AssetImport, $request->file('file'));

            return redirect()->route('asset.index')->with('success', 'Data Asset berhasil diimport');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return redirect()->route('asset.index')->withErrors($messages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: '.$e->getMessage());
        }
    }
}

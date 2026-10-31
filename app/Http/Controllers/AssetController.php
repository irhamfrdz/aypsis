<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use App\Exports\AssetKapalTemplateExport;
use App\Exports\AssetTemplateExport;
use App\Imports\AssetKapalImport;
use App\Imports\AssetImport;
use App\Models\Asset;
use App\Models\Karyawan;
use App\Models\MasterKapal;
use App\Models\TandaTerimaAsset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    /**
     * Display a listing of assets with search, filters, and statistics.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['creator', 'karyawan', 'kapal', 'tandaTerima']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_asset', 'like', "%{$search}%")
                    ->orWhere('nama_asset', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function ($kq) use ($search) {
                        $kq->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nama_panggilan', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    })
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('nama_kapal', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
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
            'total_tersedia' => Asset::where('status', 'Tersedia')->count(),
            'total_digunakan' => Asset::where('status', 'Digunakan')->count(),
            'total_maintenance' => Asset::where('status', 'Dalam Pemeliharaan')->count(),
            'total_rusak' => Asset::whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Afkir'])->count(),
        ];

        $assets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = array_merge(Asset::KONDISI_OPTIONS, Asset::KONDISI_KAPAL_OPTIONS);
        $karyawans = Karyawan::whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik']);

        return view('master-asset.index', compact('assets', 'stats', 'kategoris', 'statuses', 'kondisis', 'karyawans'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $nextKode = Asset::generateNextKode();
        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = array_merge(Asset::KONDISI_OPTIONS, Asset::KONDISI_KAPAL_OPTIONS);
        $lokasis = Asset::LOKASI_OPTIONS;
        $karyawans = Karyawan::whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik', 'divisi', 'posisi']);
        $kapals = MasterKapal::orderBy('nama_kapal')->get(['id', 'nama_kapal', 'status']);

        return view('master-asset.create', compact('nextKode', 'kategoris', 'statuses', 'kondisis', 'lokasis', 'karyawans', 'kapals'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_asset' => 'required|string|max:50|unique:assets,kode_asset',
            'nama_asset' => 'required|string|max:255',
            'nama_merk' => 'nullable|string|max:150',
            'spesifikasi' => 'nullable|string|max:2000',
            'kategori' => 'required|string|max:100',
            'tanggal_perolehan' => 'nullable|date',
            'kuantitas' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kondisi' => ['required', 'string', 'max:50', Rule::in($request->input('lokasi') === 'Di kapal' ? Asset::KONDISI_KAPAL_OPTIONS : Asset::KONDISI_OPTIONS)],
            'status' => 'required|string|max:50',
            'karyawan_id' => 'nullable|required_if:lokasi,Di bawa oleh karyawan|exists:karyawans,id',
            'tanggal_tanda_terima' => 'nullable|date',
            'nomor_seri' => 'nullable|string|max:100',
            'nomor_seri_items' => 'sometimes|array',
            'nomor_seri_items.*' => 'array:nomor,ukuran',
            'nomor_seri_items.*.nomor' => 'nullable|required_with:nomor_seri_items.*.ukuran|string|max:100',
            'nomor_seri_items.*.ukuran' => 'nullable|string|max:100',
            'lokasi' => 'nullable|in:'.implode(',', Asset::LOKASI_OPTIONS),
            'lokasi_asset' => 'nullable|in:'.implode(',', array_merge(Asset::LOKASI_ASSET_OPTIONS, Asset::LOKASI_ASSET_KAPAL_OPTIONS)),
            'kapal_id' => 'nullable|required_if:lokasi,Di kapal|exists:master_kapals,id',
            'vendor' => 'nullable|string|max:150',
            'nomor_faktur' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        $validated = $this->normalizeNomorSeri($validated);

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
        if (($validated['lokasi'] ?? null) !== 'Di kapal') {
            $validated['kapal_id'] = null;
        }

        DB::transaction(fn () => Asset::create($validated));

        return redirect()->route('asset.index')->with('success', 'Data Asset berhasil ditambahkan');
    }

    /**
     * Display the specified asset details.
     */
    public function show(Asset $asset)
    {
        $asset->load(['creator', 'updater', 'karyawan', 'kapal']);

        return view('master-asset.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $kategoris = Asset::KATEGORI_OPTIONS;
        $statuses = Asset::STATUS_OPTIONS;
        $kondisis = array_merge(Asset::KONDISI_OPTIONS, Asset::KONDISI_KAPAL_OPTIONS);
        $lokasis = Asset::LOKASI_OPTIONS;
        $karyawans = Karyawan::whereNull('tanggal_berhenti')
            ->orWhere('id', $asset->karyawan_id)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik', 'divisi', 'posisi']);
        $kapals = MasterKapal::orderBy('nama_kapal')->get(['id', 'nama_kapal', 'status']);

        return view('master-asset.edit', compact('asset', 'kategoris', 'statuses', 'kondisis', 'lokasis', 'karyawans', 'kapals'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'kode_asset' => 'required|string|max:50|unique:assets,kode_asset,'.$asset->id,
            'nama_asset' => 'required|string|max:255',
            'nama_merk' => 'nullable|string|max:150',
            'spesifikasi' => 'nullable|string|max:2000',
            'kategori' => 'required|string|max:100',
            'tanggal_perolehan' => 'nullable|date',
            'kuantitas' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kondisi' => ['required', 'string', 'max:50', Rule::in($request->input('lokasi') === 'Di kapal' ? Asset::KONDISI_KAPAL_OPTIONS : Asset::KONDISI_OPTIONS)],
            'status' => 'required|string|max:50',
            'karyawan_id' => 'nullable|required_if:lokasi,Di bawa oleh karyawan|exists:karyawans,id',
            'tanggal_tanda_terima' => 'nullable|date',
            'nomor_seri' => 'nullable|string|max:100',
            'nomor_seri_items' => 'sometimes|array',
            'nomor_seri_items.*' => 'array:nomor,ukuran',
            'nomor_seri_items.*.nomor' => 'nullable|required_with:nomor_seri_items.*.ukuran|string|max:100',
            'nomor_seri_items.*.ukuran' => 'nullable|string|max:100',
            'lokasi' => 'nullable|in:'.implode(',', Asset::LOKASI_OPTIONS),
            'lokasi_asset' => 'nullable|in:'.implode(',', array_merge(Asset::LOKASI_ASSET_OPTIONS, Asset::LOKASI_ASSET_KAPAL_OPTIONS)),
            'kapal_id' => 'nullable|required_if:lokasi,Di kapal|exists:master_kapals,id',
            'vendor' => 'nullable|string|max:150',
            'nomor_faktur' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        $validated = $this->normalizeNomorSeri($validated);

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
        if (($validated['lokasi'] ?? null) !== 'Di kapal') {
            $validated['kapal_id'] = null;
        }

        $asset->update($validated);

        return redirect()->route('asset.index')->with('success', 'Data Asset berhasil diperbarui');
    }

    private function normalizeNomorSeri(array $validated): array
    {
        if (array_key_exists('nomor_seri_items', $validated)) {
            $validated['nomor_seri_items'] = array_values(array_filter(array_map(fn ($item) => [
                'nomor' => trim($item['nomor'] ?? ''),
                'ukuran' => trim($item['ukuran'] ?? '') ?: null,
            ], $validated['nomor_seri_items']), fn ($item) => $item['nomor'] !== ''));
            // Keep the first number available to existing integrations.
            $validated['nomor_seri'] = $validated['nomor_seri_items'][0]['nomor'] ?? null;
        }

        return $validated;
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

    public function downloadKapalTemplate()
    {
        return Excel::download(new AssetKapalTemplateExport, 'template_import_asset_kapal.xlsx');
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
            $headings = (new HeadingRowImport)->toArray($request->file('file'))[0][0] ?? [];
            if (in_array('nama_kapal', $headings, true)) {
                return $this->importKapal($request);
            }

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

    public function importKapal(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            DB::transaction(fn () => Excel::import(new AssetKapalImport, $request->file('file')));

            return redirect()->route('asset.index')->with('success', 'Data Asset kapal berhasil diimport');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $messages = [];
            $columnLabels = [
                'kode_asset' => 'Kode aset',
                'nama_asset' => 'Nama aset',
                'nama_kapal' => 'Nama kapal',
                'nama_merk' => 'Nama merk',
                'spesifikasi' => 'Spesifikasi',
                'nomor_seri' => 'Nomor seri',
                'kategori' => 'Kategori',
                'lokasi_asset' => 'Lokasi asset',
                'tanggal_beli' => 'Tanggal beli',
                'tanggal_kadaluarsa' => 'Tanggal kadaluwarsa',
                'kuantitas' => 'Kuantitas',
                'satuan' => 'Satuan',
                'kondisi' => 'Kondisi',
                'status' => 'Status',
                'vendor' => 'Vendor',
                'nomor_faktur' => 'Nomor faktur',
                'keterangan' => 'Keterangan',
            ];
            foreach ($e->failures() as $failure) {
                $attribute = $failure->attribute();
                $column = $columnLabels[$attribute] ?? $attribute;
                $value = $failure->values()[$attribute] ?? null;
                $valueDescription = $value !== null && $value !== '' ? ' (isi: "'.mb_substr((string) $value, 0, 80).'")' : '';
                $messages[] = 'Baris '.$failure->row().', kolom '.$column.$valueDescription.': '.implode(', ', $failure->errors());
            }

            return redirect()->route('asset.index')->withErrors($messages);
        } catch (\Throwable $e) {
            return redirect()->route('asset.index')->with('error', 'Terjadi kesalahan saat import aset kapal: '.$e->getMessage());
        }
    }

    /**
     * Update asset tanda terima details.
     */
    public function updateTandaTerima(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_tanda_terima' => 'required|date',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'foto_tanda_terima' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'foto_tanda_terima.image' => 'Foto bukti serah terima harus berupa gambar.',
            'foto_tanda_terima.mimes' => 'Foto bukti serah terima harus berformat JPG, PNG, atau WEBP.',
            'foto_tanda_terima.max' => 'Foto bukti serah terima maksimal 5 MB.',
        ]);

        unset($validated['foto_tanda_terima']);
        $validated['status'] = ($validated['status'] ?? null) ?: 'Digunakan';
        $validated['updated_by'] = Auth::id();

        $disk = Storage::disk('tanda_terima_asset');
        $fotoBaru = null;
        $fotoLama = null;

        try {
            if ($request->hasFile('foto_tanda_terima')) {
                $fotoBaru = $request->file('foto_tanda_terima')->store('', 'tanda_terima_asset');
            }

            DB::transaction(function () use ($asset, $validated, $fotoBaru, &$fotoLama) {
                $asset->update($validated);
                $asset->unsetRelation('karyawan');
                $receipt = $asset->tandaTerima()->lockForUpdate()->first();
                $fotoLama = $receipt?->foto;
                $attributes = TandaTerimaAsset::attributesFromAsset($asset);

                if ($fotoBaru !== null) {
                    $attributes['foto'] = $fotoBaru;
                }

                $asset->tandaTerima()->updateOrCreate(['asset_id' => $asset->id], $attributes);
            });
        } catch (\Throwable $exception) {
            if ($fotoBaru !== null) {
                $disk->delete($fotoBaru);
            }

            throw $exception;
        }

        if ($fotoBaru !== null && $fotoLama) {
            $disk->delete($fotoLama);
        }

        return redirect()->route('asset.index')->with('success', 'Form tanda terima asset berhasil disimpan');
    }

    /**
     * Print form tanda terima / berita acara serah terima asset.
     */
    public function printTandaTerima(Asset $asset)
    {
        $asset->load(['karyawan', 'creator']);

        return view('master-asset.print-tanda-terima', compact('asset'));
    }

    public function fotoTandaTerima(Asset $asset)
    {
        $receipt = $asset->tandaTerima;
        $disk = Storage::disk('tanda_terima_asset');

        abort_unless($receipt?->foto && $disk->exists($receipt->foto), 404, 'Foto bukti serah terima tidak ditemukan. Silakan unggah kembali melalui Form Tanda Terima.');

        return $disk->response($receipt->foto, null, ['X-Content-Type-Options' => 'nosniff']);
    }

    public function downloadTandaTerima(Asset $asset)
    {
        $asset->load(['karyawan', 'creator']);

        $kodeAsset = preg_replace('/[^A-Za-z0-9_-]/', '_', $asset->kode_asset);

        return Pdf::loadView('master-asset.print-tanda-terima', [
            'asset' => $asset,
            'isPdf' => true,
        ])
            ->setPaper('letter', 'portrait')
            ->setOption('defaultMediaType', 'print')
            ->download('tanda_terima_asset_'.$kodeAsset.'.pdf');
    }
}

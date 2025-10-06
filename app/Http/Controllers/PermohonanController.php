<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\Kontainer;
use App\Models\Tujuan;
use App\Models\MasterKegiatan;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PermohonanController extends Controller
{
    /**
     * Parse angka dari format Indonesia (dengan titik sebagai pemisah ribuan) ke float.
     */
    private function parseIndonesianNumber($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        // Hapus titik (pemisah ribuan) dan ganti koma dengan titik jika ada
        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        return (float) $cleaned;
    }
    /**
     * Menampilkan daftar permohonan.
     */
    public function index(Request $request)
    {
        $queryBuilder = Permohonan::with(['supir', 'krani'])->latest();

        if ($request->filled('query')) {
            $query = $request->input('query');
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('nomor_memo', 'like', "%{$query}%")
                  ->orWhereHas('supir', function ($subq) use ($query) {
                      $subq->where('nama_panggilan', 'like', "%{$query}%");
                  });
            });
        }

    $permohonans = $queryBuilder->paginate(10)->appends($request->query());
    // Preload kegiatan map to show human-readable kegiatan name in the index view
    $kegiatanMap = MasterKegiatan::pluck('nama_kegiatan', 'kode_kegiatan')->toArray();
    return view('permohonan.index', compact('permohonans', 'kegiatanMap'));
    }

    /**
     * Menampilkan form untuk membuat permohonan baru.
     */
    public function create()
    {
    $supirs = Karyawan::whereIn('pekerjaan', ['Supir Truck', 'Supir Trailer'])->where('plat', '<>', '')->get();
        $kranis = Karyawan::where('pekerjaan', 'Krani')->get();
        // Ambil kontainer yang statusnya 'Tersedia' untuk dipilih
    $kontainers = Kontainer::where('status', 'Tersedia')->orderBy('nomor_seri_gabungan')->get();
    $kegiatans = MasterKegiatan::orderBy('kode_kegiatan')->get();
    $tujuans = Tujuan::orderBy('dari')->get();
    // Get next nomor memo for preview
    $nomorTerakhir = NomorTerakhir::where('modul', 'MSN')->first();
    $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;
    $now = now();
    $bulan = $now->format('m');
    $tahun = $now->format('y');
    $previewMemo = 'MSN' . '1' . $bulan . $tahun . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    return view('permohonan.create', compact('supirs', 'kranis', 'kontainers', 'kegiatans', 'tujuans', 'previewMemo'));
    }

    /**
     * Menyimpan permohonan baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi dasar untuk semua permohonan
        $validatedData = $request->validate([
            'nomor_memo' => 'nullable|string|max:255', // Changed to nullable since we'll generate it
            'kegiatan' => 'required|string|max:255',
            'vendor_perusahaan' => 'required|string|max:255',
            'tanggal_memo' => 'required|date',
            'supir_id' => 'required|exists:karyawans,id',
            'plat_nomor' => 'nullable|string|max:255',
            'krani_id' => 'nullable|exists:karyawans,id',
            'jumlah_kontainer' => 'required|integer|min:1',
            'ukuran' => 'required|string|max:255',
            'no_chasis' => 'nullable|string|max:255',
            'dari' => 'required|string|max:255',
            'ke' => 'required|string|max:255',
            'jumlah_uang_jalan' => 'required|numeric|min:0',
            'adjustment' => 'required|numeric',
            'alasan_adjustment' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Validasi kondisional berdasarkan kegiatan
        $kegiatanInput = $validatedData['kegiatan'];
        $mk = MasterKegiatan::where('kode_kegiatan', $kegiatanInput)
            ->orWhere('nama_kegiatan', $kegiatanInput)
            ->first();

        if (!$mk) {
            return back()->withInput()->with('error', "Kegiatan '{$kegiatanInput}' tidak ditemukan. Gunakan kode atau nama kegiatan yang valid.");
        }

        $isPerbaikanKontainer = strtolower($mk->nama_kegiatan) === 'perbaikan kontainer' ||
                               str_contains(strtolower($mk->nama_kegiatan), 'perbaikan');

        DB::beginTransaction();
        try {
            $supir = Karyawan::findOrFail($validatedData['supir_id']);
            $jumlahUangJalan = $this->parseIndonesianNumber($validatedData['jumlah_uang_jalan']);
            $adjustment = $this->parseIndonesianNumber($validatedData['adjustment']);
            $totalSetelahAdj = $jumlahUangJalan + $adjustment;
            $lampiranPath = $request->hasFile('lampiran') ? $request->file('lampiran')->store('public/permohonan_lampiran') : null;

            // Tentukan tujuan berdasarkan dari dan ke
            $tujuanString = $validatedData['dari'] . ' - ' . $validatedData['ke'];

            $kegiatanCode = $mk->kode_kegiatan;

            // Generate nomor memo from master nomor terakhir
            $nomorTerakhir = NomorTerakhir::where('modul', 'MSN')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                return back()->withInput()->with('error', 'Modul MSN tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $now = now();
            $bulan = $now->format('m');
            $tahun = $now->format('y');
            $nomorMemo = 'MSN' . '1' . $bulan . $tahun . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            $permohonan = Permohonan::create([
                'nomor_memo' => $nomorMemo,
                'status' => 'draft',
                'tanggal_memo' => $validatedData['tanggal_memo'],
                'kegiatan' => $kegiatanCode,
                'vendor_perusahaan' => $validatedData['vendor_perusahaan'],
                'supir_id' => $supir->id,
                'krani_id' => $validatedData['krani_id'],
                'plat_nomor' => $validatedData['plat_nomor'],
                'no_chasis' => $validatedData['no_chasis'],
                'ukuran' => $validatedData['ukuran'],
                'dari' => $validatedData['dari'],
                'ke' => $validatedData['ke'],
                'jumlah_kontainer' => $validatedData['jumlah_kontainer'],
                'jumlah_uang_jalan' => $jumlahUangJalan,
                'adjustment' => $adjustment,
                'alasan_adjustment' => $validatedData['alasan_adjustment'],
                'total_harga_setelah_adj' => $totalSetelahAdj,
                'catatan' => $validatedData['catatan'],
                'lampiran' => $lampiranPath,
            ]);

            DB::commit();

            // Cek apakah kegiatan memerlukan checkpoint otomatis
            $kegiatanLower = strtolower($mk->nama_kegiatan);
            $requiresCheckpoint = in_array($kegiatanLower, [
                'antar kontainer sewa',
                'perbaikan kontainer'
            ]);

            if ($requiresCheckpoint && Auth::check() && Auth::user()->karyawan?->id === $permohonan->supir_id) {
                // Redirect ke checkpoint hanya jika user yang login adalah supir yang ditugaskan
                return redirect()->route('supir.checkpoint.create', $permohonan)
                    ->with('success', 'Permohonan berhasil ditambahkan! Silakan lengkapi checkpoint.');
            } else {
                // Redirect normal ke index untuk kegiatan lainnya atau jika bukan supir yang ditugaskan
                return redirect()->route('permohonan.index')
                    ->with('success', 'Permohonan berhasil ditambahkan!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan permohonan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Permohonan $permohonan)
    {
        $permohonan->load('supir', 'krani', 'kontainers');
        return view('permohonan.show', compact('permohonan'));
    }

    /**
     * Menampilkan form untuk mengedit permohonan.
     */
    public function edit(Permohonan $permohonan)
    {
    $supirs = Karyawan::whereIn('pekerjaan', ['Supir Truck', 'Supir Trailer'])->whereNotNull('plat')->where('plat', '!=', '')->get();
        $kranis = Karyawan::where('pekerjaan', 'Krani')->get();
        $kontainers = Kontainer::where('kondisi_kontainer', 'Baik')
            ->orWhereIn('id', $permohonan->kontainers->pluck('id'))
            ->get();

    $kegiatans = MasterKegiatan::orderBy('kode_kegiatan')->get();
    $tujuans = Tujuan::orderBy('dari')->get();

    return view('permohonan.edit', compact('permohonan', 'supirs', 'kranis', 'kontainers', 'kegiatans', 'tujuans'));
    }

    /**
     * Memperbarui permohonan di database.
     */
    public function update(Request $request, Permohonan $permohonan)
    {
        // Validasi dasar untuk semua permohonan
        $validatedData = $request->validate([
            'kegiatan' => 'required|string',
            'vendor_perusahaan' => 'required|in:AYP,ZONA,SOC,DPE',
            'supir_id' => 'required|exists:karyawans,id',
            'krani_id' => 'nullable|exists:karyawans,id',
            'plat_nomor' => 'required|string|max:255',
            'no_chasis' => 'nullable|string|max:255',
            'ukuran' => 'required|in:10,20,40',
            'tanggal_memo' => 'required|date',
            'jumlah_kontainer' => 'required|integer|min:1',
            'nomor_kontainer' => 'nullable|array',
            'nomor_kontainer.*' => 'nullable|string|exists:kontainers,nomor_seri_gabungan',
            'dari' => 'required|string|max:255',
            'ke' => 'required|string|max:255',
            'jumlah_uang_jalan' => 'required|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'alasan_adjustment' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Validasi kondisional berdasarkan kegiatan
        $kegiatanInput = $validatedData['kegiatan'];
        $mk = MasterKegiatan::where('kode_kegiatan', $kegiatanInput)
            ->orWhere('nama_kegiatan', $kegiatanInput)
            ->first();

        if (!$mk) {
            return back()->withInput()->with('error', "Kegiatan '{$kegiatanInput}' tidak ditemukan. Gunakan kode atau nama kegiatan yang valid.");
        }

        $isPerbaikanKontainer = strtolower($mk->nama_kegiatan) === 'perbaikan kontainer' ||
                               str_contains(strtolower($mk->nama_kegiatan), 'perbaikan');

        // Validasi untuk dari dan ke fields
        $request->validate([
            'dari' => 'required|string|max:255',
            'ke' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $supir = Karyawan::findOrFail($validatedData['supir_id']);

            // Gabungkan dari dan ke untuk tujuan string
            $tujuanString = $request->input('dari') . ' - ' . $request->input('ke');
            $jumlahUangJalan = $this->parseIndonesianNumber($validatedData['jumlah_uang_jalan']);

            $adjustment = isset($validatedData['adjustment']) ? $this->parseIndonesianNumber($validatedData['adjustment']) : 0;
            $totalSetelahAdj = $jumlahUangJalan + $adjustment;

            $kegiatanCode = $mk->kode_kegiatan;

            $permohonan->update([
                'tanggal_memo' => $validatedData['tanggal_memo'],
                'kegiatan' => $kegiatanCode,
                'vendor_perusahaan' => $validatedData['vendor_perusahaan'],
                'supir_id' => $supir->id,
                'krani_id' => $validatedData['krani_id'],
                'plat_nomor' => $validatedData['plat_nomor'],
                'no_chasis' => $validatedData['no_chasis'],
                'ukuran' => $validatedData['ukuran'],
                'tujuan' => $tujuanString,
                'jumlah_kontainer' => $validatedData['jumlah_kontainer'],
                'jumlah_uang_jalan' => $jumlahUangJalan,
                'adjustment' => $adjustment,
                'alasan_adjustment' => $validatedData['alasan_adjustment'],
                'total_harga_setelah_adj' => $totalSetelahAdj,
                'catatan' => $validatedData['catatan'],
            ]);

            // Perbarui kontainer yang terhubung
            if ($request->has('nomor_kontainer')) {
                $nomorKontainers = array_filter($validatedData['nomor_kontainer']);
                if (!empty($nomorKontainers)) {
                    $kontainerIds = Kontainer::whereIn('nomor_seri_gabungan', $nomorKontainers)->pluck('id');
                    $permohonan->kontainers()->sync($kontainerIds);
                } else {
                    // Jika array kosong setelah filter, hapus semua relasi
                    $permohonan->kontainers()->detach();
                }
            }

            DB::commit();
            return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui permohonan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus permohonan dari database.
     */
    public function destroy(Permohonan $permohonan)
    {
        try {
            $permohonan->kontainers()->detach();
            $permohonan->delete();
            return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus permohonan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus beberapa permohonan sekaligus (bulk delete).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:permohonans,id'
        ]);

        try {
            $selectedIds = $request->input('selected_ids');
            $deletedCount = 0;
            $failedMemos = [];

            DB::transaction(function () use ($selectedIds, &$deletedCount, &$failedMemos) {
                foreach ($selectedIds as $id) {
                    try {
                        $permohonan = Permohonan::findOrFail($id);

                        // Detach kontainers relationship
                        $permohonan->kontainers()->detach();

                        // Store memo number for success message
                        $memoNumber = $permohonan->nomor_memo;

                        // Delete the permohonan
                        $permohonan->delete();
                        $deletedCount++;

                    } catch (\Exception $e) {
                        $failedMemos[] = $permohonan->nomor_memo ?? "ID: {$id}";
                    }
                }
            });

            // Prepare success/error messages
            $messages = [];
            if ($deletedCount > 0) {
                $messages[] = "Berhasil menghapus {$deletedCount} memo permohonan.";
            }

            if (!empty($failedMemos)) {
                $messages[] = "Gagal menghapus memo: " . implode(', ', $failedMemos);
            }

            $messageType = empty($failedMemos) ? 'success' : ($deletedCount > 0 ? 'warning' : 'error');

            return redirect()->route('permohonan.index')->with($messageType, implode(' ', $messages));

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus permohonan: ' . $e->getMessage());
        }
    }

    /**
     * Print memo surat jalan untuk permohonan tertentu.
     */
    public function print(Permohonan $permohonan)
    {
        // Load relationships yang diperlukan untuk print
        $permohonan->load(['supir', 'krani', 'kontainers']);

        // Ambil data kegiatan untuk display
        $kegiatan = MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)->first();

        return view('permohonan.print', compact('permohonan', 'kegiatan'));
    }

    /**
     * Print multiple permohonan memos berdasarkan rentang tanggal.
     */
    public function printByDate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Ambil semua permohonan dalam rentang tanggal
        $permohonans = Permohonan::with(['supir', 'krani', 'kontainers'])
            ->whereBetween('tanggal_memo', [$startDate, $endDate])
            ->orderBy('tanggal_memo', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($permohonans->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada permohonan ditemukan dalam rentang tanggal tersebut.');
        }

        // Ambil data kegiatan untuk semua permohonan
        $kegiatanMap = MasterKegiatan::pluck('nama_kegiatan', 'kode_kegiatan')->toArray();

        return view('permohonan.print-by-date', compact('permohonans', 'kegiatanMap', 'startDate', 'endDate'));
    }

    /**
     * Export permohonan list to CSV.
     */
    public function export(Request $request)
    {
        $filename = 'permohonans_' . date('Ymd_His') . '.csv';
        $delimiter = ';';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($delimiter) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Expanded header with most fields stored on permohonan plus kontainer list
            fputcsv($handle, [
                'id','nomor_memo','kegiatan','kegiatan_nama','supir_id','supir_nama','krani_id','krani_nama',
                'vendor_perusahaan','plat_nomor','no_chasis','ukuran','tujuan','jumlah_kontainer','jumlah_uang_jalan',
                'adjustment','alasan_adjustment','total_harga_setelah_adj','catatan','lampiran','status','tanggal_memo',
                'created_at','updated_at','kontainer_nomor_list'
            ], $delimiter);

            $rows = Permohonan::with(['supir','krani','kontainers'])->orderBy('created_at', 'desc')->get();
            foreach ($rows as $r) {
                $kegiatanNama = null;
                try {
                    if (!empty($r->kegiatan)) {
                        $mk = \App\Models\MasterKegiatan::where('kode_kegiatan', $r->kegiatan)->first();
                        $kegiatanNama = $mk ? $mk->nama_kegiatan : null;
                    }
                } catch (\Exception $_) { $kegiatanNama = null; }

                $kontainerList = '';
                try {
                    $kontainerList = $r->kontainers->pluck('nomor_seri_gabungan')->filter()->unique()->values()->all();
                    $kontainerList = implode('|', $kontainerList);
                } catch (\Exception $_) { $kontainerList = ''; }

                fputcsv($handle, [
                    $r->id,
                    $r->nomor_memo,
                    $r->kegiatan,
                    $kegiatanNama,
                    $r->supir_id,
                    optional($r->supir)->nama_panggilan,
                    $r->krani_id,
                    optional($r->krani)->nama_panggilan,
                    $r->vendor_perusahaan,
                    $r->plat_nomor,
                    $r->no_chasis,
                    $r->ukuran,
                    $r->tujuan,
                    $r->jumlah_kontainer,
                    $r->jumlah_uang_jalan,
                    $r->adjustment,
                    $r->alasan_adjustment,
                    $r->total_harga_setelah_adj,
                    $r->catatan,
                    $r->lampiran,
                    $r->status,
                    $r->tanggal_memo,
                    $r->created_at,
                    $r->updated_at,
                    $kontainerList,
                ], $delimiter);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import permohonan from CSV. Expected columns: nomor_memo,kegiatan,supir,tujuan,jumlah_kontainer,total_harga_setelah_adj
     * This import will create permohonan rows with minimal required fields. It will skip rows with validation errors and report them.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel',
        ]);

        $file = $request->file('csv_file');
        $delimiter = ';';
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) return redirect()->back()->with('error', 'Tidak dapat membuka file CSV');

        $first = fgets($handle);
        if ($first === false) { fclose($handle); return redirect()->back()->with('error', 'File kosong'); }
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);
        $expected = ['nomor_memo','kegiatan','supir','tujuan','jumlah_kontainer','total_harga_setelah_adj'];
        $hasHeader = $header === $expected;
        if (!$hasHeader) rewind($handle);

        $row = 0; $created = 0; $errors = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row++;
            $cols = array_pad($data, count($expected), null);
            $nomor_memo = trim($cols[0]);
            $kegiatan = trim($cols[1]);
            $supirName = trim($cols[2]);
            $tujuan = trim($cols[3]);
            $jumlah_kontainer = intval($cols[4] ?: 0);
            $total = floatval($cols[5] ?: 0);

            if (empty($nomor_memo) || empty($kegiatan)) {
                $errors[] = "Baris $row: nomor_memo atau kegiatan kosong";
                continue;
            }

            // find supir by name (simple match on nama_panggilan)
            $supir = null;
            if (!empty($supirName)) {
                $supir = Karyawan::where('nama_panggilan', 'like', $supirName)->first();
            }

            // resolve kegiatan (allow kode or nama)
            $mk = MasterKegiatan::where('kode_kegiatan', $kegiatan)
                ->orWhere('nama_kegiatan', $kegiatan)
                ->first();
            if (!$mk) {
                $errors[] = "Baris $row: kegiatan '$kegiatan' tidak ditemukan (kode atau nama tidak cocok)";
                continue;
            }
            $kegiatanCode = $mk->kode_kegiatan;

            try {
                Permohonan::create([
                    'nomor_memo' => $nomor_memo,
                    'kegiatan' => $kegiatanCode,
                    'vendor_perusahaan' => 'AYP',
                    'supir_id' => $supir->id ?? null,
                    'plat_nomor' => '',
                    'ukuran' => '20',
                    'tujuan' => $tujuan,
                    'jumlah_kontainer' => max(1, $jumlah_kontainer),
                    'jumlah_uang_jalan' => $total,
                    'total_harga_setelah_adj' => $total,
                    'tanggal_memo' => now()->toDateString(),
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Baris $row: gagal menyimpan - " . $e->getMessage();
            }
        }

        fclose($handle);

        $msg = "Import selesai. Baris: $row, berhasil: $created.";
        if (!empty($errors)) return redirect()->back()->with('warning', $msg)->with('import_errors', $errors);
        return redirect()->back()->with('success', $msg);
    }
}

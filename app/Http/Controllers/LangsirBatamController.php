<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\LangsirBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LangsirBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:langsir-batam-view')->only(['index', 'show']);
        $this->middleware('permission:langsir-batam-create')->only(['create', 'store', 'storeBulk']);
        $this->middleware('permission:langsir-batam-update')->only(['edit', 'update']);
        $this->middleware('permission:langsir-batam-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');

        $query = LangsirBatam::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        if ($tanggal_dari) {
            $query->whereDate('tanggal', '>=', $tanggal_dari);
        }
        if ($tanggal_sampai) {
            $query->whereDate('tanggal', '<=', $tanggal_sampai);
        }

        $langsirs = $query->orderBy('tanggal', 'desc')->paginate(15);

        // Fetch container sizes for auto-fill in bulk insert
        $stockContainers = \App\Models\StockKontainer::whereNotNull('nomor_seri_gabungan')->pluck('ukuran', 'nomor_seri_gabungan')->toArray();
        $containers = \App\Models\Kontainer::whereNotNull('nomor_seri_gabungan')->pluck('ukuran', 'nomor_seri_gabungan')->toArray();
        $containerSizesRaw = array_merge($containers, $stockContainers);
        
        $containerSizes = [];
        foreach ($containerSizesRaw as $no => $size) {
            // Normalize size format, e.g. "20" to "20FT"
            $normalizedSize = trim($size);
            if (is_numeric($normalizedSize)) {
                $normalizedSize .= 'FT';
            }
            $containerSizes[strtoupper(trim($no))] = strtoupper($normalizedSize);
        }

        return view('langsir-batam.index', compact('langsirs', 'search', 'tanggal_dari', 'tanggal_sampai', 'containerSizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_transaksi = LangsirBatam::generateNoTransaksi();
        $supirs = Karyawan::where('status', 'active')
            ->whereIn('cabang', ['BTM', 'BATAM'])
            ->where(function ($q) {
                $q->where('pekerjaan', 'LIKE', '%supir%')
                    ->orWhere('divisi', 'LIKE', '%supir%')
                    ->orWhere('divisi', 'SUPIR');
            })
            ->orderBy('nama_panggilan', 'asc')
            ->get();

        $kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $stock_kontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $all_kontainers = $kontainers->concat($stock_kontainers)->unique('no_kontainer')->sortBy('no_kontainer');

        $locations = ['SRIMAS', 'PELABUHAN', 'TPK/RTG'];
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        return view('langsir-batam.create', compact('no_transaksi', 'supirs', 'all_kontainers', 'locations', 'gudangs'));
    }

    public function getContainerManifestHistory(Request $request)
    {
        $no_kontainer = $request->input('no_kontainer');
        if (!$no_kontainer) {
            return response()->json(['success' => false, 'message' => 'No kontainer provided']);
        }

        $manifests = \App\Models\Manifest::where('nomor_kontainer', $no_kontainer)
            ->orderBy('tanggal_berangkat', 'asc')
            ->get();

        if ($manifests->isNotEmpty()) {
            $data = $manifests->map(function ($manifest) {
                return [
                    'nama_kapal' => $manifest->nama_kapal,
                    'no_voyage' => $manifest->no_voyage,
                    'tanggal_berangkat' => $manifest->tanggal_berangkat ? $manifest->tanggal_berangkat->format('d-m-Y') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Manifest not found']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_transaksi' => 'required|unique:langsir_batams,no_transaksi',
            'tanggal' => 'required|date',
            'no_kontainer' => 'required|string',
            'size' => 'required|string',
            'no_seal' => 'nullable|string',
            'dari' => 'nullable|string',
            'ke' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'status' => 'required|string',
            'ob_dalam_pelabuhan' => 'nullable|boolean',
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
        ]);

        $validated['input_by'] = Auth::id();
        $validated['ob_dalam_pelabuhan'] = $request->has('ob_dalam_pelabuhan');

        if ($validated['ob_dalam_pelabuhan']) {
            $validated['dari'] = 'PELABUHAN';
            $validated['ke'] = 'PELABUHAN';
        } else {
            if (empty($validated['dari']) || empty($validated['ke'])) {
                return redirect()->back()->withErrors(['dari' => 'Lokasi asal dan tujuan wajib diisi jika bukan OB Dalam Pelabuhan.'])->withInput();
            }
        }

        $langsir = LangsirBatam::create($validated);

        if ($request->filled('no_kontainer') && $request->filled('gudang_tujuan_id')) {
            $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $request->no_kontainer)
                ->where('status', '!=', 'inactive')
                ->first();
            
            if ($stockKontainer) {
                $stockKontainer->update(['gudangs_id' => $request->gudang_tujuan_id]);
            }
        }

        // Log to HistoryKontainer
        $tipeKontainer = 'kontainer';
        if (\App\Models\StockKontainer::where('nomor_seri_gabungan', $validated['no_kontainer'])->exists()) {
            $tipeKontainer = 'stock';
        }

        $asalGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($validated['dari']))->first();
        $tujuanGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($validated['ke']))->first();

        $obSuffix = $validated['ob_dalam_pelabuhan'] ? " [OB Dalam Pelabuhan]" : "";

        \App\Models\HistoryKontainer::create([
            'nomor_kontainer' => $validated['no_kontainer'],
            'tipe_kontainer' => $tipeKontainer,
            'jenis_kegiatan' => 'Langsir',
            'tanggal_kegiatan' => $validated['tanggal'],
            'asal_gudang_id' => $asalGudang?->id,
            'gudang_id' => $tujuanGudang?->id,
            'keterangan' => "Langsir ({$validated['status']}) dari {$validated['dari']} ke {$validated['ke']}{$obSuffix} [No Transaksi: {$validated['no_transaksi']}]." . ($validated['keterangan'] ? " Ket: {$validated['keterangan']}" : ""),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil disimpan.');
    }

    public function storeBulk(Request $request)
    {
        $rows = $request->input('rows', []);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim.',
            ], 422);
        }

        $successCount = 0;
        $errors = [];

        // Map valid karyawans (separated by division) and kendaraans to validate and auto-correct bulk data
        $supirMap = [];
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')->get(['id', 'nama_panggilan', 'nama_lengkap', 'plat']);
        foreach ($karyawanSupirs as $k) {
            if ($k->nama_panggilan) $supirMap[strtolower(trim($k->nama_panggilan))] = $k;
            if ($k->nama_lengkap)   $supirMap[strtolower(trim($k->nama_lengkap))] = $k;
        }

        $allKendaraansMap = [];
        foreach (\App\Models\Mobil::all(['nomor_polisi']) as $m) {
            if ($m->nomor_polisi) {
                $allKendaraansMap[strtolower(trim(str_replace(' ', '', $m->nomor_polisi)))] = $m->nomor_polisi;
            }
        }

        $gudangMap = [];
        foreach (\App\Models\Gudang::where('status', 'aktif')->get() as $g) {
            $gudangMap[strtolower(trim($g->nama_gudang))] = $g->id;
        }

        try {
            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 1;

                if (empty($row['tanggal']) || empty($row['no_kontainer']) || empty($row['size'])) {
                    $errors[] = "Baris {$rowNumber}: Tanggal, No Kontainer, dan Size wajib diisi.";
                    continue;
                }

                $obDalamPelabuhan = (strtolower(trim($row['ob_dalam_pelabuhan'] ?? '')) === 'ya');

                if (!$obDalamPelabuhan && (empty($row['dari']) || empty($row['ke']))) {
                    $errors[] = "Baris {$rowNumber}: Dari dan Ke wajib diisi jika bukan OB Dalam Pelabuhan.";
                    continue;
                }

                // Cek Gudang Tujuan
                if (empty($row['gudang_tujuan'])) {
                    $errors[] = "Baris {$rowNumber}: Gudang Tujuan wajib diisi.";
                    continue;
                }
                
                $gudangKey = strtolower(trim($row['gudang_tujuan']));
                if (isset($gudangMap[$gudangKey])) {
                    $row['gudang_tujuan_id'] = $gudangMap[$gudangKey];
                } else {
                    $errors[] = "Baris {$rowNumber}: Gudang '{$row['gudang_tujuan']}' tidak ditemukan atau tidak aktif.";
                    continue;
                }

                // Check Supir in Master Karyawan (divisi supir)
                if (!empty($row['supir'])) {
                    $supirKey = strtolower(trim($row['supir']));
                    if (isset($supirMap[$supirKey])) {
                        $row['supir'] = $supirMap[$supirKey]->nama_panggilan ?: $supirMap[$supirKey]->nama_lengkap;
                        // Auto fill plat if not provided
                        if (empty($row['no_plat']) && $supirMap[$supirKey]->plat) {
                            $row['no_plat'] = $supirMap[$supirKey]->plat;
                        }
                    } else {
                        $errors[] = "Baris {$rowNumber}: Supir '{$row['supir']}' tidak terdaftar di Master Karyawan dengan divisi Supir.";
                        continue;
                    }
                }

                // Check No Plat in Master Kendaraan and auto-correct formatting
                if (!empty($row['no_plat'])) {
                    $platClean = strtolower(trim(str_replace(' ', '', $row['no_plat'])));
                    if (isset($allKendaraansMap[$platClean])) {
                        $row['no_plat'] = $allKendaraansMap[$platClean];
                    } else {
                        $errors[] = "Baris {$rowNumber}: No Plat '{$row['no_plat']}' tidak terdaftar di Master Mobil.";
                        continue;
                    }
                }

                $noTransaksi = LangsirBatam::generateNoTransaksi();
                
                $dataInsert = [
                    'no_transaksi' => $noTransaksi,
                    'tanggal' => $row['tanggal'],
                    'no_kontainer' => $row['no_kontainer'],
                    'size' => $row['size'],
                    'no_seal' => $row['no_seal'] ?? null,
                    'dari' => $obDalamPelabuhan ? 'PELABUHAN' : $row['dari'],
                    'ke' => $obDalamPelabuhan ? 'PELABUHAN' : $row['ke'],
                    'gudang_tujuan_id' => $row['gudang_tujuan_id'],
                    'supir' => $row['supir'] ?? null,
                    'no_plat' => $row['no_plat'] ?? null,
                    'biaya' => floatval(str_replace(['Rp', '.', ',', ' '], '', $row['biaya'] ?? 0)),
                    'keterangan' => $row['keterangan'] ?? null,
                    'status' => strtoupper(trim($row['status'] ?? 'FULL')),
                    'ob_dalam_pelabuhan' => $obDalamPelabuhan,
                    'input_by' => Auth::id(),
                ];

                LangsirBatam::create($dataInsert);

                // Update Stock Kontainer if exists
                $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $dataInsert['no_kontainer'])
                    ->where('status', '!=', 'inactive')
                    ->first();
                
                if ($stockKontainer) {
                    $stockKontainer->update(['gudangs_id' => $dataInsert['gudang_tujuan_id']]);
                }

                // Log to HistoryKontainer
                $tipeKontainer = $stockKontainer ? 'stock' : 'kontainer';
                $asalGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($dataInsert['dari']))->first();
                $tujuanGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($dataInsert['ke']))->first();
                $obSuffix = $dataInsert['ob_dalam_pelabuhan'] ? " [OB Dalam Pelabuhan]" : "";

                \App\Models\HistoryKontainer::create([
                    'nomor_kontainer' => $dataInsert['no_kontainer'],
                    'tipe_kontainer' => $tipeKontainer,
                    'jenis_kegiatan' => 'Langsir',
                    'tanggal_kegiatan' => $dataInsert['tanggal'],
                    'asal_gudang_id' => $asalGudang?->id,
                    'gudang_id' => $tujuanGudang?->id,
                    'keterangan' => "Langsir ({$dataInsert['status']}) dari {$dataInsert['dari']} ke {$dataInsert['ke']}{$obSuffix} [No Transaksi: {$dataInsert['no_transaksi']}]." . ($dataInsert['keterangan'] ? " Ket: {$dataInsert['keterangan']}" : ""),
                    'created_by' => Auth::id(),
                ]);

                $successCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan pada sebagian data. Seluruh proses dibatalkan.',
                    'errors' => $errors
                ], 422);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil menyimpan {$successCount} data Langsir Batam massal.",
                'redirect' => route('langsir-batam.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $langsir = LangsirBatam::with('user')->findOrFail($id);

        return view('langsir-batam.show', compact('langsir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $langsir = LangsirBatam::findOrFail($id);
        $supirs = Karyawan::where('status', 'active')
            ->whereIn('cabang', ['BTM', 'BATAM'])
            ->where(function ($q) {
                $q->where('pekerjaan', 'LIKE', '%supir%')
                    ->orWhere('divisi', 'LIKE', '%supir%')
                    ->orWhere('divisi', 'SUPIR');
            })
            ->orderBy('nama_panggilan', 'asc')
            ->get();

        $kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $stock_kontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $all_kontainers = $kontainers->concat($stock_kontainers)->unique('no_kontainer')->sortBy('no_kontainer');

        $locations = ['SRIMAS', 'PELABUHAN', 'TPK/RTG'];

        return view('langsir-batam.edit', compact('langsir', 'supirs', 'all_kontainers', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $langsir = LangsirBatam::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_kontainer' => 'required|string',
            'size' => 'required|string',
            'no_seal' => 'nullable|string',
            'dari' => 'nullable|string',
            'ke' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'status' => 'required|string',
            'ob_dalam_pelabuhan' => 'nullable|boolean',
        ]);

        $validated['ob_dalam_pelabuhan'] = $request->has('ob_dalam_pelabuhan');

        if ($validated['ob_dalam_pelabuhan']) {
            $validated['dari'] = 'PELABUHAN';
            $validated['ke'] = 'PELABUHAN';
        } else {
            if (empty($validated['dari']) || empty($validated['ke'])) {
                return redirect()->back()->withErrors(['dari' => 'Lokasi asal dan tujuan wajib diisi jika bukan OB Dalam Pelabuhan.'])->withInput();
            }
        }

        $langsir->update($validated);

        // Update HistoryKontainer
        $tipeKontainer = 'kontainer';
        if (\App\Models\StockKontainer::where('nomor_seri_gabungan', $validated['no_kontainer'])->exists()) {
            $tipeKontainer = 'stock';
        }

        $asalGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($validated['dari']))->first();
        $tujuanGudang = \App\Models\Gudang::where('nama_gudang', 'like', trim($validated['ke']))->first();

        $obSuffix = $validated['ob_dalam_pelabuhan'] ? " [OB Dalam Pelabuhan]" : "";

        $history = \App\Models\HistoryKontainer::where('keterangan', 'like', "%[No Transaksi: {$langsir->no_transaksi}]%")->first();
        if ($history) {
            $history->update([
                'nomor_kontainer' => $validated['no_kontainer'],
                'tipe_kontainer' => $tipeKontainer,
                'tanggal_kegiatan' => $validated['tanggal'],
                'asal_gudang_id' => $asalGudang?->id,
                'gudang_id' => $tujuanGudang?->id,
                'keterangan' => "Langsir ({$validated['status']}) dari {$validated['dari']} ke {$validated['ke']}{$obSuffix} [No Transaksi: {$langsir->no_transaksi}]." . ($validated['keterangan'] ? " Ket: {$validated['keterangan']}" : ""),
            ]);
        } else {
            \App\Models\HistoryKontainer::create([
                'nomor_kontainer' => $validated['no_kontainer'],
                'tipe_kontainer' => $tipeKontainer,
                'jenis_kegiatan' => 'Langsir',
                'tanggal_kegiatan' => $validated['tanggal'],
                'asal_gudang_id' => $asalGudang?->id,
                'gudang_id' => $tujuanGudang?->id,
                'keterangan' => "Langsir ({$validated['status']}) dari {$validated['dari']} ke {$validated['ke']}{$obSuffix} [No Transaksi: {$langsir->no_transaksi}]." . ($validated['keterangan'] ? " Ket: {$validated['keterangan']}" : ""),
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $langsir = LangsirBatam::findOrFail($id);
        
        // Delete HistoryKontainer
        \App\Models\HistoryKontainer::where('keterangan', 'like', "%[No Transaksi: {$langsir->no_transaksi}]%")->delete();

        $langsir->delete();

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil dihapus.');
    }
}

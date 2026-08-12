<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Mobil;
use App\Models\SuratJalanTarikKosongBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratJalanTarikKosongBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratJalanTarikKosongBatam::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengambilan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $items = $query->orderBy('tanggal_surat_jalan', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(10);

        $pricelistRings = \App\Models\PricelistUangJalanBatam::activeBbm()->orderBy('ring')
            ->get(['ring', 'expedisi', 'wilayah', 'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty'])
            ->flatMap(function ($item) {
                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $name = trim($sw);
                        if ($name) {
                            $mapped[] = [
                                'name' => $name,
                                'ring' => $item->ring,
                                'rates' => [
                                    '20_F' => $item->tarif_20ft_full,
                                    '20_Empty' => $item->tarif_20ft_empty,
                                    '40_F' => $item->tarif_40ft_full,
                                    '40_Empty' => $item->tarif_40ft_empty,
                                    '45_F' => $item->tarif_40ft_full, // 45ft usually uses 40ft rate or specific
                                    '45_Empty' => $item->tarif_40ft_empty,
                                ]
                            ];
                        }
                    }
                }
                return $mapped;
            })
            ->values();

        return view('surat-jalan-tarik-kosong-batam.index', compact('items', 'pricelistRings'));
    }

    public function create()
    {
        $supirs = Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->whereIn('cabang', ['BTM', 'BATAM'])->orderBy('nama_lengkap')->get();
        $keneks = Karyawan::where('status', 'active')->where('divisi', 'KENEK')->orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        // Get kontainer data dari 2 table: stock_kontainers dan kontainers
        $stockKontainersRaw = \App\Models\StockKontainer::orderBy('nomor_seri_gabungan')
            ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);

        $kontainersRaw = \App\Models\Kontainer::orderBy('nomor_seri_gabungan')
            ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);

        $allKontainers = collect();
        foreach ($stockKontainersRaw as $stock) {
            $allKontainers->push((object) [
                'id' => $stock->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers',
            ]);
        }
        foreach ($kontainersRaw as $kontainer) {
            $allKontainers->push((object) [
                'id' => $kontainer->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers',
            ]);
        }
        $kontainers = $allKontainers->sortBy('nomor_seri_gabungan');

        $pricelistRings = \App\Models\PricelistUangJalanBatam::activeBbm()->orderBy('ring')
            ->get(['ring', 'expedisi', 'wilayah', 'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty'])
            ->flatMap(function ($item) {
                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $trimmed = trim($sw);
                        if ($trimmed !== '') {
                            $mapped[] = [
                                'name' => $trimmed,
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'rates' => [
                                    '20_F' => $item->tarif_20ft_full,
                                    '20_E' => $item->tarif_20ft_empty,
                                    '40_F' => $item->tarif_40ft_full,
                                    '40_E' => $item->tarif_40ft_empty,
                                    '45_F' => $item->tarif_40ft_full,
                                    '45_E' => $item->tarif_40ft_empty,
                                ],
                            ];
                            $mapped[] = [
                                'name' => $trimmed . ' (' . $item->expedisi . ')',
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'rates' => [
                                    '20_F' => $item->tarif_20ft_full,
                                    '20_E' => $item->tarif_20ft_empty,
                                    '40_F' => $item->tarif_40ft_full,
                                    '40_E' => $item->tarif_40ft_empty,
                                    '45_F' => $item->tarif_40ft_full,
                                    '45_E' => $item->tarif_40ft_empty,
                                ],
                            ];
                        }
                    }
                }
                return $mapped;
            })
            ->unique('name')
            ->values();

        $locations = $pricelistRings->pluck('name');

        $warehouses = \App\Models\Gudang::orderBy('nama_gudang')->pluck('nama_gudang');
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        return view('surat-jalan-tarik-kosong-batam.create', compact('supirs', 'keneks', 'mobils', 'kontainers', 'locations', 'warehouses', 'pricelistRings', 'gudangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_tarik_kosong_batams,no_surat_jalan',
            'tanggal_surat_jalan' => 'required|date',

            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'kenek' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'size' => 'nullable|string',
            'f_e' => 'nullable|string',
            'status' => 'nullable|in:draft,active,completed,cancelled',
            'catatan' => 'nullable|string',
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['input_by'] = Auth::id();
        $validated['input_date'] = now();
        $validated['lokasi'] = 'batam';

        SuratJalanTarikKosongBatam::create($validated);

        if ($request->filled('no_kontainer') && $request->filled('gudang_tujuan_id')) {
            $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $request->no_kontainer)
                ->where('status', '!=', 'inactive')
                ->first();
            
            if ($stockKontainer) {
                $asalGudangId = $stockKontainer->gudangs_id;
                $stockKontainer->update(['gudangs_id' => $request->gudang_tujuan_id]);

                \App\Models\HistoryKontainer::create([
                    'nomor_kontainer' => $request->no_kontainer,
                    'tipe_kontainer' => 'stock',
                    'jenis_kegiatan' => 'Tarik Kosong Batam',
                    'tanggal_kegiatan' => $request->tanggal_surat_jalan,
                    'asal_gudang_id' => $asalGudangId,
                    'gudang_id' => $request->gudang_tujuan_id,
                    'keterangan' => 'SJ Tarik Kosong Batam: ' . $request->no_surat_jalan,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil disimpan');
    }

    public function show($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);

        return view('surat-jalan-tarik-kosong-batam.show', compact('item'));
    }

    public function edit($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        $supirs = Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->whereIn('cabang', ['BTM', 'BATAM'])->orderBy('nama_lengkap')->get();
        $keneks = Karyawan::where('status', 'active')->where('divisi', 'KENEK')->orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        // Get kontainer data
        $stockKontainersRaw = \App\Models\StockKontainer::orderBy('nomor_seri_gabungan')
            ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);

        $kontainersRaw = \App\Models\Kontainer::orderBy('nomor_seri_gabungan')
            ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);

        $allKontainers = collect();
        foreach ($stockKontainersRaw as $stock) {
            $allKontainers->push((object) [
                'id' => $stock->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers',
            ]);
        }
        foreach ($kontainersRaw as $kontainer) {
            $allKontainers->push((object) [
                'id' => $kontainer->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers',
            ]);
        }
        $kontainers = $allKontainers->sortBy('nomor_seri_gabungan');

        $pricelistRings = \App\Models\PricelistUangJalanBatam::activeBbm()->orderBy('ring')
            ->get(['ring', 'expedisi', 'wilayah', 'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty'])
            ->flatMap(function ($item) {
                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $trimmed = trim($sw);
                        if ($trimmed !== '') {
                            $mapped[] = [
                                'name' => $trimmed,
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'rates' => [
                                    '20_F' => $item->tarif_20ft_full,
                                    '20_E' => $item->tarif_20ft_empty,
                                    '40_F' => $item->tarif_40ft_full,
                                    '40_E' => $item->tarif_40ft_empty,
                                    '45_F' => $item->tarif_40ft_full,
                                    '45_E' => $item->tarif_40ft_empty,
                                ],
                            ];
                            $mapped[] = [
                                'name' => $trimmed . ' (' . $item->expedisi . ')',
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'rates' => [
                                    '20_F' => $item->tarif_20ft_full,
                                    '20_E' => $item->tarif_20ft_empty,
                                    '40_F' => $item->tarif_40ft_full,
                                    '40_E' => $item->tarif_40ft_empty,
                                    '45_F' => $item->tarif_40ft_full,
                                    '45_E' => $item->tarif_40ft_empty,
                                ],
                            ];
                        }
                    }
                }
                return $mapped;
            })
            ->unique('name')
            ->values();

        $locations = $pricelistRings->pluck('name');

        $warehouses = \App\Models\Gudang::orderBy('nama_gudang')->pluck('nama_gudang');

        return view('surat-jalan-tarik-kosong-batam.edit', compact('item', 'supirs', 'keneks', 'mobils', 'kontainers', 'locations', 'warehouses', 'pricelistRings'));
    }

    public function update(Request $request, $id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);

        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_tarik_kosong_batams,no_surat_jalan,'.$id,
            'tanggal_surat_jalan' => 'required|date',

            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'kenek' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'size' => 'nullable|string',
            'f_e' => 'nullable|string',
            'status' => 'nullable|in:draft,active,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['status'] = $validated['status'] ?? $item->status ?? 'active';
        $item->update($validated);

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil dihapus');
    }

    public function print($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);

        return view('surat-jalan-tarik-kosong-batam.print', compact('item'));
    }

    public function checkContainerSizes(Request $request)
    {
        $no_kontainers = $request->input('no_kontainers', []);
        
        if (empty($no_kontainers) || !is_array($no_kontainers)) {
            return response()->json(['success' => true, 'sizes' => []]);
        }

        // Clean container numbers
        $cleanedNumbers = array_map(function($no) {
            return preg_replace('/[^A-Za-z0-9]/', '', $no);
        }, $no_kontainers);
        
        $cleanedNumbers = array_filter(array_unique($cleanedNumbers));

        // First check in stock_kontainers
        $stockSizes = \App\Models\StockKontainer::whereIn('nomor_seri_gabungan', $cleanedNumbers)
            ->whereNotNull('ukuran')
            ->pluck('ukuran', 'nomor_seri_gabungan')
            ->toArray();

        // Find which ones are still missing
        $missingNumbers = array_diff($cleanedNumbers, array_keys($stockSizes));

        $kontainerSizes = [];
        if (!empty($missingNumbers)) {
            // Check in kontainers for the missing ones
            $kontainerSizes = \App\Models\Kontainer::whereIn('nomor_seri_gabungan', $missingNumbers)
                ->whereNotNull('ukuran')
                ->pluck('ukuran', 'nomor_seri_gabungan')
                ->toArray();
        }

        // Merge results
        $allSizes = array_merge($stockSizes, $kontainerSizes);

        // Map back to original input strings (ignoring punctuation differences)
        $result = [];
        foreach ($no_kontainers as $original) {
            if (!$original) continue;
            $clean = preg_replace('/[^A-Za-z0-9]/', '', $original);
            if (isset($allSizes[$clean])) {
                $result[$original] = $allSizes[$clean];
            }
        }

        return response()->json([
            'success' => true,
            'sizes' => $result
        ]);
    }

    public function storeBulk(Request $request)
    {
        $rows = $request->input('rows', []);
        $gudang_tujuan_id = $request->input('gudang_tujuan_id', null);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim.',
            ], 422);
        }

        $successCount = 0;
        $errors = [];
        $failedRows = [];

        // Map valid karyawans (separated by division) and kendaraans to validate and auto-correct bulk data
        $supirMap = [];
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')->get(['nama_panggilan', 'nama_lengkap']);
        foreach ($karyawanSupirs as $k) {
            if ($k->nama_panggilan) $supirMap[strtolower(trim($k->nama_panggilan))] = $k->nama_panggilan;
            if ($k->nama_lengkap)   $supirMap[strtolower(trim($k->nama_lengkap))] = $k->nama_panggilan ?: $k->nama_lengkap;
        }

        $allKendaraansMap = [];
        foreach (\App\Models\Mobil::all(['nomor_polisi']) as $m) {
            if ($m->nomor_polisi) {
                $allKendaraansMap[strtolower(trim(str_replace(' ', '', $m->nomor_polisi)))] = $m->nomor_polisi;
            }
        }

        try {
            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 1;
                $nomorSuratJalan = trim($row['no_surat_jalan'] ?? '');

                if (empty($nomorSuratJalan)) {
                    $errors[] = "Baris {$rowNumber}: Nomor Surat Jalan wajib diisi.";
                    $failedRows[] = $row['_original_line'] ?? '';
                    continue;
                }

                $tanggalSuratJalan = trim($row['tanggal_surat_jalan'] ?? '');
                if (empty($tanggalSuratJalan)) {
                    $errors[] = "Baris {$rowNumber}: Tanggal wajib diisi.";
                    $failedRows[] = $row['_original_line'] ?? '';
                    continue;
                }

                $tujuanPengambilan = trim($row['tujuan_pengambilan'] ?? '');
                $gudangTujuanInput = trim($row['gudang_tujuan'] ?? '');
                
                // Determine the Gudang ID to use
                // 1. Try to match from row if specified
                $rowGudangId = null;
                if (!empty($gudangTujuanInput)) {
                    $matchedGudang = \App\Models\Gudang::where('nama_gudang', 'like', "%{$gudangTujuanInput}%")->first();
                    if ($matchedGudang) {
                        $rowGudangId = $matchedGudang->id;
                    } else {
                        $errors[] = "Baris {$rowNumber}: Gudang Tujuan '{$gudangTujuanInput}' tidak valid atau tidak ditemukan.";
                        $failedRows[] = $row['_original_line'] ?? '';
                        continue;
                    }
                } else {
                    // 2. Fallback to global setting if no row specific setting matched
                    $rowGudangId = $gudang_tujuan_id;
                }
                
                if (empty($rowGudangId)) {
                    $errors[] = "Baris {$rowNumber}: Gudang Tujuan wajib diisi atau tidak valid.";
                    $failedRows[] = $row['_original_line'] ?? '';
                    continue;
                }

                $supir = trim($row['supir'] ?? '');
                if ($supir) {
                    $lowerSupir = strtolower($supir);
                    if (isset($supirMap[$lowerSupir])) {
                        $supir = $supirMap[$lowerSupir];
                    }
                }

                $noPlat = trim($row['no_plat'] ?? '');
                if ($noPlat) {
                    $lowerPlat = strtolower(str_replace(' ', '', $noPlat));
                    if (isset($allKendaraansMap[$lowerPlat])) {
                        $noPlat = $allKendaraansMap[$lowerPlat];
                    }
                }

                // Cek duplikasi no SJ
                $exists = SuratJalanTarikKosongBatam::where('no_surat_jalan', $nomorSuratJalan)->exists();
                if ($exists) {
                    $errors[] = "Baris {$rowNumber}: Surat Jalan '{$nomorSuratJalan}' sudah ada (duplikat).";
                    $failedRows[] = $row['_original_line'] ?? '';
                    continue;
                }
                
                $noKontainer = trim($row['no_kontainer'] ?? '');

                SuratJalanTarikKosongBatam::create([
                    'no_surat_jalan' => $nomorSuratJalan,
                    'tanggal_surat_jalan' => $tanggalSuratJalan,
                    'tujuan_pengambilan' => $tujuanPengambilan,
                    'supir' => $supir,
                    'no_plat' => $noPlat,
                    'no_kontainer' => $noKontainer,
                    'size' => trim($row['size'] ?? ''),
                    'f_e' => 'Empty', // Tarik kosong implies empty
                    'status' => 'active',
                    'catatan' => trim($row['catatan'] ?? ''),
                    'uang_jalan' => $row['uang_jalan'] ?? 0,
                    'gudang_tujuan_id' => $rowGudangId,
                    'input_by' => Auth::id(),
                    'input_date' => now(),
                    'lokasi' => 'batam'
                ]);

                if (!empty($noKontainer)) {
                    $stockKontainer = \App\Models\StockKontainer::where('nomor_seri_gabungan', $noKontainer)
                        ->where('status', '!=', 'inactive')
                        ->first();
                    
                    if ($stockKontainer) {
                        $asalGudangId = $stockKontainer->gudangs_id;
                        $stockKontainer->update(['gudangs_id' => $rowGudangId]);

                        \App\Models\HistoryKontainer::create([
                            'nomor_kontainer' => $noKontainer,
                            'tipe_kontainer' => 'stock',
                            'jenis_kegiatan' => 'Tarik Kosong Batam',
                            'tanggal_kegiatan' => $tanggalSuratJalan,
                            'asal_gudang_id' => $asalGudangId,
                            'gudang_id' => $rowGudangId,
                            'keterangan' => 'SJ Tarik Kosong Batam: ' . $nomorSuratJalan,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }

                $successCount++;
            }

            if ($successCount > 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses {$successCount} surat jalan.",
                'errors' => $errors,
                'failedRows' => $failedRows,
                'successCount' => $successCount,
                'hasErrors' => count($errors) > 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}

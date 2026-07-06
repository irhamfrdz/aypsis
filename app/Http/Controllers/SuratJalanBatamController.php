<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use App\Models\PricelistUangJalanBatam;
use App\Models\StockKontainer;
use App\Models\SuratJalanBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratJalanBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalanBatam::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('jenis_barang', 'like', "%{$search}%")
                    ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%")
                    ->orWhere('f_e', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by status pembayaran
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'all') {
            $statusPembayaran = $request->status_pembayaran;
            $query->where(function ($q) use ($statusPembayaran) {
                if ($statusPembayaran === 'sudah_dibayar') {
                    $q->where('status_pembayaran', 'sudah_dibayar')
                        ->orWhere('status_pembayaran_uang_jalan', 'dibayar');
                } elseif ($statusPembayaran === 'belum_dibayar') {
                    $q->where('status_pembayaran_uang_jalan', 'sudah_masuk_uang_jalan')
                        ->where('status_pembayaran', '!=', 'sudah_dibayar');
                } else { // belum_masuk_pranota
                    $q->where('status_pembayaran_uang_jalan', 'belum_ada')
                        ->where('status_pembayaran', '!=', 'sudah_dibayar');
                }
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $suratJalans = $query->with('orderBatam')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Fetch shared dropdowns for bulk create modal
        $prevBbm = \App\Models\KelolaBbm::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->skip(1)->first();
        $prevPersentase = $prevBbm ? $prevBbm->persentase : 0;

        $pricelistRings = PricelistUangJalanBatam::activeBbm()->select(
            'ring', 'expedisi', 'status', 'wilayah',
            'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty',
            'tarif_20ft_full_base', 'tarif_20ft_empty_base', 'tarif_40ft_full_base', 'tarif_40ft_empty_base'
        )
            ->get()
            ->flatMap(function ($item) use ($prevPersentase) {
                if ($prevPersentase <= 5) {
                    $prev20Full = $item->tarif_20ft_full_base ?? $item->tarif_20ft_full;
                    $prev20Empty = $item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty;
                    $prev40Full = $item->tarif_40ft_full_base ?? $item->tarif_40ft_full;
                    $prev40Empty = $item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty;
                } else {
                    $perubahanTarif = $prevPersentase - 5;
                    $faktorPengali = 1 + ($perubahanTarif / 100);
                    $prev20Full = round(($item->tarif_20ft_full_base ?? $item->tarif_20ft_full) * $faktorPengali);
                    $prev20Empty = round(($item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty) * $faktorPengali);
                    $prev40Full = round(($item->tarif_40ft_full_base ?? $item->tarif_40ft_full) * $faktorPengali);
                    $prev40Empty = round(($item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty) * $faktorPengali);
                }

                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $trimmed = trim($sw);
                        if ($trimmed !== '') {
                            $mapped[] = [
                                'value' => $trimmed,
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'ring' => $item->ring,
                                'rates' => [
                                    '20_Full' => $item->tarif_20ft_full,
                                    '20_Empty' => $item->tarif_20ft_empty,
                                    '40_Full' => $item->tarif_40ft_full,
                                    '40_Empty' => $item->tarif_40ft_empty,
                                ],
                                'rates_prev' => [
                                    '20_Full' => $prev20Full,
                                    '20_Empty' => $prev20Empty,
                                    '40_Full' => $prev40Full,
                                    '40_Empty' => $prev40Empty,
                                ],
                            ];
                        }
                    }
                }

                return $mapped;
            })
            ->unique('value')
            ->values();

        $terms = \App\Models\Term::orderBy('kode')->get();

        return view('surat-jalan-batam.index', compact('suratJalans', 'pricelistRings', 'terms'));
    }

    /**
     * Show order selection page before creating surat jalan batam.
     */
    public function selectOrder(Request $request)
    {
        $query = \App\Models\OrderBatam::whereIn('status', ['active', 'confirmed', 'processing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                    ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                    ->orWhere('tujuan_ambil', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('tanggal_order', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('surat-jalan-batam.select-order', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedOrder = null;
        if ($request->filled('order_id')) {
            $selectedOrder = \App\Models\OrderBatam::with(['pengirim', 'jenisBarang', 'tujuanAmbil', 'term'])
                ->find($request->order_id);
        }

        $supirs = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->get();
        $keneks = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KENEK')->get();
        $kranis = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KRANI')->get();
        $terms = \App\Models\Term::orderBy('kode')->get();
        $masterKegiatans = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
            ->where('status', 'aktif')
            ->orderBy('nama_kegiatan')
            ->get();
        $jenisBarangs = \App\Models\JenisBarang::orderBy('nama_barang')->get();

        // Calculate default uang jalan from pricelist
        $defaultUangJalan = 0;
        if ($selectedOrder) {
            $pricelist = null; // Removed rute lookup

            if ($pricelist) {
                $defaultUangJalan = $pricelist->tarif;
            }
        }

        // Get normalized ukuran kontainer (normalize 20, 20ft, 20FT to 20FT)
        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->get()
            ->map(function ($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));

                return $val ? $val.'FT' : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $stockContainers = StockKontainer::whereNotNull('nomor_seri_gabungan')
            ->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->get();
        $sewaContainers = Kontainer::whereNotNull('nomor_seri_gabungan')
            ->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->get();

        $daftarKontainers = $stockContainers->concat($sewaContainers)
            ->unique('nomor_seri_gabungan')
            ->map(function ($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));
                $size = $val ? $val.'FT' : '';

                return [
                    'no' => $item->nomor_seri_gabungan,
                    'size' => $size,
                    'tipe' => $item->tipe_kontainer ?? '',
                ];
            })
            ->sortBy('no')
            ->values()
            ->toArray();

        $prevBbm = \App\Models\KelolaBbm::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->skip(1)->first();
        $prevPersentase = $prevBbm ? $prevBbm->persentase : 0;

        $pricelistRings = PricelistUangJalanBatam::activeBbm()->select(
            'ring', 'expedisi', 'status', 'wilayah',
            'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty',
            'tarif_20ft_full_base', 'tarif_20ft_empty_base', 'tarif_40ft_full_base', 'tarif_40ft_empty_base'
        )
            ->get()
            ->flatMap(function ($item) use ($prevPersentase) {
                if ($prevPersentase <= 5) {
                    $prev20Full = $item->tarif_20ft_full_base ?? $item->tarif_20ft_full;
                    $prev20Empty = $item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty;
                    $prev40Full = $item->tarif_40ft_full_base ?? $item->tarif_40ft_full;
                    $prev40Empty = $item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty;
                } else {
                    $perubahanTarif = $prevPersentase - 5;
                    $faktorPengali = 1 + ($perubahanTarif / 100);
                    $prev20Full = round(($item->tarif_20ft_full_base ?? $item->tarif_20ft_full) * $faktorPengali);
                    $prev20Empty = round(($item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty) * $faktorPengali);
                    $prev40Full = round(($item->tarif_40ft_full_base ?? $item->tarif_40ft_full) * $faktorPengali);
                    $prev40Empty = round(($item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty) * $faktorPengali);
                }

                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $trimmed = trim($sw);
                        if ($trimmed !== '') {
                            $mapped[] = [
                                'value' => $trimmed,
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'ring' => $item->ring,
                                'rates' => [
                                    '20_Full' => $item->tarif_20ft_full,
                                    '20_Empty' => $item->tarif_20ft_empty,
                                    '40_Full' => $item->tarif_40ft_full,
                                    '40_Empty' => $item->tarif_40ft_empty,
                                ],
                                'rates_prev' => [
                                    '20_Full' => $prev20Full,
                                    '20_Empty' => $prev20Empty,
                                    '40_Full' => $prev40Full,
                                    '40_Empty' => $prev40Empty,
                                ],
                            ];
                        }
                    }
                }

                return $mapped;
            })
            ->unique('value')
            ->values();

        $masterTujuanKirims = \App\Models\MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();

        $allPenerimas = \App\Models\Penerima::orderBy('nama_penerima')->get();

        return view('surat-jalan-batam.create', compact('selectedOrder', 'supirs', 'keneks', 'kranis', 'terms', 'masterKegiatans', 'jenisBarangs', 'defaultUangJalan', 'ukuranKontainers', 'daftarKontainers', 'pricelistRings', 'masterTujuanKirims', 'allPenerimas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_batam_id' => 'nullable|exists:order_batams,id',
            'no_surat_jalan' => 'required|unique:surat_jalan_batams,no_surat_jalan',
            'tanggal_surat_jalan' => 'required|date',
            'term' => 'nullable|string',
            'ring' => 'nullable|string',
            'aktifitas' => 'nullable|string',
            'pengirim' => 'nullable|string',
            'penerima' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jenis_barang' => 'nullable|string',
            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'kenek' => 'nullable|string',
            'krani' => 'nullable|string',
            'uang_jalan' => 'nullable|string',
            'lembur' => 'nullable|boolean',
            'nginap' => 'nullable|boolean',
            'is_supir_customer' => 'nullable|boolean',
            'size' => 'nullable|string',
            'tipe_kontainer' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'no_seal' => 'nullable|string',
            'f_e' => 'nullable|string',
            'rit' => 'nullable|string',
            'karton' => 'nullable|integer',
            'plastik' => 'nullable|integer',
            'terpal' => 'nullable|integer',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['tanpa_uang_jalan'] = $request->has('tanpa_uang_jalan') ? 1 : 0;

        $validated['input_by'] = Auth::id();
        $validated['input_date'] = now();

        $suratJalan = SuratJalanBatam::create($validated);

        // Optional: Update order progress if linked
        if ($suratJalan->order_batam_id) {
            $order = $suratJalan->orderBatam;
            if ($order && $order->isOutstanding()) {
                $order->processUnits(1, 'Dibuat Surat Jalan Batam: '.$suratJalan->no_surat_jalan);
            }
        }

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $suratJalan = SuratJalanBatam::with('orderBatam')->findOrFail($id);

        return view('surat-jalan-batam.show', compact('suratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        $supirs = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->get();
        $keneks = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KENEK')->get();
        $kranis = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KRANI')->get();
        $terms = \App\Models\Term::orderBy('kode')->get();
        $masterKegiatans = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
            ->where('status', 'aktif')
            ->orderBy('nama_kegiatan')
            ->get();
        $jenisBarangs = \App\Models\JenisBarang::orderBy('nama_barang')->get();

        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->get()
            ->map(function ($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));

                return $val ? $val.'FT' : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $stockContainers = StockKontainer::whereNotNull('nomor_seri_gabungan')
            ->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->get();
        $sewaContainers = Kontainer::whereNotNull('nomor_seri_gabungan')
            ->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->get();

        $daftarKontainers = $stockContainers->concat($sewaContainers)
            ->unique('nomor_seri_gabungan')
            ->map(function ($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));
                $size = $val ? $val.'FT' : '';

                return [
                    'no' => $item->nomor_seri_gabungan,
                    'size' => $size,
                    'tipe' => $item->tipe_kontainer ?? '',
                ];
            })
            ->sortBy('no')
            ->values()
            ->toArray();

        $prevBbm = \App\Models\KelolaBbm::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->skip(1)->first();
        $prevPersentase = $prevBbm ? $prevBbm->persentase : 0;

        $pricelistRings = PricelistUangJalanBatam::activeBbm()->select(
            'ring', 'expedisi', 'status', 'wilayah',
            'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty',
            'tarif_20ft_full_base', 'tarif_20ft_empty_base', 'tarif_40ft_full_base', 'tarif_40ft_empty_base'
        )
            ->get()
            ->flatMap(function ($item) use ($prevPersentase) {
                if ($prevPersentase <= 5) {
                    $prev20Full = $item->tarif_20ft_full_base ?? $item->tarif_20ft_full;
                    $prev20Empty = $item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty;
                    $prev40Full = $item->tarif_40ft_full_base ?? $item->tarif_40ft_full;
                    $prev40Empty = $item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty;
                } else {
                    $perubahanTarif = $prevPersentase - 5;
                    $faktorPengali = 1 + ($perubahanTarif / 100);
                    $prev20Full = round(($item->tarif_20ft_full_base ?? $item->tarif_20ft_full) * $faktorPengali);
                    $prev20Empty = round(($item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty) * $faktorPengali);
                    $prev40Full = round(($item->tarif_40ft_full_base ?? $item->tarif_40ft_full) * $faktorPengali);
                    $prev40Empty = round(($item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty) * $faktorPengali);
                }

                $mapped = [];
                if ($item->wilayah) {
                    $subWilayahs = explode(',', $item->wilayah);
                    foreach ($subWilayahs as $sw) {
                        $trimmed = trim($sw);
                        if ($trimmed !== '') {
                            $mapped[] = [
                                'value' => $trimmed,
                                'label' => $trimmed.' (Ring '.$item->ring.' - '.$item->expedisi.')',
                                'ring' => $item->ring,
                                'rates' => [
                                    '20_Full' => $item->tarif_20ft_full,
                                    '20_Empty' => $item->tarif_20ft_empty,
                                    '40_Full' => $item->tarif_40ft_full,
                                    '40_Empty' => $item->tarif_40ft_empty,
                                ],
                                'rates_prev' => [
                                    '20_Full' => $prev20Full,
                                    '20_Empty' => $prev20Empty,
                                    '40_Full' => $prev40Full,
                                    '40_Empty' => $prev40Empty,
                                ],
                            ];
                        }
                    }
                }

                return $mapped;
            })
            ->unique('value')
            ->values();

        $masterTujuanKirims = \App\Models\MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();

        $allPenerimas = \App\Models\Penerima::orderBy('nama_penerima')->get();

        return view('surat-jalan-batam.edit', compact('suratJalan', 'supirs', 'keneks', 'kranis', 'terms', 'masterKegiatans', 'jenisBarangs', 'ukuranKontainers', 'daftarKontainers', 'pricelistRings', 'masterTujuanKirims', 'allPenerimas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);

        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_batams,no_surat_jalan,'.$id,
            'tanggal_surat_jalan' => 'required|date',
            'term' => 'nullable|string',
            'ring' => 'nullable|string',
            'aktifitas' => 'nullable|string',
            'pengirim' => 'nullable|string',
            'penerima' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jenis_barang' => 'nullable|string',
            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'kenek' => 'nullable|string',
            'krani' => 'nullable|string',
            'uang_jalan' => 'nullable|string',
            'lembur' => 'nullable|boolean',
            'nginap' => 'nullable|boolean',
            'is_supir_customer' => 'nullable|boolean',
            'size' => 'nullable|string',
            'tipe_kontainer' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'no_seal' => 'nullable|string',
            'f_e' => 'nullable|string',
            'rit' => 'nullable|string',
            'karton' => 'nullable|integer',
            'plastik' => 'nullable|integer',
            'terpal' => 'nullable|integer',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['tanpa_uang_jalan'] = $request->has('tanpa_uang_jalan') ? 1 : 0;

        $suratJalan->update($validated);

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        $suratJalan->delete();

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil dihapus');
    }

    public function updateStatus(Request $request, $id)
    {
        if (! Auth::user()->can('surat-jalan-batam-approve')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $suratJalan = SuratJalanBatam::findOrFail($id);
        $suratJalan->status = $request->status;
        $suratJalan->save();

        return response()->json(['success' => true]);
    }

    public function print($id)
    {
        $suratJalan = SuratJalanBatam::with('orderBatam')->findOrFail($id);

        // This would normally return a PDF or a print view
        return view('surat-jalan-batam.print', compact('suratJalan'));
    }

    public function printMemo($id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);

        return view('surat-jalan-batam.print-memo', compact('suratJalan'));
    }

    public function printPreprinted($id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);

        return view('surat-jalan-batam.print-preprinted', compact('suratJalan'));
    }

    public function generateSuratJalanBatamNumber(Request $request)
    {
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $year = $date->format('Y');
        $month = $date->format('m');

        $lastSj = SuratJalanBatam::whereYear('tanggal_surat_jalan', $year)
            ->whereMonth('tanggal_surat_jalan', $month)
            ->orderBy('no_surat_jalan', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSj) {
            $parts = explode('/', $lastSj->no_surat_jalan);
            $lastNum = (int) end($parts);
            $nextNumber = $lastNum + 1;
        }

        $formattedNumber = "SJB/{$year}/{$month}/".str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['number' => $formattedNumber]);
    }

    private function calculateUangJalanValue($tujuan, $size, $fe)
    {
        if (empty($tujuan) || empty($size) || empty($fe)) {
            return 0;
        }

        $prevBbm = \App\Models\KelolaBbm::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->skip(1)->first();
        $prevPersentase = $prevBbm ? $prevBbm->persentase : 0;

        $pricelists = PricelistUangJalanBatam::activeBbm()->get();
        foreach ($pricelists as $item) {
            if ($item->wilayah) {
                $subWilayahs = array_map('trim', explode(',', $item->wilayah));
                if (in_array(trim($tujuan), $subWilayahs)) {
                    $sizeNum = preg_replace('/\D/', '', $size);
                    $feClean = ucfirst(strtolower(trim($fe)));

                    if ($prevPersentase <= 5) {
                        $tarif_20_full = $item->tarif_20ft_full_base ?? $item->tarif_20ft_full;
                        $tarif_20_empty = $item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty;
                        $tarif_40_full = $item->tarif_40ft_full_base ?? $item->tarif_40ft_full;
                        $tarif_40_empty = $item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty;
                    } else {
                        $perubahanTarif = $prevPersentase - 5;
                        $faktorPengali = 1 + ($perubahanTarif / 100);
                        $tarif_20_full = round(($item->tarif_20ft_full_base ?? $item->tarif_20ft_full) * $faktorPengali);
                        $tarif_20_empty = round(($item->tarif_20ft_empty_base ?? $item->tarif_20ft_empty) * $faktorPengali);
                        $tarif_40_full = round(($item->tarif_40ft_full_base ?? $item->tarif_40ft_full) * $faktorPengali);
                        $tarif_40_empty = round(($item->tarif_40ft_empty_base ?? $item->tarif_40ft_empty) * $faktorPengali);
                    }

                    if ($sizeNum == '20') {
                        return $feClean === 'Full' ? $tarif_20_full : $tarif_20_empty;
                    } elseif ($sizeNum == '40') {
                        return $feClean === 'Full' ? $tarif_40_full : $tarif_40_empty;
                    }
                }
            }
        }

        return 0;
    }

    public function storeBulk(Request $request)
    {
        $rows = $request->input('rows', []);

        $sharedTanggal = $request->input('tanggal_surat_jalan', date('Y-m-d'));
        $sharedPengirim = $request->input('pengirim');
        $sharedPenerima = $request->input('penerima');
        $sharedAlamat = $request->input('alamat');
        $sharedTujuanAmbil = $request->input('tujuan_pengambilan');
        $sharedTujuanKirim = $request->input('tujuan_pengiriman');
        $sharedTerm = $request->input('term');
        $sharedStatus = $request->input('status', 'active');

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim.',
            ], 422);
        }

        $successCount = 0;
        $errors = [];

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 1;
                $noSj = trim($row['nomor_surat_jalan'] ?? '');

                if (empty($noSj)) {
                    $errors[] = "Baris {$rowNumber}: Nomor Surat Jalan wajib diisi.";

                    continue;
                }

                if (SuratJalanBatam::where('no_surat_jalan', $noSj)->exists()) {
                    $errors[] = "Baris {$rowNumber}: Nomor Surat Jalan '{$noSj}' sudah terdaftar.";

                    continue;
                }

                $tujuanKirim = trim($row['tujuan_pengiriman'] ?? '') ?: $sharedTujuanKirim;
                $size = trim($row['size'] ?? '') ?: '20FT';
                $fe = trim($row['f_e'] ?? '') ?: 'Full';

                // Resolve ring
                $ring = null;
                if ($tujuanKirim) {
                    $pricelist = PricelistUangJalanBatam::activeBbm()->get();
                    foreach ($pricelist as $pl) {
                        if ($pl->wilayah) {
                            $subWilayahs = array_map('trim', explode(',', $pl->wilayah));
                            if (in_array(trim($tujuanKirim), $subWilayahs)) {
                                $ring = $pl->ring;
                                break;
                            }
                        }
                    }
                }

                $uangJalan = $this->calculateUangJalanValue($tujuanKirim, $size, $fe);

                SuratJalanBatam::create([
                    'no_surat_jalan' => $noSj,
                    'tanggal_surat_jalan' => trim($row['tanggal_surat_jalan'] ?? '') ?: $sharedTanggal,
                    'no_kontainer' => trim($row['no_kontainer'] ?? '') ?: null,
                    'no_seal' => trim($row['no_seal'] ?? '') ?: null,
                    'size' => $size,
                    'tipe_kontainer' => trim($row['tipe_kontainer'] ?? '') ?: 'Dry Container',
                    'f_e' => $fe,
                    'supir' => trim($row['supir'] ?? '') ?: null,
                    'no_plat' => trim($row['no_plat'] ?? '') ?: null,
                    'kenek' => trim($row['kenek'] ?? '') ?: null,
                    'krani' => trim($row['krani'] ?? '') ?: null,
                    'jenis_barang' => trim($row['jenis_barang'] ?? '') ?: null,
                    'pengirim' => trim($row['pengirim'] ?? '') ?: $sharedPengirim,
                    'penerima' => trim($row['penerima'] ?? '') ?: $sharedPenerima,
                    'alamat' => trim($row['alamat'] ?? '') ?: $sharedAlamat,
                    'tujuan_pengambilan' => trim($row['tujuan_pengambilan'] ?? '') ?: $sharedTujuanAmbil,
                    'tujuan_pengiriman' => $tujuanKirim,
                    'term' => trim($row['term'] ?? '') ?: $sharedTerm,
                    'ring' => $ring,
                    'uang_jalan' => $uangJalan,
                    'status' => $sharedStatus,
                    'input_by' => Auth::id(),
                    'input_date' => now(),
                ]);

                $successCount++;
            }

            if ($successCount > 0) {
                \Illuminate\Support\Facades\DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menyimpan {$successCount} data Surat Jalan Batam.",
                    'errors' => $errors,
                    'redirect' => route('surat-jalan-batam.index'),
                ]);
            } else {
                \Illuminate\Support\Facades\DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data valid yang dapat disimpan.',
                    'errors' => $errors,
                ], 422);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: '.$e->getMessage(),
                'errors' => $errors,
            ], 500);
        }
    }
}

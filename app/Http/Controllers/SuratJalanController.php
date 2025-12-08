<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\Prospek;
use App\Models\User;
use App\Models\Order;
use App\Models\Karyawan;
use App\Models\TujuanKegiatanUtama;
use App\Models\Permohonan;
use App\Models\MasterKegiatan;
use App\Models\MasterTujuanKirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SuratJalanExport;

class SuratJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalan::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by status pembayaran with overall logic
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'all') {
            $statusPembayaran = $request->status_pembayaran;
            
            $query->where(function($q) use ($statusPembayaran) {
                if ($statusPembayaran === 'sudah_dibayar') {
                    // Sudah dibayar: status_pembayaran = 'sudah_dibayar' OR status_pembayaran_uang_jalan = 'dibayar'
                    $q->where('status_pembayaran', 'sudah_dibayar')
                      ->orWhere('status_pembayaran_uang_jalan', 'dibayar');
                } elseif ($statusPembayaran === 'belum_dibayar') {
                    // Belum dibayar: ada uang jalan tapi belum dibayar
                    $q->where('status_pembayaran_uang_jalan', 'sudah_masuk_uang_jalan')
                      ->where('status_pembayaran', '!=', 'sudah_dibayar');
                } else { // belum_masuk_pranota
                    // Belum masuk pranota: belum ada uang jalan sama sekali
                    $q->where('status_pembayaran_uang_jalan', 'belum_ada')
                      ->where('status_pembayaran', '!=', 'sudah_dibayar');
                }
            });
        }

        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $suratJalans = $query->with('order')
                    ->withCount('pranotaUangRit')
                            ->orderBy('created_at', 'desc')
                            ->orderBy('tanggal_surat_jalan', 'desc')
                            ->orderBy('id', 'desc')
                            ->paginate(15);

        return view('surat-jalan.index', compact('suratJalans'));
    }

    /**
     * Export surat jalan listing to Excel with current filters
     */
    public function exportExcel(Request $request)
    {
        // Permission check, reuse surat-jalan-view or dedicated export permission
        if (!auth()->user()->can('surat-jalan-export')) {
            Log::warning('User lacks permission for surat-jalan-export', ['user_id' => auth()->id()]);
            abort(403, 'Anda tidak memiliki permission untuk melakukan export surat jalan.');
        }

        try {
            $filters = $request->only(['search', 'status', 'status_pembayaran', 'tipe_kontainer', 'start_date', 'end_date']);
            $fileName = 'surat_jalan_export_' . date('Ymd_His') . '.xlsx';
            $export = new SuratJalanExport($filters, []);
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting surat jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal export surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Show order selection page before creating surat jalan.
     */
    public function selectOrder(Request $request)
    {
        $query = Order::with(['pengirim', 'jenisBarang', 'tujuanAmbil'])
                     ->whereIn('status', ['active', 'confirmed', 'processing']); // Order dengan status valid

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                  ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_ambil', 'like', "%{$search}%")
                  ->orWhereHas('pengirim', function($q) use ($search) {
                      $q->where('nama_pengirim', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jenisBarang', function($q) use ($search) {
                      $q->where('nama_barang', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('nomor_order', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(15);

        return view('surat-jalan.select-order', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedOrder = null;

        // If order_id is provided, get the order data
        if ($request->filled('order_id')) {
            $selectedOrder = Order::with(['pengirim', 'jenisBarang', 'tujuanAmbil', 'term'])
                                  ->find($request->order_id);

            // Validasi order exists dan status valid
            if (!$selectedOrder || !in_array($selectedOrder->status, ['active', 'confirmed', 'processing'])) {
                return redirect()->route('surat-jalan.select-order')
                                ->with('error', 'Order tidak valid atau tidak tersedia untuk membuat surat jalan.');
            }

            // Approval system removed - no validation needed
        } else {
            // Jika tidak ada order yang dipilih, redirect ke halaman select order
            return redirect()->route('surat-jalan.select-order')
                            ->with('info', 'Silakan pilih order terlebih dahulu untuk membuat surat jalan.');
        }

        // Get karyawan supir data - hanya divisi supir
        $supirs = Karyawan::where('divisi', 'supir')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_panggilan')
                         ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);

        // Get karyawan kenek data - hanya divisi krani
        $keneks = Karyawan::where('divisi', 'krani')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap']);

        // Get kegiatan surat jalan from master kegiatan
        $kegiatanSuratJalan = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
                                                        ->where('status', 'Aktif')
                                                        ->orderBy('nama_kegiatan')
                                                        ->get(['id', 'nama_kegiatan']);

        // Get kontainer data dari 2 table: stock_kontainers dan kontainers
        // 1. Dari table stock_kontainers - hanya yang available/tersedia
        $stockKontainers = \App\Models\StockKontainer::whereIn('status', ['available', 'tersedia'])
                                                     ->orderBy('nomor_seri_gabungan')
                                                     ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        // 2. Dari table kontainers - hanya yang status tersedia
        $kontainers = \App\Models\Kontainer::where('status', 'tersedia')
                                          ->orderBy('nomor_seri_gabungan')
                                          ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        // Gabungkan kedua data dengan format yang sama
        $allKontainers = collect();
        
        // Tambahkan data dari stock_kontainers
        foreach ($stockKontainers as $stock) {
            $allKontainers->push((object)[
                'id' => 'stock_' . $stock->id,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers'
            ]);
        }
        
        // Tambahkan data dari kontainers
        foreach ($kontainers as $kontainer) {
            $allKontainers->push((object)[
                'id' => 'kontainer_' . $kontainer->id,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers'
            ]);
        }
        
        // Urutkan berdasarkan nomor
        $stockKontainers = $allKontainers->sortBy('nomor_seri_gabungan');

        return view('surat-jalan.create', compact('selectedOrder', 'supirs', 'keneks', 'kegiatanSuratJalan', 'stockKontainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission explicitly
        if (!auth()->user()->can('surat-jalan-create')) {
            Log::warning('User lacks permission for surat-jalan-create', ['user_id' => auth()->id()]);
            return redirect()->back()
                           ->with('error', 'Anda tidak memiliki permission untuk membuat surat jalan.');
        }

        Log::info('Starting surat jalan validation');

        $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'tanggal_surat_jalan' => 'required|date',
            'no_surat_jalan' => 'required|string|max:255|unique:surat_jalans',
            'kegiatan' => 'required|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'tujuan_pengambilan' => 'nullable|string|max:255',
            'retur_barang' => 'nullable|string|max:255',
            'jumlah_retur' => 'nullable|integer|min:0',
            'supir' => 'nullable|string|max:255',
            'supir2' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'kenek' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:50',
            'nomor_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'jumlah_kontainer' => 'nullable|integer|min:1',
            'karton' => 'nullable|in:pakai,tidak_pakai',
            'plastik' => 'nullable|in:pakai,tidak_pakai',
            'terpal' => 'nullable|in:pakai,tidak_pakai',
            'tanggal_berangkat' => 'nullable|date',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'tanggal_muat' => 'nullable|date',
            'term' => 'nullable|string|max:255',
            'rit' => 'nullable|string|max:255',
            'uang_jalan' => 'nullable|numeric|min:0',
            'is_supir_customer' => 'nullable|in:0,1',
            'nama_supir_customer' => 'nullable|string|max:255',
            'no_pemesanan' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        Log::info('Validation passed successfully');

        // Approval system removed - no validation needed for approval status

        try {
            Log::info('Starting surat jalan creation process');
            Log::info('Request data:', $request->all());

            $data = $request->except(['gambar']);
            $data['input_by'] = Auth::id();
            $data['input_date'] = now();
            $data['status'] = 'draft'; // Set default status to draft

            // Map nomor_kontainer to no_kontainer (database column name)
            if (isset($data['nomor_kontainer'])) {
                $data['no_kontainer'] = $data['nomor_kontainer'];
                unset($data['nomor_kontainer']);
            }

            // If submitted as supir customer, set supir as the nama_supir_customer value
            if (isset($data['is_supir_customer']) && $data['is_supir_customer']) {
                // Prefer explicit customer name if supplied
                if (!empty($request->input('nama_supir_customer'))) {
                    $data['supir'] = $request->input('nama_supir_customer');
                } else {
                    // Fallback to placeholder
                    $data['supir'] = $data['supir'] ?? '__CUSTOMER__';
                }
            }

            // Handle cargo type - set default values for size and jumlah_kontainer if empty
            if (isset($data['tipe_kontainer']) && strtolower($data['tipe_kontainer']) === 'cargo') {
                if (empty($data['size'])) {
                    $data['size'] = null;
                }
                if (empty($data['jumlah_kontainer'])) {
                    $data['jumlah_kontainer'] = 1; // Default to 1 for cargo
                }
                Log::info('Cargo type detected, adjusting size and jumlah_kontainer', [
                    'size' => $data['size'],
                    'jumlah_kontainer' => $data['jumlah_kontainer']
                ]);
            }

            Log::info('Prepared data for saving:', $data);

            // Handle image upload
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('surat-jalan', $filename, 'public');
                $data['gambar'] = $path;
                Log::info('Image uploaded:', ['path' => $path]);
            }

            $suratJalan = SuratJalan::create($data);
            Log::info('Surat jalan created successfully:', [
                'id' => $suratJalan->id,
                'supir_saved' => $suratJalan->supir,
                'supir_from_request' => $request->input('supir'),
                'status' => $suratJalan->status,
                'status_pembayaran_uang_jalan' => $suratJalan->status_pembayaran_uang_jalan
            ]);

            // Langsung buat approval record untuk surat jalan
            \App\Models\SuratJalanApproval::create([
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => 'approval',
                'status' => 'pending',
            ]);

            Log::info('Surat jalan approval record created automatically:', [
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => 'approval',
                'status' => 'pending'
            ]);

            // NOTE: Units will be processed when surat jalan is approved, not when created
            // This ensures completion percentage only increases after proper approval workflow
            // If created with supir customer flag, immediately create prospek entries
            if (!empty($suratJalan->is_supir_customer)) {
                try {
                    $jumlahKontainer = $suratJalan->jumlah_kontainer ?? 1;
                    $nomorKontainerArray = [];
                    $noSealArray = [];

                    if (!empty($suratJalan->no_kontainer)) {
                        $nomorKontainerArray = array_filter(array_map('trim', explode(',', $suratJalan->no_kontainer)));
                    }
                    if (!empty($suratJalan->no_seal)) {
                        $noSealArray = array_filter(array_map('trim', explode(',', $suratJalan->no_seal)));
                    }

                    for ($i = 0; $i < max(1, (int)$jumlahKontainer); $i++) {
                        $nomorKontainerIni = isset($nomorKontainerArray[$i]) ? $nomorKontainerArray[$i] : null;
                        $noSealIni = isset($noSealArray[$i]) ? $noSealArray[$i] : null;

                        $prospekData = [
                            'tanggal' => $suratJalan->tanggal_surat_jalan ?? now(),
                            'nama_supir' => $suratJalan->supir,
                            'barang' => $suratJalan->jenis_barang ?? null,
                            'pt_pengirim' => $suratJalan->pengirim ?? null,
                            'ukuran' => $suratJalan->size ?? null,
                            'tipe' => $suratJalan->tipe_kontainer ?? null,
                            'no_surat_jalan' => $suratJalan->no_surat_jalan ?? null,
                            'surat_jalan_id' => $suratJalan->id,
                            'nomor_kontainer' => $nomorKontainerIni,
                            'no_seal' => $noSealIni,
                            'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman ?? null,
                            'nama_kapal' => null,
                               'keterangan' => 'Auto generated from Surat Jalan (Supir Customer): ' . ($suratJalan->no_surat_jalan ?? '-'),
                            'status' => Prospek::STATUS_AKTIF,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id()
                        ];

                        $createdProspek = Prospek::create($prospekData);
                        Log::info('Prospek created from Supir Customer Surat Jalan', ['prospek_id' => $createdProspek->id, 'surat_jalan_id' => $suratJalan->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error creating prospek from supir customer surat jalan: ' . $e->getMessage(), ['surat_jalan_id' => $suratJalan->id]);
                }
            }
            // Redirect to surat jalan index page
            return redirect()->route('surat-jalan.index')
                           ->with('success', 'Surat jalan berhasil dibuat dengan nomor: ' . $suratJalan->no_surat_jalan . '. Surat jalan telah otomatis masuk ke sistem approval.');

        } catch (\Exception $e) {
            Log::error('Error creating surat jalan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $suratJalan = SuratJalan::with('order')->findOrFail($id);
        return view('surat-jalan.show', compact('suratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $suratJalan = SuratJalan::findOrFail($id);

        // Cek apakah sudah ada pembayaran pranota uang jalan
        if ($suratJalan->status_pembayaran_uang_jalan === 'dibayar') {
            return redirect()->route('surat-jalan.index')
                           ->with('error', 'Surat jalan tidak dapat diedit karena pembayaran pranota uang jalan sudah dibuat.');
        }

        // Get karyawan supir data - hanya divisi supir
        $supirs = Karyawan::where('divisi', 'supir')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_panggilan')
                         ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);

        // Get karyawan kenek data - hanya divisi krani
        $keneks = Karyawan::where('divisi', 'krani')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap']);

        // Get kegiatan surat jalan from master kegiatan
        $kegiatanSuratJalan = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
                                                        ->where('status', 'Aktif')
                                                        ->orderBy('nama_kegiatan')
                                                        ->get(['id', 'nama_kegiatan']);

        // Get kontainer data dari 2 table: stock_kontainers dan kontainers
        // 1. Dari table stock_kontainers - hanya yang available/tersedia
        $stockKontainers = \App\Models\StockKontainer::whereIn('status', ['available', 'tersedia'])
                                                     ->orderBy('nomor_seri_gabungan')
                                                     ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        // 2. Dari table kontainers - hanya yang status tersedia
        $kontainers = \App\Models\Kontainer::where('status', 'tersedia')
                                          ->orderBy('nomor_seri_gabungan')
                                          ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        // Gabungkan kedua data dengan format yang sama
        $allKontainers = collect();
        
        // Tambahkan data dari stock_kontainers
        foreach ($stockKontainers as $stock) {
            $allKontainers->push((object)[
                'id' => 'stock_' . $stock->id,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers'
            ]);
        }
        
        // Tambahkan data dari kontainers
        foreach ($kontainers as $kontainer) {
            $allKontainers->push((object)[
                'id' => 'kontainer_' . $kontainer->id,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers'
            ]);
        }
        
        // Urutkan berdasarkan nomor
        $stockKontainers = $allKontainers->sortBy('nomor_seri_gabungan');

        // Get pengirim data for dropdown
        $pengirims = \App\Models\Pengirim::orderBy('nama_pengirim')->get(['id', 'nama_pengirim']);

        // Get jenis barang data for dropdown
        $jenisBarangOptions = \App\Models\JenisBarang::where('status', 'active')
                                                     ->orderBy('nama_barang')
                                                     ->get(['id', 'nama_barang']);

        // Get tujuan kegiatan utama untuk tujuan pengambilan
        $tujuanKegiatanUtamas = \App\Models\TujuanKegiatanUtama::orderBy('ke')
                                                               ->get(['id', 'ke']);

        // Get tujuan kirim untuk tujuan pengiriman
        $tujuanKirimOptions = \App\Models\MasterTujuanKirim::where('status', 'active')
                                                           ->orderBy('nama_tujuan')
                                                           ->get(['id', 'nama_tujuan']);

        return view('surat-jalan.edit', compact('suratJalan', 'supirs', 'keneks', 'kegiatanSuratJalan', 'stockKontainers', 'pengirims', 'jenisBarangOptions', 'tujuanKegiatanUtamas', 'tujuanKirimOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $suratJalan = SuratJalan::findOrFail($id);

        // Cek apakah sudah ada pembayaran pranota uang jalan
        if ($suratJalan->status_pembayaran_uang_jalan === 'dibayar') {
            return redirect()->route('surat-jalan.index')
                           ->with('error', 'Surat jalan tidak dapat diupdate karena pembayaran pranota uang jalan sudah dibuat.');
        }

        $request->validate([
            'tanggal_surat_jalan' => 'required|date',
            'no_surat_jalan' => 'required|string|max:255|unique:surat_jalans,no_surat_jalan,' . $id,
            'kegiatan' => 'required|string|max:255',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'jenis_barang_id' => 'nullable|exists:jenis_barangs,id',
            'tujuan_pengambilan_id' => 'nullable|exists:tujuan_kegiatan_utamas,id',
            'tujuan_pengiriman_id' => 'nullable|exists:master_tujuan_kirim,id',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'retur_barang' => 'nullable|string|max:255',
            'jumlah_retur' => 'nullable|integer|min:0',
            'supir' => 'nullable|string|max:255',
            'supir2' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'kenek' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:50',
            'nomor_kontainer' => 'nullable|string|max:255',
            'kontainer_id' => 'nullable|integer|min:1',
            'no_seal' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'jumlah_kontainer' => 'nullable|integer|min:1',
            'uang_jalan' => 'nullable|numeric|min:0',
            'aktifitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,belum masuk checkpoint,sudah_checkpoint,approved,fully_approved,rejected,completed,cancelled',
        ]);

        try {
            $data = $request->except(['gambar']);

            // Map nomor_kontainer to no_kontainer (database column name)
            if (isset($data['nomor_kontainer'])) {
                $data['no_kontainer'] = $data['nomor_kontainer'];
                unset($data['nomor_kontainer']);
            }

            // Convert IDs to text values for dropdown fields
            if (!empty($data['pengirim_id'])) {
                $pengirim = \App\Models\Pengirim::find($data['pengirim_id']);
                $data['pengirim'] = $pengirim ? $pengirim->nama_pengirim : null;
                unset($data['pengirim_id']);
            }

            if (!empty($data['jenis_barang_id'])) {
                $jenisBarang = \App\Models\JenisBarang::find($data['jenis_barang_id']);
                $data['jenis_barang'] = $jenisBarang ? $jenisBarang->nama_barang : null;
                unset($data['jenis_barang_id']);
            }

            if (!empty($data['tujuan_pengambilan_id'])) {
                $tujuanPengambilan = \App\Models\TujuanKegiatanUtama::find($data['tujuan_pengambilan_id']);
                $data['tujuan_pengambilan'] = $tujuanPengambilan ? $tujuanPengambilan->ke : null;
                unset($data['tujuan_pengambilan_id']);
            }

            if (!empty($data['tujuan_pengiriman_id'])) {
                $tujuanPengiriman = \App\Models\MasterTujuanKirim::find($data['tujuan_pengiriman_id']);
                $data['tujuan_pengiriman'] = $tujuanPengiriman ? $tujuanPengiriman->nama_tujuan : null;
                unset($data['tujuan_pengiriman_id']);
            }

            // Set default values for required fields if empty
            if (empty($data['status_pembayaran_uang_rit'])) {
                $data['status_pembayaran_uang_rit'] = 'belum_dibayar';
            }
            if (empty($data['status_pembayaran_uang_rit_kenek'])) {
                $data['status_pembayaran_uang_rit_kenek'] = 'belum_dibayar';
            }
            if (empty($data['status_pembayaran'])) {
                $data['status_pembayaran'] = 'belum_dibayar';
            }
            if (empty($data['status_pembayaran_uang_jalan'])) {
                $data['status_pembayaran_uang_jalan'] = 'belum_ada';
            }

            // Store old values for comparison
            $oldJumlahKontainer = $suratJalan->jumlah_kontainer;
            $oldOrderId = $suratJalan->order_id;

            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($suratJalan->gambar && Storage::disk('public')->exists($suratJalan->gambar)) {
                    Storage::disk('public')->delete($suratJalan->gambar);
                }

                $image = $request->file('gambar');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('surat-jalan', $filename, 'public');
                $data['gambar'] = $path;
            }

            $suratJalan->update($data);

            // Update tujuan pengiriman di order jika ada perubahan
            if ($suratJalan->order_id && isset($data['tujuan_pengiriman'])) {
                try {
                    $order = $suratJalan->order;
                    if ($order) {
                        $oldTujuanPengiriman = $order->tujuan_pengiriman;
                        $newTujuanPengiriman = $data['tujuan_pengiriman'];

                        // Update tujuan pengiriman di order jika berbeda
                        if ($oldTujuanPengiriman !== $newTujuanPengiriman) {
                            $order->update([
                                'tujuan_pengiriman' => $newTujuanPengiriman,
                                'updated_by' => Auth::id()
                            ]);

                            Log::info('Order tujuan pengiriman updated from surat jalan edit', [
                                'surat_jalan_id' => $suratJalan->id,
                                'order_id' => $order->id,
                                'old_tujuan_pengiriman' => $oldTujuanPengiriman,
                                'new_tujuan_pengiriman' => $newTujuanPengiriman,
                                'updated_by' => Auth::id()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error updating order tujuan pengiriman: ' . $e->getMessage(), [
                        'surat_jalan_id' => $suratJalan->id,
                        'order_id' => $suratJalan->order_id
                    ]);
                    // Don't fail the surat jalan update if order update fails
                }
            }

            // Handle order units processing - only if surat jalan is already approved
            $newJumlahKontainer = $suratJalan->jumlah_kontainer;
            $newOrderId = $suratJalan->order_id;

            if ($newOrderId && $oldJumlahKontainer != $newJumlahKontainer) {
                try {
                    $order = $suratJalan->order;
                    if ($order) {
                        // Check if this surat jalan is already approved
                        $isApproved = $suratJalan->approvals()->where('status', 'approved')->exists();
                        
                        if ($isApproved) {
                            // If approved, we need to adjust the processed units
                            $difference = $newJumlahKontainer - $oldJumlahKontainer;

                            if ($difference > 0) {
                                // Increased containers - process more units
                                $note = "Surat jalan diupdate: {$suratJalan->no_surat_jalan} - Tambah {$difference} kontainer (sudah approved)";
                                $order->processUnits($difference, $note);
                            } elseif ($difference < 0) {
                                // Decreased containers - reverse process units
                                $reverseDifference = abs($difference);
                                $order->sisa += $reverseDifference;

                                // Add to processing history
                                $history = $order->processing_history;
                                if (!is_array($history)) {
                                    $history = [];
                                }
                                $history[] = [
                                    'processed_count' => -$reverseDifference,
                                    'remaining' => $order->sisa,
                                    'note' => "Surat jalan diupdate: {$suratJalan->no_surat_jalan} - Kurangi {$reverseDifference} kontainer (sudah approved)",
                                    'processed_at' => now()->toISOString(),
                                    'processed_by' => Auth::id()
                                ];
                                $order->processing_history = $history;
                                $order->updateOutstandingStatus();
                                $order->save();
                            }

                            Log::info('Order units updated for approved surat jalan', [
                                'order_id' => $order->id,
                                'old_containers' => $oldJumlahKontainer,
                                'new_containers' => $newJumlahKontainer,
                                'difference' => $difference,
                                'remaining_sisa' => $order->sisa
                            ]);
                        } else {
                            // If not approved yet, no need to process units (they will be processed on approval)
                            Log::info('Surat jalan updated but not approved yet - no unit processing needed', [
                                'surat_jalan_id' => $suratJalan->id,
                                'order_id' => $order->id,
                                'old_containers' => $oldJumlahKontainer,
                                'new_containers' => $newJumlahKontainer
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the surat jalan update
                    Log::error('Error updating order units: ' . $e->getMessage(), [
                        'surat_jalan_id' => $suratJalan->id,
                        'order_id' => $newOrderId
                    ]);
                }
            }

            return redirect()->route('surat-jalan.index')
                           ->with('success', 'Surat jalan berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('Error updating surat jalan: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);

            // Store values before deletion for order processing
            $orderId = $suratJalan->order_id;
            $jumlahKontainer = $suratJalan->jumlah_kontainer;
            $noSuratJalan = $suratJalan->no_surat_jalan;

            // Delete associated image
            if ($suratJalan->gambar && Storage::disk('public')->exists($suratJalan->gambar)) {
                Storage::disk('public')->delete($suratJalan->gambar);
            }

            $suratJalan->delete();

            // Restore units to order only if the surat jalan was already approved
            if ($orderId && $jumlahKontainer) {
                try {
                    $order = Order::find($orderId);
                    if ($order) {
                        // Check if this surat jalan was approved before deletion
                        $wasApproved = \App\Models\SuratJalanApproval::where('surat_jalan_id', $id)
                            ->where('status', 'approved')
                            ->exists();
                        
                        if ($wasApproved) {
                            // If it was approved, restore units back to order
                            $order->sisa += $jumlahKontainer;

                            // Add to processing history
                            $history = $order->processing_history;
                            if (!is_array($history)) {
                                $history = [];
                            }
                            $history[] = [
                                'processed_count' => -$jumlahKontainer,
                                'remaining' => $order->sisa,
                                'note' => "Surat jalan dihapus: {$noSuratJalan} - Kembalikan {$jumlahKontainer} kontainer (sudah approved sebelumnya)",
                                'processed_at' => now()->toISOString(),
                                'processed_by' => Auth::id()
                            ];
                            $order->processing_history = $history;
                            $order->updateOutstandingStatus();
                            $order->save();

                            Log::info('Order units restored after approved surat jalan deletion', [
                                'order_id' => $order->id,
                                'restored_units' => $jumlahKontainer,
                                'remaining_sisa' => $order->sisa,
                                'deleted_surat_jalan' => $noSuratJalan
                            ]);
                        } else {
                            // If it was not approved, no need to restore units
                            Log::info('Surat jalan deleted but was not approved - no unit restoration needed', [
                                'order_id' => $order->id,
                                'surat_jalan' => $noSuratJalan,
                                'containers' => $jumlahKontainer
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the surat jalan deletion
                    Log::error('Error restoring order units after surat jalan deletion: ' . $e->getMessage(), [
                        'surat_jalan_id' => $id,
                        'order_id' => $orderId
                    ]);
                }
            }

            return redirect()->route('surat-jalan.index')
                           ->with('success', 'Surat jalan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting surat jalan: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor surat jalan otomatis
     */
    public function generateNomorSuratJalan()
    {
        $today = Carbon::today();
        $prefix = 'SJ/' . $today->format('Y/m');

        $lastNumber = SuratJalan::whereDate('tanggal_surat_jalan', $today)
                               ->where('no_surat_jalan', 'like', $prefix . '%')
                               ->count();

        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'no_surat_jalan' => $prefix . '/' . $nextNumber
        ]);
    }

    /**
     * Get uang jalan based on tujuan pengambilan and container size
     */
    public function getUangJalanByTujuan(Request $request)
    {
        $request->validate([
            'tujuan' => 'required|string',
            'size' => 'nullable|string'
        ]);

        try {
            $tujuan = $request->tujuan;
            $size = $request->size;

            // Find tujuan kegiatan utama by 'dari' or 'ke' field
            // Try exact match first, then partial match
            $tujuanKegiatan = TujuanKegiatanUtama::where(function($query) use ($tujuan) {
                                                    $query->where('dari', $tujuan)
                                                          ->orWhere('ke', $tujuan);
                                                })
                                                ->first();
            
            // If no exact match found, try partial match with shortest result prioritized
            if (!$tujuanKegiatan) {
                $tujuanKegiatan = TujuanKegiatanUtama::where(function($query) use ($tujuan) {
                                                        $query->where('dari', 'like', '%' . $tujuan . '%')
                                                              ->orWhere('ke', 'like', '%' . $tujuan . '%');
                                                    })
                                                    ->orderByRaw('LENGTH(COALESCE(ke, dari))')
                                                    ->first();
            }

            if ($tujuanKegiatan) {
                $uangJalan = 0;

                // Determine uang jalan based on container size
                if ($size == '20') {
                    $uangJalan = $tujuanKegiatan->uang_jalan_20ft ?? 0;
                } elseif ($size == '40' || $size == '45') {
                    $uangJalan = $tujuanKegiatan->uang_jalan_40ft ?? 0;
                } else {
                    // Default to 20ft if size not specified
                    $uangJalan = $tujuanKegiatan->uang_jalan_20ft ?? 0;
                }

                return response()->json([
                    'success' => true,
                    'uang_jalan' => number_format($uangJalan, 0, ',', '.'),
                    'message' => 'Uang jalan ditemukan'
                ]);
            }

            return response()->json([
                'success' => false,
                'uang_jalan' => '0',
                'message' => 'Tujuan tidak ditemukan dalam master data'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting uang jalan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'uang_jalan' => '0',
                'message' => 'Terjadi kesalahan saat mengambil data uang jalan'
            ], 500);
        }
    }

    /**
     * Print surat jalan
     */
    public function print(SuratJalan $suratJalan)
    {
        // Load all related data
        $suratJalan->load([
            'order.pengirim',
            'tujuanPengambilanRelation',
            'tujuanPengirimanRelation',
            'order.jenisBarang'
        ]);

        return view('surat-jalan.print', compact('suratJalan'));
    }

    /**
     * Download PDF surat jalan
     */
    public function downloadPdf(SuratJalan $suratJalan)
    {
        // Load all related data
        $suratJalan->load([
            'order.pengirim',
            'tujuanPengambilanRelation',
            'tujuanPengirimanRelation',
            'order.jenisBarang'
        ]);

        // Generate PDF using DOMPDF
        $pdf = PDF::loadView('surat-jalan.print', compact('suratJalan'))
                  ->setPaper('A4', 'portrait')
                  ->setOptions([
                      'dpi' => 150,
                      'defaultFont' => 'Arial',
                      'isRemoteEnabled' => false,
                      'isHtml5ParserEnabled' => true,
                      'isPhpEnabled' => false
                  ]);

        $filename = 'Surat_Jalan_' . $suratJalan->no_surat_jalan . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show form for creating surat jalan without order.
     */
    public function createWithoutOrder()
    {
        // Get karyawan supir data - hanya divisi supir
        $supirs = Karyawan::where('divisi', 'supir')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_panggilan')
                         ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);

        // Get karyawan kenek data - hanya divisi krani
        $keneks = Karyawan::where('divisi', 'krani')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap']);

        // Get kegiatan surat jalan from master kegiatan
        $kegiatanSuratJalan = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
                                                        ->where('status', 'Aktif')
                                                        ->orderBy('nama_kegiatan')
                                                        ->get(['id', 'nama_kegiatan']);

        // Get data untuk dropdown
        $pengirimOptions = \App\Models\Pengirim::where('status', 'active')
                                               ->orderBy('nama_pengirim')
                                               ->get(['id', 'nama_pengirim']);

        $jenisBarangOptions = \App\Models\JenisBarang::where('status', 'active')
                                                     ->orderBy('nama_barang')
                                                     ->get(['id', 'nama_barang']);

        // Get tujuan kirim untuk tujuan pengambilan dan pengiriman
        $tujuanKirimOptions = \App\Models\MasterTujuanKirim::where('status', 'active')
                                                           ->orderBy('nama_tujuan')
                                                           ->get(['id', 'nama_tujuan']);

        $tujuanOptions = \App\Models\TujuanKegiatanUtama::where('aktif', true)
                                                        ->orderBy('ke')
                                                        ->get(['id', 'ke', 'dari']);

        return view('surat-jalan.create-without-order', compact(
            'supirs', 
            'keneks', 
            'kegiatanSuratJalan',
            'pengirimOptions',
            'jenisBarangOptions',
            'tujuanKirimOptions',
            'tujuanOptions'
        ));
    }

    /**
     * Store surat jalan without order.
     */
    public function storeWithoutOrder(Request $request)
    {
        // Validation rules
        $request->validate([
            'no_surat_jalan' => 'required|string|max:255|unique:surat_jalans',
            'tanggal_surat_jalan' => 'required|date',
            'pengirim_id' => 'required|exists:pengirims,id',
            'alamat' => 'required|string',
            'jenis_barang_id' => 'required|exists:jenis_barangs,id',
            'tujuan_pengambilan_id' => 'required|exists:master_tujuan_kirim,id',
            'tujuan_pengiriman_id' => 'required|exists:master_tujuan_kirim,id',
            'supir' => 'required|string|max:255',
            'kenek' => 'nullable|string|max:255',
            'no_plat' => 'required|string|max:20',
            'kegiatan' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'nomor_kontainer' => 'nullable|string|max:255',
            'nomor_seal' => 'nullable|string|max:255',
        ]);

        try {
            // Get pengirim, jenis barang, dan tujuan names from database
            $pengirim = \App\Models\Pengirim::findOrFail($request->pengirim_id);
            $jenisBarang = \App\Models\JenisBarang::findOrFail($request->jenis_barang_id);
            $tujuanPengambilan = \App\Models\MasterTujuanKirim::findOrFail($request->tujuan_pengambilan_id);
            $tujuanPengiriman = \App\Models\MasterTujuanKirim::findOrFail($request->tujuan_pengiriman_id);

            // Create surat jalan without order
            $suratJalan = SuratJalan::create([
                'order_id' => null, // No order associated
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_surat_jalan' => $request->tanggal_surat_jalan,
                'pengirim' => $pengirim->nama_pengirim,
                'alamat' => $request->alamat,
                'jenis_barang' => $jenisBarang->nama_barang,
                'tujuan_pengambilan' => $tujuanPengambilan->nama_tujuan,
                'tujuan_pengiriman' => $tujuanPengiriman->nama_tujuan,
                'supir' => $request->supir,
                'kenek' => $request->kenek,
                'no_plat' => $request->no_plat,
                'kegiatan' => $request->kegiatan,
                'catatan' => $request->catatan,
                'nomor_kontainer' => $request->nomor_kontainer,
                'no_seal' => $request->nomor_seal,
                'status' => 'draft',
                'status_pembayaran' => 'belum_masuk_pranota',
                'created_by' => Auth::id(),
            ]);

            // If supir is a customer or explicit flag is set, create prospek for it
            $isSupirCustomer = $request->input('is_supir_customer') == '1' ||
                                (isset($request->supir) && stripos($request->supir, 'supir customer') !== false) ||
                                (isset($request->supir) && $request->supir === '__CUSTOMER__');

            if ($isSupirCustomer) {
                try {
                    $prospekData = [
                           'tanggal' => $suratJalan->tanggal_surat_jalan ?? now(),
                        'nama_supir' => $suratJalan->supir,
                        'barang' => $suratJalan->jenis_barang ?? null,
                        'pt_pengirim' => $suratJalan->pengirim ?? null,
                        'ukuran' => null,
                        'tipe' => $suratJalan->tipe_kontainer ?? null,
                        'no_surat_jalan' => $suratJalan->no_surat_jalan,
                        'surat_jalan_id' => $suratJalan->id,
                        'nomor_kontainer' => $suratJalan->nomor_kontainer ?? null,
                        'no_seal' => $suratJalan->no_seal ?? null,
                        'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman ?? null,
                        'nama_kapal' => null,
                        'keterangan' => 'Auto generated from Surat Jalan (Supir Customer): ' . ($suratJalan->no_surat_jalan ?? '-'),
                        'status' => Prospek::STATUS_AKTIF,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ];
                    $createdProspek = Prospek::create($prospekData);
                    Log::info('Prospek created from Supir Customer Surat Jalan (no order)', ['prospek_id' => $createdProspek->id, 'surat_jalan_id' => $suratJalan->id]);
                } catch (\Exception $e) {
                    Log::error('Error creating prospek for supir customer (WithoutOrder): ' . $e->getMessage(), ['surat_jalan_id' => $suratJalan->id]);
                }
            }

            return redirect()->route('surat-jalan.show', $suratJalan->id)
                           ->with('success', 'Surat jalan berhasil dibuat tanpa order.');

        } catch (\Exception $e) {
            Log::error('Error creating surat jalan without order: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan surat jalan. Silakan coba lagi.');
        }
    }

    /**
     * Update status of surat jalan
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);
            
            $request->validate([
                'status' => 'required|in:draft,active,completed,cancelled'
            ]);

            $suratJalan->status = $request->status;
            $suratJalan->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating surat jalan status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }

    /**
     * Print memo for surat jalan
     */
    public function printMemo($id)
    {
        try {
            $suratJalan = SuratJalan::with(['order', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation'])->findOrFail($id);
            
            return view('surat-jalan.print-memo', compact('suratJalan'));
            
        } catch (\Exception $e) {
            Log::error('Error printing memo for surat jalan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menampilkan halaman memo');
        }
    }

    /**
     * Print preprinted surat jalan
     */
    public function printPreprinted($id)
    {
        try {
            $suratJalan = SuratJalan::with(['order', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation'])->findOrFail($id);
            
            $pdf = PDF::loadView('surat-jalan.print-preprinted', compact('suratJalan'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->stream('surat-jalan-preprinted-' . $suratJalan->no_surat_jalan . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Error printing preprinted surat jalan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak surat jalan preprinted');
        }
    }
}

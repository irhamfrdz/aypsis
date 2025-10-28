<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\User;
use App\Models\Order;
use App\Models\Karyawan;
use App\Models\TujuanKegiatanUtama;
use App\Models\Permohonan;
use App\Models\MasterKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by status pembayaran
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'all') {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $suratJalans = $query->with('order')
                            ->orderBy('tanggal_surat_jalan', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        return view('surat-jalan.index', compact('suratJalans'));
    }

    /**
     * Show order selection page before creating surat jalan.
     */
    public function selectOrder(Request $request)
    {
        $query = Order::with(['pengirim', 'jenisBarang', 'tujuanAmbil'])
                     ->whereIn('status', ['active', 'confirmed', 'processing']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                  ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_ambil', 'like', "%{$search}%")
                  ->orWhereHas('pengirim', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jenisBarang', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('tanggal_order', 'desc')
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

            if (!$selectedOrder || !in_array($selectedOrder->status, ['active', 'confirmed', 'processing'])) {
                return redirect()->route('surat-jalan.select-order')
                                ->with('error', 'Order tidak valid atau tidak tersedia untuk membuat surat jalan.');
            }
        } else {
            // Jika tidak ada order yang dipilih, redirect ke halaman select order
            return redirect()->route('surat-jalan.select-order')
                            ->with('info', 'Silakan pilih order terlebih dahulu untuk membuat surat jalan.');
        }

        // Get karyawan supir data - hanya divisi supir
        $supirs = Karyawan::where('divisi', 'supir')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap', 'plat']);

        // Get karyawan kenek data - hanya divisi krani
        $keneks = Karyawan::where('divisi', 'krani')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap']);

        // Get kegiatan surat jalan from master kegiatan
        $kegiatanSuratJalan = \App\Models\MasterKegiatan::where('type', 'kegiatan surat jalan')
                                                        ->where('status', 'aktif')
                                                        ->orderBy('nama_kegiatan')
                                                        ->get(['id', 'nama_kegiatan']);

        return view('surat-jalan.create', compact('selectedOrder', 'supirs', 'keneks', 'kegiatanSuratJalan'));
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
            'no_pemesanan' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        Log::info('Validation passed successfully');

        try {
            Log::info('Starting surat jalan creation process');
            Log::info('Request data:', $request->all());

            $data = $request->except(['gambar']);
            $data['input_by'] = Auth::id();
            $data['input_date'] = now();
            $data['status'] = 'belum masuk checkpoint'; // Set default status to belum masuk checkpoint

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
                'supir_from_request' => $request->input('supir')
            ]);

            // Update status surat jalan to indicate it needs checkpoint
            $suratJalan->update(['status' => 'belum masuk checkpoint']);

            // Process units on related order if order_id exists and jumlah_kontainer is set
            if ($suratJalan->order_id && $suratJalan->jumlah_kontainer) {
                try {
                    $order = $suratJalan->order;
                    if ($order) {
                        $processedUnits = (int) $suratJalan->jumlah_kontainer;
                        $note = "Surat jalan dibuat: {$suratJalan->no_surat_jalan} dengan {$processedUnits} kontainer";

                        // Process units on the order
                        $order->processUnits($processedUnits, $note);

                        Log::info('Order units processed', [
                            'order_id' => $order->id,
                            'processed_units' => $processedUnits,
                            'remaining_sisa' => $order->sisa
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the surat jalan creation
                    Log::error('Error processing order units: ' . $e->getMessage(), [
                        'surat_jalan_id' => $suratJalan->id,
                        'order_id' => $suratJalan->order_id
                    ]);
                }
            }

            // Redirect to surat jalan index page
            return redirect()->route('surat-jalan.index')
                           ->with('success', 'Surat jalan berhasil dibuat dengan nomor: ' . $suratJalan->no_surat_jalan);

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

        // Get karyawan supir data - hanya divisi supir
        $supirs = Karyawan::where('divisi', 'supir')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap', 'plat']);

        // Get karyawan kenek data - hanya divisi krani
        $keneks = Karyawan::where('divisi', 'krani')
                         ->whereNotNull('nama_lengkap')
                         ->orderBy('nama_lengkap')
                         ->get(['id', 'nama_lengkap']);

        return view('surat-jalan.edit', compact('suratJalan', 'supirs', 'keneks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $suratJalan = SuratJalan::findOrFail($id);

        $request->validate([
            'tanggal_surat_jalan' => 'required|date',
            'no_surat_jalan' => 'required|string|max:255|unique:surat_jalans,no_surat_jalan,' . $id,
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
            'aktifitas' => 'nullable|string',
            'rit' => 'nullable|integer|min:0',
            'uang_jalan' => 'nullable|numeric|min:0',
            'no_pemesanan' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        try {
            $data = $request->except(['gambar']);

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

            // Handle order units processing if order_id and jumlah_kontainer changed
            $newJumlahKontainer = $suratJalan->jumlah_kontainer;
            $newOrderId = $suratJalan->order_id;

            if ($newOrderId && $oldJumlahKontainer != $newJumlahKontainer) {
                try {
                    $order = $suratJalan->order;
                    if ($order) {
                        $difference = $newJumlahKontainer - $oldJumlahKontainer;

                        if ($difference > 0) {
                            // Increased containers - process more units
                            $note = "Surat jalan diupdate: {$suratJalan->no_surat_jalan} - Tambah {$difference} kontainer";
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
                                'note' => "Surat jalan diupdate: {$suratJalan->no_surat_jalan} - Kurangi {$reverseDifference} kontainer",
                                'processed_at' => now()->toISOString(),
                                'processed_by' => Auth::id()
                            ];
                            $order->processing_history = $history;
                            $order->updateOutstandingStatus();
                            $order->save();
                        }

                        Log::info('Order units updated', [
                            'order_id' => $order->id,
                            'old_containers' => $oldJumlahKontainer,
                            'new_containers' => $newJumlahKontainer,
                            'difference' => $difference,
                            'remaining_sisa' => $order->sisa
                        ]);
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

            // Restore units to order if applicable
            if ($orderId && $jumlahKontainer) {
                try {
                    $order = Order::find($orderId);
                    if ($order) {
                        // Restore units back to order
                        $order->sisa += $jumlahKontainer;

                        // Add to processing history
                        $history = $order->processing_history;
                        if (!is_array($history)) {
                            $history = [];
                        }
                        $history[] = [
                            'processed_count' => -$jumlahKontainer,
                            'remaining' => $order->sisa,
                            'note' => "Surat jalan dihapus: {$noSuratJalan} - Kembalikan {$jumlahKontainer} kontainer",
                            'processed_at' => now()->toISOString(),
                            'processed_by' => Auth::id()
                        ];
                        $order->processing_history = $history;
                        $order->updateOutstandingStatus();
                        $order->save();

                        Log::info('Order units restored after surat jalan deletion', [
                            'order_id' => $order->id,
                            'restored_units' => $jumlahKontainer,
                            'remaining_sisa' => $order->sisa,
                            'deleted_surat_jalan' => $noSuratJalan
                        ]);
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
            $tujuanKegiatan = TujuanKegiatanUtama::where(function($query) use ($tujuan) {
                                                    $query->where('dari', 'like', '%' . $tujuan . '%')
                                                          ->orWhere('ke', 'like', '%' . $tujuan . '%');
                                                })
                                                ->first();

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
}

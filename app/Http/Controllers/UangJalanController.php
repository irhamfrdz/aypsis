<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\UangJalan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UangJalanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UangJalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:uang-jalan-view')->only(['index', 'show']);
        $this->middleware('permission:uang-jalan-create')->only(['create', 'store', 'selectSuratJalanAdjustment', 'selectUangJalanAdjustment', 'createAdjustment', 'storeAdjustment']);
        $this->middleware('permission:uang-jalan-update')->only(['edit', 'update']);
        $this->middleware('permission:uang-jalan-delete')->only(['destroy']);
        // Allow export only to users with export permission
        $this->middleware('permission:uang-jalan-export')->only(['exportExcel']);
    }

    /**
     * Display a listing of uang jalan records.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');
        
        // Query uang jalan dengan relasi
        $query = UangJalan::with(['suratJalan.order.pengirim', 'createdBy']);
        
        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhereHas('suratJalan', function ($suratJalanQuery) use ($search) {
                      $suratJalanQuery->where('no_surat_jalan', 'like', "%{$search}%")
                                      ->orWhere('supir', 'like', "%{$search}%")
                                      ->orWhere('no_plat', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suratJalan.order', function ($orderQuery) use ($search) {
                      $orderQuery->where('nomor_order', 'like', "%{$search}%")
                                 ->orWhereHas('pengirim', function ($pengirimQuery) use ($search) {
                                     $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                                 });
                  });
            });
        }

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan tanggal uang jalan
        if ($tanggal_dari) {
            $query->whereDate('tanggal_uang_jalan', '>=', $tanggal_dari);
        }
        
        if ($tanggal_sampai) {
            $query->whereDate('tanggal_uang_jalan', '<=', $tanggal_sampai);
        }

        // Urutkan berdasarkan tanggal terbaru
        $uangJalans = $query->orderBy('created_at', 'desc')->paginate(15);

        $statusOptions = [
            'all' => 'Semua Status',
            'belum_masuk_pranota' => 'Belum Masuk Pranota',
            'sudah_masuk_pranota' => 'Sudah Masuk Pranota',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Dibatalkan'
        ];

        return view('uang-jalan.index', compact('uangJalans', 'search', 'status', 'statusOptions', 'tanggal_dari', 'tanggal_sampai'));
    }

    /**
     * Display a listing of surat jalan untuk dipilih sebagai basis uang jalan.
     */
    public function selectSuratJalan(Request $request)
    {
        // Determine if this select page is for penyesuaian (penambahan/pengurangan uang jalan)
        $isPenyesuaian = $request->get('penyesuaian') || \Route::currentRouteName() === 'uang-jalan.select-surat-jalan-penyesuaian';
        $search = $request->get('search');
        $status = $request->get('status', 'all'); // Default filter
        
        // Query surat jalan biasa dengan filter
        $querySuratJalan = SuratJalan::with(['order.pengirim', 'order.jenisBarang'])
            ->whereNotNull('order_id') // Hanya surat jalan yang ada ordernya
            ->where('status_pembayaran_uang_jalan', 'belum_ada') // Hanya yang belum ada uang jalan
            ->where(function($q) {
                // Exclude surat jalan yang merupakan 'supir customer'
                $q->whereNull('is_supir_customer')
                  ->orWhere('is_supir_customer', false)
                  ->orWhere('is_supir_customer', 0);
            });
        
        // Query surat jalan bongkaran dengan filter
        $querySuratJalanBongkaran = \App\Models\SuratJalanBongkaran::query();
        
        // Exclude surat jalan bongkaran yang sudah memiliki uang jalan
        $existingUangJalanBongkaranIds = \App\Models\UangJalanBongkaran::pluck('surat_jalan_bongkaran_id')->toArray();
        $querySuratJalanBongkaran->whereNotIn('id', $existingUangJalanBongkaranIds);
        
        // Filter berdasarkan status untuk surat jalan biasa
        if ($status && $status !== 'all') {
            $querySuratJalan->where('status', $status);
        }
        
        // Filter berdasarkan pencarian untuk surat jalan biasa
        if ($search) {
            $querySuratJalan->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('kenek', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('nomor_order', 'like', "%{$search}%")
                                 ->orWhereHas('pengirim', function ($pengirimQuery) use ($search) {
                                     $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                                 });
                  });
            });
            
            // Filter berdasarkan pencarian untuk surat jalan bongkaran
            $querySuratJalanBongkaran->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('kenek', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }
        
        // Ambil data surat jalan biasa
        $suratJalansBiasa = $querySuratJalan->orderBy('tanggal_surat_jalan', 'desc')
                                           ->orderBy('created_at', 'desc')
                                           ->get();
        
        // Ambil data surat jalan bongkaran
        $suratJalansBongkaran = $querySuratJalanBongkaran->orderBy('tanggal_surat_jalan', 'desc')
                                                         ->orderBy('created_at', 'desc')
                                                         ->get();
        
        // Tambahkan flag untuk membedakan jenis surat jalan
        $suratJalansBiasa->each(function($item) {
            $item->jenis_surat_jalan = 'biasa';
        });
        
        $suratJalansBongkaran->each(function($item) {
            $item->jenis_surat_jalan = 'bongkaran';
            // Normalisasi field untuk konsistensi di frontend
            $item->no_surat_jalan = $item->nomor_surat_jalan;
            $item->no_kontainer = $item->no_kontainer ?? $item->nomor_kontainer;
        });
        
        // Gabungkan kedua collection dan urutkan berdasarkan tanggal
        $allSuratJalans = $suratJalansBiasa->concat($suratJalansBongkaran)
                                          ->sortByDesc(function($item) {
                                              return $item->tanggal_surat_jalan ?? $item->created_at;
                                          });
        
        // Manual pagination
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $allSuratJalans->slice($offset, $perPage)->values();
        $total = $allSuratJalans->count();
        
        $suratJalans = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        $suratJalans->appends($request->query());
        
        // Status options untuk filter
        $statusOptions = [
            'all' => 'Semua Status',
            'belum_masuk_checkpoint' => 'Belum Masuk Checkpoint',
            'sudah_masuk_checkpoint' => 'Sudah Masuk Checkpoint',
            'sudah_berangkat' => 'Sudah Berangkat',
            'approved' => 'Approved'
        ];
        
        return view('uang-jalan.select-surat-jalan', compact('suratJalans', 'search', 'status', 'statusOptions', 'isPenyesuaian'));
    }

    /**
     * Show the form for creating a new uang jalan berdasarkan surat jalan yang dipilih.
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->get('surat_jalan_id');
        $jenisSuratJalan = $request->get('jenis_surat_jalan', 'biasa');
        
        if (!$suratJalanId) {
            return redirect()->route('uang-jalan.select-surat-jalan')
                           ->with('error', 'Silakan pilih surat jalan terlebih dahulu.');
        }
        
        if ($jenisSuratJalan === 'bongkaran') {
            // Ambil data surat jalan bongkaran
            $suratJalan = \App\Models\SuratJalanBongkaran::findOrFail($suratJalanId);
            
            // Cek apakah sudah ada uang jalan untuk surat jalan bongkaran ini
            $existingUangJalan = \App\Models\UangJalanBongkaran::where('surat_jalan_bongkaran_id', $suratJalanId)->first();
            
            if ($existingUangJalan) {
                return redirect()->route('uang-jalan.select-surat-jalan')
                               ->with('error', 'Uang jalan untuk surat jalan bongkaran ini sudah dibuat.');
            }
            
            // Normalisasi field untuk konsistensi dengan surat jalan biasa
            $suratJalan->no_surat_jalan = $suratJalan->nomor_surat_jalan;
            $suratJalan->no_kontainer = $suratJalan->no_kontainer ?? $suratJalan->nomor_kontainer;
        } else {
            // Ambil data surat jalan dengan relasi yang diperlukan
            $suratJalan = SuratJalan::with(['order.pengirim', 'order.jenisBarang'])
                                    ->findOrFail($suratJalanId);
            
            // Cek apakah surat jalan ini adalah supir customer - jika ya, tidak boleh dibuat uang jalan
            if (!empty($suratJalan->is_supir_customer) && $suratJalan->is_supir_customer) {
                return redirect()->route('uang-jalan.select-surat-jalan')
                               ->with('error', 'Uang jalan tidak dapat dibuat untuk Surat Jalan dengan Supir Customer.');
            }

            // Cek apakah sudah ada uang jalan untuk surat jalan ini
            $existingUangJalan = UangJalan::where('surat_jalan_id', $suratJalanId)->first();
            
            if ($existingUangJalan) {
                return redirect()->route('uang-jalan.select-surat-jalan')
                               ->with('error', 'Uang jalan untuk surat jalan ini sudah dibuat.');
            }
        }
        
        // Generate nomor uang jalan untuk preview
        $nomorUangJalan = UangJalan::generateNomorUangJalan();
        
        return view('uang-jalan.create', compact('suratJalan', 'nomorUangJalan', 'jenisSuratJalan'));
    }

    /**
     * Store a newly created uang jalan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            'nomor_uang_jalan' => 'nullable|string|max:50|unique:uang_jalans,nomor_uang_jalan',
            'tanggal_uang_jalan' => 'required|date',
            'kegiatan_bongkar_muat' => 'required|in:bongkar,muat',
            'jumlah_uang_jalan' => 'required|numeric|min:0',
            'jumlah_mel' => 'nullable|numeric|min:0',
            'jumlah_pelancar' => 'nullable|numeric|min:0',
            'jumlah_kawalan' => 'nullable|numeric|min:0',
            'jumlah_parkir' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string|max:255',
            'jumlah_penyesuaian' => 'nullable|numeric',
            'jumlah_total' => 'required|numeric|min:0',
            'memo' => 'nullable|string|max:1000',
            'jumlah_uang_supir' => 'nullable|numeric|min:0',
            'jumlah_uang_kenek' => 'nullable|numeric|min:0',
            'total_uang_jalan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);
        
        // Cek apakah sudah ada uang jalan untuk surat jalan ini
        $existingUangJalan = UangJalan::where('surat_jalan_id', $request->surat_jalan_id)->first();
        
        if ($existingUangJalan) {
            return redirect()->back()
                           ->with('error', 'Uang jalan untuk surat jalan ini sudah dibuat.')
                           ->withInput();
        }
        
        // Double-check: ensure surat jalan is not supir customer
        $sj = SuratJalan::find($request->surat_jalan_id);
        if ($sj && $sj->is_supir_customer) {
            return redirect()->back()
                           ->with('error', 'Uang jalan tidak dapat dibuat untuk Surat Jalan yang menggunakan Supir Customer.')
                           ->withInput();
        }

        try {
            // Generate nomor uang jalan otomatis jika tidak diisi
            $nomorUangJalan = $request->nomor_uang_jalan ?: UangJalan::generateNomorUangJalan();
            
            // Buat record uang jalan baru
            $uangJalan = UangJalan::create([
                'nomor_uang_jalan' => $nomorUangJalan,
                'tanggal_uang_jalan' => $request->tanggal_uang_jalan,
                'surat_jalan_id' => $request->surat_jalan_id,
                'kegiatan_bongkar_muat' => $request->kegiatan_bongkar_muat,
                'jumlah_uang_jalan' => $request->jumlah_uang_jalan,
                'jumlah_mel' => $request->jumlah_mel ?? 0,
                'jumlah_pelancar' => $request->jumlah_pelancar ?? 0,
                'jumlah_kawalan' => $request->jumlah_kawalan ?? 0,
                'jumlah_parkir' => $request->jumlah_parkir ?? 0,
                'subtotal' => $request->subtotal,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'jumlah_penyesuaian' => $request->jumlah_penyesuaian ?? 0,
                'jumlah_total' => $request->jumlah_total,
                'memo' => $request->memo,
                'jumlah_uang_supir' => $request->jumlah_uang_supir ?? 0,
                'jumlah_uang_kenek' => $request->jumlah_uang_kenek ?? 0,
                'total_uang_jalan' => $request->total_uang_jalan ?? $request->jumlah_total,
                'keterangan' => $request->keterangan ?? $request->memo,
                'status' => 'belum_masuk_pranota',
                'created_by' => Auth::id()
            ]);
            
            // Update status pembayaran uang jalan di surat jalan
            $suratJalan = SuratJalan::find($request->surat_jalan_id);
            $suratJalan->update([
                'status_pembayaran_uang_jalan' => 'sudah_masuk_uang_jalan'
            ]);
            
            return redirect()->route('uang-jalan.index')
                           ->with('success', 'Uang jalan berhasil dibuat untuk surat jalan ' . $suratJalan->no_surat_jalan);
            
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data uang jalan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified uang jalan.
     */
    public function show($id)
    {
        $uangJalan = UangJalan::with(['suratJalan.order.pengirim', 'createdBy'])
                             ->findOrFail($id);
        
        return view('uang-jalan.show', compact('uangJalan'));
    }

    /**
     * Remove the specified uang jalan from storage.
     */
    public function destroy($id)
    {
        try {
            $uangJalan = UangJalan::findOrFail($id);
            
            // Check if uang jalan can be deleted (only if status allows it)
            if (!in_array($uangJalan->status, ['belum_dibayar', 'belum_masuk_pranota'])) {
                return redirect()
                    ->route('uang-jalan.index')
                    ->with('error', 'Uang jalan dengan status ' . $uangJalan->status . ' tidak dapat dihapus.');
            }
            
            // Check if uang jalan is already in pranota
            if ($uangJalan->pranotaUangJalan()->exists()) {
                return redirect()
                    ->route('uang-jalan.index')
                    ->with('error', 'Uang jalan sudah masuk dalam pranota dan tidak dapat dihapus.');
            }
            
            // Store info for success message
            $identifier = $uangJalan->nomor_uang_jalan ?? $uangJalan->suratJalan->no_surat_jalan ?? 'ID: ' . $uangJalan->id;
            
            // Delete the uang jalan
            $uangJalan->delete();
            
            Log::info('Uang jalan deleted successfully', [
                'uang_jalan_id' => $id,
                'identifier' => $identifier,
                'deleted_by' => Auth::id()
            ]);
            
            return redirect()
                ->route('uang-jalan.index')
                ->with('success', 'Uang jalan "' . $identifier . '" berhasil dihapus.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting uang jalan', [
                'uang_jalan_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()
                ->route('uang-jalan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus uang jalan: ' . $e->getMessage());
        }
    }

    /**
     * Export Uang Jalan listing to Excel with current filters
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'tanggal_dari', 'tanggal_sampai']);
            $fileName = 'uang_jalan_export_' . date('Ymd_His') . '.xlsx';

            $export = new UangJalanExport($filters, []);
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            \Log::error('Error exporting uang jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal export uang jalan: ' . $e->getMessage());
        }
    }

    /**
     * Get first available uang jalan for adjustment from selected surat jalan
     */
    public function getFirstUangJalanForAdjustment(Request $request)
    {
        $suratJalanId = $request->get('surat_jalan_id');

        if (!$suratJalanId) {
            return response()->json(['success' => false, 'message' => 'Surat jalan ID diperlukan']);
        }

        $suratJalan = SuratJalan::find($suratJalanId);

        if (!$suratJalan) {
            return response()->json(['success' => false, 'message' => 'Surat jalan tidak ditemukan']);
        }

        // Ambil uang jalan pertama yang bisa di-adjust
        $uangJalan = $suratJalan->uangJalan()
            ->whereIn('status', ['belum_masuk_pranota', 'sudah_masuk_pranota'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($uangJalan) {
            return response()->json([
                'success' => true,
                'uang_jalan_id' => $uangJalan->id,
                'nomor_uang_jalan' => $uangJalan->nomor_uang_jalan
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada uang jalan yang dapat disesuaikan'
            ]);
        }
    }

    /**
     * Display a listing of surat jalan untuk dipilih sebagai basis adjustment uang jalan.
     */
    public function selectSuratJalanAdjustment(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        // Query surat jalan yang sudah memiliki uang jalan (untuk adjustment)
        $query = SuratJalan::with(['order.pengirim', 'order.jenisBarang', 'uangJalan'])
            ->whereNotNull('order_id')
            ->where('status_pembayaran_uang_jalan', 'sudah_masuk_uang_jalan')
            ->whereHas('uangJalan', function($q) {
                $q->whereIn('status', ['belum_masuk_pranota', 'sudah_masuk_pranota']);
            });

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('kenek', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('nomor_order', 'like', "%{$search}%")
                                 ->orWhereHas('pengirim', function ($pengirimQuery) use ($search) {
                                     $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                                 });
                  })
                  ->orWhereHas('uangJalan', function ($uangJalanQuery) use ($search) {
                      $uangJalanQuery->where('nomor_uang_jalan', 'like', "%{$search}%");
                  });
            });
        }

        $suratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        $statusOptions = [
            'all' => 'Semua Status',
            'belum_masuk_checkpoint' => 'Belum Masuk Checkpoint',
            'sudah_masuk_checkpoint' => 'Sudah Masuk Checkpoint',
            'sudah_berangkat' => 'Sudah Berangkat',
            'approved' => 'Approved'
        ];

        return view('uang-jalan.adjustment.select-surat-jalan-adjustment', compact('suratJalans', 'search', 'status', 'statusOptions'));
    }

    /**
     * Display a listing of uang jalan untuk dipilih sebagai basis adjustment.
     */
    public function selectUangJalanAdjustment(Request $request)
    {
        $suratJalanId = $request->get('surat_jalan_id');

        if (!$suratJalanId) {
            return redirect()->route('uang-jalan.adjustment.select-surat-jalan')
                           ->with('error', 'Silakan pilih surat jalan terlebih dahulu.');
        }

        $suratJalan = SuratJalan::with(['order.pengirim', 'uangJalan'])->findOrFail($suratJalanId);

        // Ambil semua uang jalan untuk surat jalan ini yang bisa di-adjust
        $uangJalans = $suratJalan->uangJalan()
            ->whereIn('status', ['belum_masuk_pranota', 'sudah_masuk_pranota'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('uang-jalan.adjustment.select-uang-jalan-adjustment', compact('suratJalan', 'uangJalans'));
    }

    /**
     * Show the form for creating a new adjustment for uang jalan.
     */
    public function createAdjustment(Request $request)
    {
        $uangJalanId = $request->get('uang_jalan_id');

        if (!$uangJalanId) {
            return redirect()->route('uang-jalan.adjustment.select-surat-jalan')
                           ->with('error', 'Silakan pilih surat jalan terlebih dahulu.');
        }

        $uangJalan = UangJalan::with(['suratJalan.order.pengirim'])->findOrFail($uangJalanId);

        // Cek apakah uang jalan masih bisa di-adjust
        if (!in_array($uangJalan->status, ['belum_masuk_pranota', 'sudah_masuk_pranota'])) {
            return redirect()->route('uang-jalan.adjustment.select-surat-jalan')
                           ->with('error', 'Uang jalan dengan status ' . $uangJalan->status . ' tidak dapat di-adjust.');
        }

        return view('uang-jalan.adjustment.create-adjustment', compact('uangJalan'));
    }

    /**
     * Store a newly created adjustment for uang jalan in storage.
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'uang_jalan_id' => 'required|exists:uang_jalans,id',
            'jenis_penyesuaian' => 'required|in:penambahan,pengurangan,pengembalian_penuh,pengembalian_sebagian',
            'debit_kredit' => 'required|in:debit,kredit',
            'jumlah_penyesuaian' => 'required|numeric',
            'jumlah_mel' => 'nullable|numeric|min:0',
            'jumlah_pelancar' => 'nullable|numeric|min:0',
            'jumlah_kawalan' => 'nullable|numeric|min:0',
            'jumlah_parkir' => 'nullable|numeric|min:0',
            'alasan_penyesuaian' => 'required|string|max:500',
            'tanggal_penyesuaian' => 'required|date',
            'memo' => 'nullable|string|max:1000'
        ]);

        $uangJalan = UangJalan::findOrFail($request->uang_jalan_id);

        // Cek apakah uang jalan masih bisa di-adjust
        if (!in_array($uangJalan->status, ['belum_masuk_pranota', 'sudah_masuk_pranota'])) {
            return redirect()->back()
                           ->with('error', 'Uang jalan dengan status ' . $uangJalan->status . ' tidak dapat di-adjust.')
                           ->withInput();
        }

        try {
            // Hitung jumlah penyesuaian berdasarkan jenis
            $jumlahPenyesuaian = $request->jumlah_penyesuaian;

            // Jika jenis adalah pengurangan atau pengembalian, buat negatif
            if (in_array($request->jenis_penyesuaian, ['pengurangan', 'pengembalian_penuh', 'pengembalian_sebagian'])) {
                $jumlahPenyesuaian = -$request->jumlah_penyesuaian;
            }

            // Jika pengembalian penuh, set ke -total saat ini
            if ($request->jenis_penyesuaian === 'pengembalian_penuh') {
                $jumlahPenyesuaian = -$uangJalan->jumlah_total;
            }

            // Simpan adjustment sebagai record baru
            UangJalanAdjustment::create([
                'uang_jalan_id' => $request->uang_jalan_id,
                'tanggal_penyesuaian' => $request->tanggal_penyesuaian,
                'jenis_penyesuaian' => $request->jenis_penyesuaian,
                'debit_kredit' => $request->debit_kredit,
                'jumlah_penyesuaian' => $jumlahPenyesuaian,
                'jumlah_mel' => $request->jumlah_mel,
                'jumlah_pelancar' => $request->jumlah_pelancar,
                'jumlah_kawalan' => $request->jumlah_kawalan,
                'jumlah_parkir' => $request->jumlah_parkir,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'memo' => $request->memo,
                'created_by' => Auth::id()
            ]);

            Log::info('Uang jalan adjustment created successfully', [
                'uang_jalan_id' => $uangJalan->id,
                'jenis_penyesuaian' => $request->jenis_penyesuaian,
                'jumlah_penyesuaian' => $jumlahPenyesuaian,
                'adjusted_by' => Auth::id()
            ]);

            return redirect()->route('uang-jalan.index')
                           ->with('success', 'Penyesuaian uang jalan berhasil disimpan untuk ' . $uangJalan->nomor_uang_jalan);

        } catch (\Exception $e) {
            Log::error('Error creating uang jalan adjustment', [
                'uang_jalan_id' => $request->uang_jalan_id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menyimpan penyesuaian: ' . $e->getMessage())
                           ->withInput();
        }
    }
}
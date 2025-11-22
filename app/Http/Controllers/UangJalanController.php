<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\UangJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UangJalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:uang-jalan-view')->only(['index', 'show']);
        $this->middleware('permission:uang-jalan-create')->only(['create', 'store']);
        $this->middleware('permission:uang-jalan-update')->only(['edit', 'update']);
        $this->middleware('permission:uang-jalan-delete')->only(['destroy']);
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
                $q->where('keterangan', 'like', "%{$search}%")
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
            'belum_dibayar' => 'Belum Dibayar',
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
        $search = $request->get('search');
        $status = $request->get('status', 'all'); // Default filter
        
        // Query surat jalan dengan filter
        $query = SuratJalan::with(['order.pengirim', 'order.jenisBarang'])
            ->whereNotNull('order_id') // Hanya surat jalan yang ada ordernya
            ->where('status_pembayaran_uang_jalan', 'belum_ada'); // Hanya yang belum ada uang jalan
        
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
                  });
            });
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $suratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);
        
        // Status options untuk filter
        $statusOptions = [
            'all' => 'Semua Status',
            'belum_masuk_checkpoint' => 'Belum Masuk Checkpoint',
            'sudah_masuk_checkpoint' => 'Sudah Masuk Checkpoint',
            'sudah_berangkat' => 'Sudah Berangkat',
            'approved' => 'Approved'
        ];
        
        return view('uang-jalan.select-surat-jalan', compact('suratJalans', 'search', 'status', 'statusOptions'));
    }

    /**
     * Show the form for creating a new uang jalan berdasarkan surat jalan yang dipilih.
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->get('surat_jalan_id');
        
        if (!$suratJalanId) {
            return redirect()->route('uang-jalan.select-surat-jalan')
                           ->with('error', 'Silakan pilih surat jalan terlebih dahulu.');
        }
        
        // Ambil data surat jalan dengan relasi yang diperlukan
        $suratJalan = SuratJalan::with(['order.pengirim', 'order.jenisBarang'])
                                ->findOrFail($suratJalanId);
        
        // Cek apakah sudah ada uang jalan untuk surat jalan ini
        $existingUangJalan = UangJalan::where('surat_jalan_id', $suratJalanId)->first();
        
        if ($existingUangJalan) {
            return redirect()->route('uang-jalan.select-surat-jalan')
                           ->with('error', 'Uang jalan untuk surat jalan ini sudah dibuat.');
        }
        
        // Generate nomor uang jalan untuk preview
        $nomorUangJalan = UangJalan::generateNomorUangJalan();
        
        return view('uang-jalan.create', compact('suratJalan', 'nomorUangJalan'));
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
}
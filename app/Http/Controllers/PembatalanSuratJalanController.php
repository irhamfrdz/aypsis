<?php

namespace App\Http\Controllers;

use App\Models\PembatalanSuratJalan;
use App\Models\Coa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembatalanSuratJalanController extends Controller
{
    /**
     * Display a listing of Pembatalan.
     */
    public function index(Request $request)
    {
        $query = PembatalanSuratJalan::with(['suratJalan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('alasan_batal', 'like', "%{$search}%");
            });
        }

        $pembatalans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('pembatalan-surat-jalan.index', compact('pembatalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // List cancelable surat jalans
        $query = \App\Models\SuratJalan::with(['supirKaryawan', 'uangJalan'])
            ->where('status', '!=', 'cancelled');
        
        if ($request->filled('search_sj')) {
            $query->where('no_surat_jalan', 'like', "%{$request->search_sj}%");
        }

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Bank options for searchable dropdown (same source as payment forms)
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembatalan-surat-jalan.create', compact('suratJalans', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            'alasan_batal' => 'required|string',
            'nomor_pembayaran' => 'nullable|string|max:255',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'tanggal_pembayaran' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Fallback generation if client-side generator did not run
        if (empty($validated['nomor_pembayaran'])) {
            $now = now();
            $validated['nomor_pembayaran'] = sprintf(
                'PBL-%s-%s-%06d',
                $now->format('m'),
                $now->format('y'),
                random_int(1, 999999)
            );
        }

        $suratJalan = \App\Models\SuratJalan::findOrFail($validated['surat_jalan_id']);

        if ($suratJalan->status === 'cancelled') {
            return redirect()->back()->with('error', 'Surat Jalan sudah dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($validated, $suratJalan) {
            // Create Cancel Record
            PembatalanSuratJalan::create([
                'surat_jalan_id' => $suratJalan->id,
                'no_surat_jalan' => $suratJalan->no_surat_jalan,
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'bank' => $validated['bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'total_pembayaran' => $validated['total_pembayaran'],
                'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? 0,
                'total_tagihan_setelah_penyesuaian' => $validated['total_tagihan_setelah_penyesuaian'],
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'alasan_batal' => $validated['alasan_batal'],
                'status' => 'approved', // auto-approve to cancel immediately
                'created_by' => auth()->id(),
            ]);

            // Hapus data prospek yang terkait (by surat_jalan_id or no_surat_jalan)
            \App\Models\Prospek::where('surat_jalan_id', $suratJalan->id)
                ->orWhere('no_surat_jalan', $suratJalan->no_surat_jalan)
                ->delete();

            // Hapus data tanda terima yang terkait (by surat_jalan_id or no_surat_jalan)
            \App\Models\TandaTerima::where('surat_jalan_id', $suratJalan->id)
                ->orWhere('no_surat_jalan', $suratJalan->no_surat_jalan)
                ->delete();

            // Hapus surat jalan yang dibatalkan
            $suratJalan->delete();
        });

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Surat Jalan berhasil dibatalkan dan data terkait sudah dihapus.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $pembatalanSuratJalan->load('suratJalan');
        return view('pembatalan-surat-jalan.show', compact('pembatalanSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembatalan-surat-jalan.edit', compact('pembatalanSuratJalan', 'akunCoa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $validated = $request->validate([
            'alasan_batal' => 'required|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'tanggal_pembayaran' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Keep total_tagihan_setelah_penyesuaian in sync if not explicitly changed
        $totalPembayaran = (float) ($pembatalanSuratJalan->total_pembayaran ?? 0);
        $totalPenyesuaian = (float) ($validated['total_tagihan_penyesuaian'] ?? 0);

        $pembatalanSuratJalan->update([
            'nomor_accurate' => $validated['nomor_accurate'] ?? null,
            'tanggal_kas' => $validated['tanggal_kas'],
            'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
            'bank' => $validated['bank'],
            'jenis_transaksi' => $validated['jenis_transaksi'],
            'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? 0,
            'total_tagihan_setelah_penyesuaian' => $validated['total_tagihan_setelah_penyesuaian'] ?? ($totalPembayaran + $totalPenyesuaian),
            'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'alasan_batal' => $validated['alasan_batal'],
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Catatan pembatalan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        return redirect()->route('pembatalan-surat-jalan.index')->with('error', 'Penghapusan transaksional dibatasi.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PranotaBpjsHeader;
use App\Models\PranotaBpjsDetail;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PranotaBpjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pranota-bpjs-view')->only(['index', 'show']);
        $this->middleware('permission:pranota-bpjs-create')->only(['create', 'store']);
        $this->middleware('permission:pranota-bpjs-update')->only(['edit', 'update']);
        $this->middleware('permission:pranota-bpjs-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PranotaBpjsHeader::with(['createdBy']);

        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }

        $pranotas = $query->orderBy('tanggal_pranota', 'desc')->paginate(10);

        return view('pranota-bpjs.index', compact('pranotas'));
    }

    public function create()
    {
        // Get active Karyawan that might have BPJS
        $karyawans = Karyawan::whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'group_jkn', 'group_bp_jamsostek', 'dpp_jkn', 'dpp_bp_jamsostek']);
            
        $rumusBpjs = \App\Models\MasterRumusBpjs::all();
            
        return view('pranota-bpjs.create', compact('karyawans', 'rumusBpjs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000',
            'details' => 'nullable|array',
            'details.*.karyawan_id' => 'required|exists:karyawans,id',
            'details.*.bpjs_kesehatan' => 'nullable',
            'details.*.bpjs_ketenagakerjaan' => 'nullable',
            'details.*.jht_biaya' => 'nullable',
            'details.*.jht_hutang' => 'nullable',
            'details.*.jkk_tunjangan' => 'nullable',
            'details.*.jkm_tunjangan' => 'nullable',
            'details.*.jp_biaya' => 'nullable',
            'details.*.jp_hutang' => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $nomorPranota = $this->generateNomorPranota();

            $header = PranotaBpjsHeader::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_bulan' => $request->periode_bulan,
                'periode_tahun' => $request->periode_tahun,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            $totalKes = 0;
            $totalKet = 0;
            $totalKaryawan = 0;

            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    $bpjsKes = $this->parseIndonesianNumber($detail['bpjs_kesehatan'] ?? 0);
                    $bpjsKetInput = $this->parseIndonesianNumber($detail['bpjs_ketenagakerjaan'] ?? 0);
                    $jknTotal = $bpjsKes + $bpjsKetInput;

                    $jhtBiaya = $this->parseIndonesianNumber($detail['jht_biaya'] ?? 0);
                    $jhtHutang = $this->parseIndonesianNumber($detail['jht_hutang'] ?? 0);
                    $jkkTunjangan = $this->parseIndonesianNumber($detail['jkk_tunjangan'] ?? 0);
                    $jkmTunjangan = $this->parseIndonesianNumber($detail['jkm_tunjangan'] ?? 0);
                    $jpBiaya = $this->parseIndonesianNumber($detail['jp_biaya'] ?? 0);
                    $jpHutang = $this->parseIndonesianNumber($detail['jp_hutang'] ?? 0);
                    
                    $jamsostekTotal = $jhtBiaya + $jhtHutang + $jkkTunjangan + $jkmTunjangan + $jpBiaya + $jpHutang;
                    
                    $total = $jknTotal + $jamsostekTotal;

                    if ($total > 0 || $jknTotal > 0 || $jamsostekTotal > 0) {
                        PranotaBpjsDetail::create([
                            'pranota_bpjs_header_id' => $header->id,
                            'karyawan_id' => $detail['karyawan_id'],
                            'bpjs_kesehatan' => $bpjsKes,
                            'bpjs_ketenagakerjaan' => $bpjsKetInput,
                            'jht_biaya' => $jhtBiaya,
                            'jht_hutang' => $jhtHutang,
                            'jkk_tunjangan' => $jkkTunjangan,
                            'jkm_tunjangan' => $jkmTunjangan,
                            'jp_biaya' => $jpBiaya,
                            'jp_hutang' => $jpHutang,
                            'total' => $total,
                        ]);

                        $totalKes += $jknTotal;
                        $totalKet += $jamsostekTotal;
                        $totalKaryawan++;
                    }
                }
            }

            $header->update([
                'total_bpjs_kesehatan' => $totalKes,
                'total_bpjs_ketenagakerjaan' => $totalKet,
                'grand_total' => $totalKes + $totalKet,
                'total_karyawan' => $totalKaryawan,
            ]);

            DB::commit();
            return redirect()->route('pranota-bpjs.index')->with('success', 'Pranota BPJS berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Pranota BPJS: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(PranotaBpjsHeader $pranota_bpj)
    {
        $pranota_bpj->load('details.karyawan', 'createdBy');
        return view('pranota-bpjs.show', compact('pranota_bpj'));
    }

    public function edit(PranotaBpjsHeader $pranota_bpj)
    {
        if ($pranota_bpj->status != 'draft') {
            return redirect()->route('pranota-bpjs.index')->with('error', 'Hanya pranota berstatus Draft yang dapat diedit.');
        }

        $pranota_bpj->load('details');
        
        $karyawans = Karyawan::whereNull('tanggal_berhenti')
            ->orderBy('nama_lengkap')
            ->get();
            
        $rumusBpjs = \App\Models\MasterRumusBpjs::all();
            
        return view('pranota-bpjs.edit', compact('pranota_bpj', 'karyawans', 'rumusBpjs'));
    }

    public function update(Request $request, PranotaBpjsHeader $pranota_bpj)
    {
        if ($pranota_bpj->status != 'draft') {
            return redirect()->route('pranota-bpjs.index')->with('error', 'Hanya pranota berstatus Draft yang dapat diedit.');
        }

        $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000',
            'details' => 'nullable|array',
            'details.*.karyawan_id' => 'required|exists:karyawans,id',
            'details.*.bpjs_kesehatan' => 'nullable',
            'details.*.bpjs_ketenagakerjaan' => 'nullable',
            'details.*.jht_biaya' => 'nullable',
            'details.*.jht_hutang' => 'nullable',
            'details.*.jkk_tunjangan' => 'nullable',
            'details.*.jkm_tunjangan' => 'nullable',
            'details.*.jp_biaya' => 'nullable',
            'details.*.jp_hutang' => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pranota_bpj->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_bulan' => $request->periode_bulan,
                'periode_tahun' => $request->periode_tahun,
                'keterangan' => $request->keterangan,
                'updated_by' => auth()->id(),
            ]);

            // Hapus detail lama
            $pranota_bpj->details()->delete();

            $totalKes = 0;
            $totalKet = 0;
            $totalKaryawan = 0;

            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    $bpjsKes = $this->parseIndonesianNumber($detail['bpjs_kesehatan'] ?? 0);
                    $bpjsKetInput = $this->parseIndonesianNumber($detail['bpjs_ketenagakerjaan'] ?? 0);
                    $jknTotal = $bpjsKes + $bpjsKetInput;

                    $jhtBiaya = $this->parseIndonesianNumber($detail['jht_biaya'] ?? 0);
                    $jhtHutang = $this->parseIndonesianNumber($detail['jht_hutang'] ?? 0);
                    $jkkTunjangan = $this->parseIndonesianNumber($detail['jkk_tunjangan'] ?? 0);
                    $jkmTunjangan = $this->parseIndonesianNumber($detail['jkm_tunjangan'] ?? 0);
                    $jpBiaya = $this->parseIndonesianNumber($detail['jp_biaya'] ?? 0);
                    $jpHutang = $this->parseIndonesianNumber($detail['jp_hutang'] ?? 0);
                    
                    $jamsostekTotal = $jhtBiaya + $jhtHutang + $jkkTunjangan + $jkmTunjangan + $jpBiaya + $jpHutang;
                    
                    $total = $jknTotal + $jamsostekTotal;

                    if ($total > 0 || $jknTotal > 0 || $jamsostekTotal > 0) {
                        PranotaBpjsDetail::create([
                            'pranota_bpjs_header_id' => $pranota_bpj->id,
                            'karyawan_id' => $detail['karyawan_id'],
                            'bpjs_kesehatan' => $bpjsKes,
                            'bpjs_ketenagakerjaan' => $bpjsKetInput,
                            'jht_biaya' => $jhtBiaya,
                            'jht_hutang' => $jhtHutang,
                            'jkk_tunjangan' => $jkkTunjangan,
                            'jkm_tunjangan' => $jkmTunjangan,
                            'jp_biaya' => $jpBiaya,
                            'jp_hutang' => $jpHutang,
                            'total' => $total,
                        ]);

                        $totalKes += $jknTotal;
                        $totalKet += $jamsostekTotal;
                        $totalKaryawan++;
                    }
                }
            }

            $pranota_bpj->update([
                'total_bpjs_kesehatan' => $totalKes,
                'total_bpjs_ketenagakerjaan' => $totalKet,
                'grand_total' => $totalKes + $totalKet,
                'total_karyawan' => $totalKaryawan,
            ]);

            DB::commit();
            return redirect()->route('pranota-bpjs.index')->with('success', 'Pranota BPJS berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Pranota BPJS: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PranotaBpjsHeader $pranota_bpj)
    {
        if ($pranota_bpj->status != 'draft') {
            return redirect()->route('pranota-bpjs.index')->with('error', 'Hanya pranota berstatus Draft yang dapat dihapus.');
        }

        try {
            DB::beginTransaction();
            // Hapus permanen detail terlebih dahulu
            $pranota_bpj->details()->forceDelete();
            // Hapus permanen header dari database
            $pranota_bpj->forceDelete();
            DB::commit();
            return redirect()->route('pranota-bpjs.index')->with('success', 'Pranota BPJS berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Pranota BPJS: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Parse angka format Indonesia ke float.
     * Contoh: "5.729.880"  → 5729880.0
     *         "5.200.000,50" → 5200000.5
     *         5200000        → 5200000.0  (sudah number)
     */
    private function parseIndonesianNumber($value): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }
        // Hapus titik pemisah ribuan, ganti koma desimal → titik
        $cleaned = str_replace('.', '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return floatval($cleaned) ?: 0.0;
    }

    private function generateNomorPranota(): string
    {
        $prefix = 'PBPJS' . date('ym');

        // Ambil nomor terakhir berdasarkan prefix bulan ini
        $lastPranota = PranotaBpjsHeader::withTrashed()
            ->where('nomor_pranota', 'like', $prefix . '%')
            ->orderBy('nomor_pranota', 'desc')
            ->first();

        $lastNumber = $lastPranota
            ? (int) substr($lastPranota->nomor_pranota, -4)
            : 0;

        // Loop sampai ketemu nomor yang benar-benar belum ada
        do {
            $lastNumber++;
            $candidate = $prefix . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        } while (PranotaBpjsHeader::withTrashed()->where('nomor_pranota', $candidate)->exists());

        return $candidate;
    }
}

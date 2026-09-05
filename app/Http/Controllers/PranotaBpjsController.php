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
            'details.*.bpjs_kesehatan' => 'nullable|numeric',
            'details.*.bpjs_ketenagakerjaan' => 'nullable|numeric',
            'details.*.jht_biaya' => 'nullable|numeric',
            'details.*.jht_hutang' => 'nullable|numeric',
            'details.*.jkk_tunjangan' => 'nullable|numeric',
            'details.*.jkm_tunjangan' => 'nullable|numeric',
            'details.*.jp_biaya' => 'nullable|numeric',
            'details.*.jp_hutang' => 'nullable|numeric',
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
                    $bpjsKes = floatval($detail['bpjs_kesehatan'] ?? 0);
                    $bpjsKetInput = floatval($detail['bpjs_ketenagakerjaan'] ?? 0);
                    $jknTotal = $bpjsKes + $bpjsKetInput;

                    $jhtBiaya = floatval($detail['jht_biaya'] ?? 0);
                    $jhtHutang = floatval($detail['jht_hutang'] ?? 0);
                    $jkkTunjangan = floatval($detail['jkk_tunjangan'] ?? 0);
                    $jkmTunjangan = floatval($detail['jkm_tunjangan'] ?? 0);
                    $jpBiaya = floatval($detail['jp_biaya'] ?? 0);
                    $jpHutang = floatval($detail['jp_hutang'] ?? 0);
                    
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
            'details.*.bpjs_kesehatan' => 'nullable|numeric',
            'details.*.bpjs_ketenagakerjaan' => 'nullable|numeric',
            'details.*.jht_biaya' => 'nullable|numeric',
            'details.*.jht_hutang' => 'nullable|numeric',
            'details.*.jkk_tunjangan' => 'nullable|numeric',
            'details.*.jkm_tunjangan' => 'nullable|numeric',
            'details.*.jp_biaya' => 'nullable|numeric',
            'details.*.jp_hutang' => 'nullable|numeric',
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
                    $bpjsKes = floatval($detail['bpjs_kesehatan'] ?? 0);
                    $bpjsKetInput = floatval($detail['bpjs_ketenagakerjaan'] ?? 0);
                    $jknTotal = $bpjsKes + $bpjsKetInput;

                    $jhtBiaya = floatval($detail['jht_biaya'] ?? 0);
                    $jhtHutang = floatval($detail['jht_hutang'] ?? 0);
                    $jkkTunjangan = floatval($detail['jkk_tunjangan'] ?? 0);
                    $jkmTunjangan = floatval($detail['jkm_tunjangan'] ?? 0);
                    $jpBiaya = floatval($detail['jp_biaya'] ?? 0);
                    $jpHutang = floatval($detail['jp_hutang'] ?? 0);
                    
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
            $pranota_bpj->details()->delete();
            $pranota_bpj->delete();
            DB::commit();
            return redirect()->route('pranota-bpjs.index')->with('success', 'Pranota BPJS berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Pranota BPJS: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateNomorPranota()
    {
        $prefix = 'PBPJS' . date('ym');
        
        $lastPranota = PranotaBpjsHeader::where('nomor_pranota', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
            
        if ($lastPranota) {
            $lastNumber = (int) substr($lastPranota->nomor_pranota, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}

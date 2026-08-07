<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PranotaPumlController extends Controller
{
    public function index()
    {
        $pranotas = \App\Models\PranotaPuml::orderBy('created_at', 'desc')->get();
        return view('pranota-puml.index', compact('pranotas'));
    }

    public function create()
    {
        // Get all draft Uang Makan
        $draftUangMakan = \App\Models\PranotaUangMakan::where('status', 'draft')
                            ->whereNull('pranota_puml_id')
                            ->get();
                            
        // Get all draft Lembur
        $draftLembur = \App\Models\PranotaLemburKaryawanHeader::where('status', 'draft')
                            ->whereNull('pranota_puml_id')
                            ->get();

        return view('pranota-puml.create', compact('draftUangMakan', 'draftLembur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'uang_makan_ids' => 'nullable|array',
            'lembur_ids' => 'nullable|array',
        ]);

        if (empty($request->uang_makan_ids) && empty($request->lembur_ids)) {
            return back()->with('error', 'Minimal pilih satu Pranota Uang Makan atau Lembur untuk digabung.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Generate nomor PUML
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PUML')->first();
            if (! $nomorTerakhir) {
                $nomorTerakhir = \App\Models\NomorTerakhir::create(['modul' => 'PUML', 'nomor_terakhir' => 0]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomorPranota = "PUML1{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $totalUangMakan = 0;
            $totalLembur = 0;
            $periodeStart = null;
            $periodeEnd = null; // Bisa dihitung dari data tanggal_pranota anak jika diperlukan

            // Create header PUML
            $puml = \App\Models\PranotaPuml::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'status' => 'submitted',
                'created_by' => auth()->id(),
            ]);

            // Assign uang_makan_ids
            if (!empty($request->uang_makan_ids)) {
                $umRecords = \App\Models\PranotaUangMakan::whereIn('id', $request->uang_makan_ids)->get();
                foreach ($umRecords as $um) {
                    $totalUangMakan += $um->total_nominal;
                    $um->update(['pranota_puml_id' => $puml->id, 'status' => 'submitted']);
                }
            }

            // Assign lembur_ids
            if (!empty($request->lembur_ids)) {
                $lemburRecords = \App\Models\PranotaLemburKaryawanHeader::whereIn('id', $request->lembur_ids)->get();
                foreach ($lemburRecords as $lm) {
                    $totalLembur += $lm->total_setelah_adjustment;
                    $lm->update(['pranota_puml_id' => $puml->id, 'status' => 'submitted']);
                }
            }

            $puml->update([
                'total_uang_makan' => $totalUangMakan,
                'total_lembur' => $totalLembur,
                'grand_total' => $totalUangMakan + $totalLembur
            ]);

            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pranota-puml.index')->with('success', 'Pranota Gabungan (PUML) berhasil dibuat!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal membuat PUML: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $puml = \App\Models\PranotaPuml::with(['uangMakans.details.karyawan', 'lemburs.karyawans.karyawan'])->findOrFail($id);
        
        // Kita butuh merekap data berdasarkan karyawan_id
        $karyawanRekap = [];
        
        foreach ($puml->uangMakans as $um) {
            foreach ($um->details as $d) {
                $kid = $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $karyawanRekap[$kid] = [
                        'karyawan' => $d->karyawan,
                        'total_uang_makan' => 0,
                        'total_lembur' => 0
                    ];
                }
                $karyawanRekap[$kid]['total_uang_makan'] += $d->total_akhir;
            }
        }
        
        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $kid = $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $karyawanRekap[$kid] = [
                        'karyawan' => $d->karyawan,
                        'total_uang_makan' => 0,
                        'total_lembur' => 0
                    ];
                }
                $karyawanRekap[$kid]['total_lembur'] += $d->total_akhir;
            }
        }
        
        return view('pranota-puml.show', compact('puml', 'karyawanRekap'));
    }
}

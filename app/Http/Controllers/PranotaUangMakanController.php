<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaUangMakan;
use App\Models\PranotaUangMakanDetail;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\PranotaUangMakanAutoTransferExport;
use Maatwebsite\Excel\Facades\Excel;

class PranotaUangMakanController extends Controller
{
    public function index()
    {
        $pranotas = PranotaUangMakan::with('details')
            ->whereNull('pranota_puml_id')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pranota-uang-makan.index', compact('pranotas'));
    }

    public function show(Request $request, $id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);
        
        if ($request->has('print')) {
            return view('pranota-uang-makan.print', compact('pranota'));
        }
        
        return view('pranota-uang-makan.show', compact('pranota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pranota' => 'required|string|unique:pranota_uang_makans,nomor_pranota',
            'tanggal_pranota' => 'required|date',
            'karyawans' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $pranota = PranotaUangMakan::create([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_nominal' => 0, // Will calculate below
                'status' => 'draft',
            ]);

            $totalNominal = 0;

            foreach ($request->karyawans as $karyawanId => $data) {
                // Determine the total akhir based on inputs
                $nominalAwal = isset($data['nominal_awal']) ? (int) $data['nominal_awal'] : 0;
                $adjustment = isset($data['adjustment']) ? (int) $data['adjustment'] : 0;
                $totalAkhir = $nominalAwal + $adjustment;
                
                $pranota->details()->create([
                    'karyawan_id' => $karyawanId,
                    'kehadiran' => $data['kehadiran'] ?? null,
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adjustment,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $totalNominal += $totalAkhir;
            }

            $pranota->update(['total_nominal' => $totalNominal]);

            DB::commit();

            return redirect()->route('pranota-uang-makan.index')->with('success', 'Pranota Uang Makan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Pranota: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $pranota = PranotaUangMakan::findOrFail($id);
            $pranota->delete(); // Details will cascade
            return redirect()->route('pranota-uang-makan.index')->with('success', 'Pranota Uang Makan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Pranota: ' . $e->getMessage());
        }
    }

    public function exportAutoTransfer($id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);
        $filename = 'Auto_Transfer_Uang_Makan_' . str_replace('/', '_', $pranota->nomor_pranota) . '.xlsx';
        return Excel::download(new PranotaUangMakanAutoTransferExport($pranota), $filename);
    }
}

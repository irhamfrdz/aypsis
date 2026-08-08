<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PranotaLemburKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PranotaLemburKaryawanHeader::query()
            ->whereNull('pranota_puml_id');

        if ($request->filled('nomor_pranota')) {
            $query->where('nomor_pranota', 'like', '%' . $request->nomor_pranota . '%');
        }
        
        if ($request->filled('tanggal_pranota')) {
            $query->where('tanggal_pranota', $request->tanggal_pranota);
        }

        $pranotas = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('pranota-lembur-karyawan.index', compact('pranotas'));
    }

    public function show($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan'])
            ->findOrFail($id);
            
        return view('pranota-lembur-karyawan.show', compact('pranota'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'karyawans' => 'required|array',
            'karyawans.*.kehadiran' => 'required|string',
            'karyawans.*.nominal_awal' => 'required|numeric',
            'karyawans.*.adjustment' => 'nullable|numeric',
            'karyawans.*.catatan' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Generate nomor pranota
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PML')->first();
            if (! $nomorTerakhir) {
                $nomorTerakhir = \App\Models\NomorTerakhir::create([
                    'modul' => 'PML',
                    'nomor_terakhir' => 0,
                ]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomorCetakan = 1; // Default
            $nomorPranota = "PML{$nomorCetakan}{$bulan}{$tahun}".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $totalBiaya = 0;
            $totalAdjustment = 0;

            foreach ($validated['karyawans'] as $karyawanId => $data) {
                $nominalAwal = $data['nominal_awal'] ?? 0;
                $adj = $data['adjustment'] ?? 0;
                $totalBiaya += $nominalAwal;
                $totalAdjustment += $adj;
            }

            $totalSetelahAdjustment = $totalBiaya + $totalAdjustment;

            // Save parent
            $pranota = \App\Models\PranotaLemburKaryawanHeader::create([
                'nomor_pranota' => $nomorPranota,
                'nomor_cetakan' => $nomorCetakan,
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'total_biaya' => $totalBiaya,
                'adjustment' => $totalAdjustment,
                'total_setelah_adjustment' => $totalSetelahAdjustment,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Save details
            foreach ($validated['karyawans'] as $karyawanId => $data) {
                $nominalAwal = $data['nominal_awal'] ?? 0;
                $adj = $data['adjustment'] ?? 0;
                $totalAkhir = $nominalAwal + $adj;

                \App\Models\PranotaLemburKaryawan::create([
                    'pranota_lembur_karyawan_header_id' => $pranota->id,
                    'karyawan_id' => $karyawanId,
                    'jam_lembur' => $data['kehadiran'],
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adj,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);
            }

            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pranota Lembur berhasil disimpan dengan nomor: ' . $nomorPranota);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menyimpan Pranota Lembur: ' . $e->getMessage());
        }
    }
}

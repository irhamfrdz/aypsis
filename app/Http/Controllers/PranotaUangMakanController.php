<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaUangMakan;
use App\Models\PranotaUangMakanDetail;
use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
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

    public function edit($id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);

        $karyawanTetap = Karyawan::where('status', 'active')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nik', 'nama_lengkap', 'penempatan', 'cabang', 'posisi', 'nominal_uang_makan'])
            ->map(function ($k) {
                $k->unique_id = 'Karyawan_' . $k->id;
                $k->tipe_karyawan = 'App\\Models\\Karyawan';
                $k->tipe_label = 'Tetap';
                return $k;
            });

        $karyawanTidakTetap = KaryawanTidakTetap::orderBy('nama_lengkap')
            ->get(['id', 'nik', 'nama_lengkap', 'penempatan', 'cabang', 'pekerjaan'])
            ->map(function ($k) {
                $k->unique_id = 'KaryawanTidakTetap_' . $k->id;
                $k->tipe_karyawan = 'App\\Models\\KaryawanTidakTetap';
                $k->tipe_label = 'Tidak Tetap';
                $k->posisi = $k->pekerjaan ?? '-';
                $k->nominal_uang_makan = 0;
                return $k;
            });

        $allKaryawans = $karyawanTetap->concat($karyawanTidakTetap)->sortBy('nama_lengkap')->values();

        return view('pranota-uang-makan.edit', compact('pranota', 'allKaryawans'));
    }

    public function update(Request $request, $id)
    {
        $pranota = PranotaUangMakan::findOrFail($id);

        $request->validate([
            'nomor_pranota' => 'required|string|unique:pranota_uang_makans,nomor_pranota,' . $pranota->id,
            'tanggal_pranota' => 'required|date',
            'status' => 'nullable|string',
            'karyawans' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $pranota->update([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'status' => $request->status ?? $pranota->status ?? 'draft',
            ]);

            // Re-sync details
            $pranota->details()->delete();

            $totalNominal = 0;

            foreach ($request->karyawans as $karyawanKey => $data) {
                $tipeKaryawan = $data['tipe_karyawan'] ?? null;
                $karyawanId = $data['karyawan_id'] ?? null;

                if (!$tipeKaryawan || !$karyawanId) {
                    $parts = explode('_', $karyawanKey);
                    $tipeKaryawan = count($parts) > 1 ? 'App\\Models\\' . $parts[0] : 'App\\Models\\Karyawan';
                    $karyawanId = count($parts) > 1 ? $parts[1] : $karyawanKey;
                }

                $nominalAwal = isset($data['nominal_awal']) ? (int) str_replace(['.', ',', ' '], '', $data['nominal_awal']) : 0;
                $adjustment = isset($data['adjustment']) ? (int) str_replace(['.', ',', ' '], '', $data['adjustment']) : 0;
                $totalAkhir = $nominalAwal + $adjustment;

                $pranota->details()->create([
                    'tipe_karyawan' => $tipeKaryawan,
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

            return redirect()->route('pranota-uang-makan.index')->with('success', 'Pranota Uang Makan ' . $pranota->nomor_pranota . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Pranota: ' . $e->getMessage())->withInput();
        }
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

            foreach ($request->karyawans as $karyawanKey => $data) {
                // Parse key like "Karyawan_257" or "KaryawanTidakTetap_12"
                $parts = explode('_', $karyawanKey);
                $tipeKaryawan = count($parts) > 1 ? 'App\\Models\\' . $parts[0] : 'App\\Models\\Karyawan';
                $karyawanId = count($parts) > 1 ? $parts[1] : $karyawanKey;

                // Determine the total akhir based on inputs
                $nominalAwal = isset($data['nominal_awal']) ? (int) str_replace(['.', ',', ' '], '', $data['nominal_awal']) : 0;
                $adjustment = isset($data['adjustment']) ? (int) str_replace(['.', ',', ' '], '', $data['adjustment']) : 0;
                $totalAkhir = $nominalAwal + $adjustment;
                
                $pranota->details()->create([
                    'tipe_karyawan' => $tipeKaryawan,
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
            return redirect()->back()->with('success', 'Pranota Uang Makan berhasil dihapus!');
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

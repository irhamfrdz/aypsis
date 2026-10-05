<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PranotaLemburKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PranotaLemburKaryawanHeader::query()
            ->whereNull('pranota_puml_id');

        if ($request->filled('nomor_pranota')) {
            $query->where('nomor_pranota', 'like', '%'.$request->nomor_pranota.'%');
        }

        if ($request->filled('tanggal_pranota')) {
            $query->where('tanggal_pranota', $request->tanggal_pranota);
        }

        if ($request->filled('only_my') && $request->only_my == '1') {
            $query->where('created_by', auth()->id());
        }

        $pranotas = $query->with(['creator', 'karyawans'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pranota-lembur-karyawan.index', compact('pranotas'));
    }

    public function show($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan'])
            ->findOrFail($id);

        return view('pranota-lembur-karyawan.show', compact('pranota'));
    }

    public function edit($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan', 'pranotaPuml'])
            ->findOrFail($id);

        if ($pranota->pranota_puml_id && in_array(optional($pranota->pranotaPuml)->status, ['approved', 'paid'])) {
            return redirect()->route('pranota-lembur-karyawan.show', $id)
                ->with('error', 'Pranota tidak dapat diedit karena PUML induk sudah ' . $pranota->pranotaPuml->status . '.');
        }

        return view('pranota-lembur-karyawan.edit', compact('pranota'));
    }

    public function export($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['creator', 'karyawans.karyawan'])
            ->findOrFail($id);

        $safeNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pranota->nomor_pranota);
        $filename = 'Pranota_Lembur_'.$safeNomor.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PranotaLemburKaryawanExport($pranota), $filename);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date',
            'karyawans' => 'required|array',
            'karyawans.*.kehadiran' => 'required|string',
            'karyawans.*.nominal_awal' => 'required|numeric',
            'karyawans.*.adjustment' => 'nullable|numeric',
            'karyawans.*.nominal_lembur' => 'nullable|numeric',
            'karyawans.*.uang_makan_lembur' => 'nullable|numeric',
            'karyawans.*.nominal_per_hari' => 'nullable|numeric',
            'karyawans.*.catatan' => 'nullable|string',
            'karyawans.*.tanggal_lembur' => 'nullable',
        ]);

        $periodeMulai = $validated['periode_mulai'] ?? null;
        $periodeSelesai = $validated['periode_selesai'] ?? null;

        // Fallback periode dari tanggal_pranota jika kosong
        if (! $periodeMulai || ! $periodeSelesai) {
            $parsedDate = \Carbon\Carbon::parse($validated['tanggal_pranota']);
            $periodeMulai = $periodeMulai ?: $parsedDate->copy()->startOfMonth()->toDateString();
            $periodeSelesai = $periodeSelesai ?: $parsedDate->copy()->endOfMonth()->toDateString();
        }

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
                'periode_mulai' => $periodeMulai,
                'periode_selesai' => $periodeSelesai,
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

                $tanggalLembur = null;
                if (! empty($data['tanggal_lembur'])) {
                    $tanggalLembur = is_array($data['tanggal_lembur'])
                        ? $data['tanggal_lembur']
                        : json_decode($data['tanggal_lembur'], true);
                }

                \App\Models\PranotaLemburKaryawan::create([
                    'pranota_lembur_karyawan_header_id' => $pranota->id,
                    'karyawan_id' => $karyawanId,
                    'periode_mulai' => $periodeMulai,
                    'periode_selesai' => $periodeSelesai,
                    'tanggal_lembur' => $tanggalLembur,
                    'jam_lembur' => $data['kehadiran'],
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adj,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);
            }

            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pranota Lembur berhasil disimpan dengan nomor: '.$nomorPranota);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return back()->with('error', 'Gagal menyimpan Pranota Lembur: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::findOrFail($id);

        if ($pranota->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat diedit karena PUML induk sudah '.$puml->status.'.');
            }
        }

        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date',
            'karyawans' => 'required|array|min:1',
            'karyawans.*.detail_id' => 'nullable',
            'karyawans.*.karyawan_id' => 'required|integer',
            'karyawans.*.jam_lembur' => 'nullable|string',
            'karyawans.*.nominal_awal' => 'required|numeric',
            'karyawans.*.adjustment' => 'nullable|numeric',
            'karyawans.*.catatan' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $submittedDetailIds = [];
            $totalBiaya = 0;
            $totalAdjustment = 0;

            foreach ($validated['karyawans'] as $itemData) {
                $detailId = $itemData['detail_id'] ?? null;
                $nominalAwal = (float) ($itemData['nominal_awal'] ?? 0);
                $adj = (float) ($itemData['adjustment'] ?? 0);
                $totalAkhir = $nominalAwal + $adj;

                $totalBiaya += $nominalAwal;
                $totalAdjustment += $adj;

                if ($detailId) {
                    $detail = \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)
                        ->where('id', $detailId)
                        ->first();
                    if ($detail) {
                        $detail->update([
                            'nominal_awal' => $nominalAwal,
                            'adjustment' => $adj,
                            'total_akhir' => $totalAkhir,
                            'catatan' => $itemData['catatan'] ?? null,
                        ]);
                        $submittedDetailIds[] = $detail->id;
                    }
                } else {
                    $newDetail = \App\Models\PranotaLemburKaryawan::create([
                        'pranota_lembur_karyawan_header_id' => $pranota->id,
                        'karyawan_id' => $itemData['karyawan_id'],
                        'periode_mulai' => $validated['periode_mulai'] ?? $pranota->periode_mulai,
                        'periode_selesai' => $validated['periode_selesai'] ?? $pranota->periode_selesai,
                        'jam_lembur' => $itemData['jam_lembur'] ?? '0 Jam',
                        'nominal_awal' => $nominalAwal,
                        'adjustment' => $adj,
                        'total_akhir' => $totalAkhir,
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                    $submittedDetailIds[] = $newDetail->id;
                }
            }

            // Hapus karyawan yang dikeluarkan dari pranota
            if (!empty($submittedDetailIds)) {
                \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)
                    ->whereNotIn('id', $submittedDetailIds)
                    ->delete();
            }

            $totalSetelahAdjustment = $totalBiaya + $totalAdjustment;

            $pranota->update([
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'periode_mulai' => $validated['periode_mulai'] ?? $pranota->periode_mulai,
                'periode_selesai' => $validated['periode_selesai'] ?? $pranota->periode_selesai,
                'total_biaya' => $totalBiaya,
                'adjustment' => $totalAdjustment,
                'total_setelah_adjustment' => $totalSetelahAdjustment,
                'updated_by' => auth()->id(),
            ]);

            // Sync PUML jika terhubung
            if ($pranota->pranota_puml_id) {
                $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
                if ($puml) {
                    $sumLembur = \App\Models\PranotaLemburKaryawanHeader::where('pranota_puml_id', $puml->id)->sum('total_setelah_adjustment');
                    $puml->update([
                        'total_lembur' => $sumLembur,
                        'grand_total' => $puml->total_uang_makan + $sumLembur,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->input('_redirect_to') === 'show') {
                return redirect()->route('pranota-lembur-karyawan.show', $pranota->id)
                    ->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil diperbarui.');
            }

            return redirect()->back()
                ->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil diperbarui.')
                ->with('open_riwayat', true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui pranota lembur: '.$e->getMessage())
                ->with('open_riwayat', true);
        }
    }

    public function destroy($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::findOrFail($id);

        if ($pranota->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat dihapus karena PUML induk sudah '.$puml->status.'.');
            }

            if ($puml) {
                $puml->update([
                    'total_lembur' => max(0, $puml->total_lembur - $pranota->total_setelah_adjustment),
                    'grand_total' => max(0, $puml->grand_total - $pranota->total_setelah_adjustment),
                ]);
            }
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)->delete();
            $pranota->delete();

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return back()->with('error', 'Gagal menghapus pranota: '.$e->getMessage());
        }
    }
}
